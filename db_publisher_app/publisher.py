import os
import time
import json
import mysql.connector
import paho.mqtt.client as mqtt
from datetime import datetime
import logging 

# --- Logging Setup ---
logging.basicConfig(
    level=logging.DEBUG, 
    format='%(asctime)s - %(levelname)s - %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S'
)
logger = logging.getLogger(__name__)

# --- Configuration ---
DB_HOST = os.getenv("DB_HOST", "CTF-MySQL") 
DB_NAME = os.getenv("DB_NAME", "CyberCity")
DB_USER = os.getenv("DB_USER", "CyberCity")
DB_PASS = os.getenv("DB_PASS", "Cyb3rC1ty")
MQTT_HOST = os.getenv("MQTT_HOST", "CTF-MQTT-Broker")
MQTT_PORT = int(os.getenv("MQTT_PORT", 1883))
POLL_INTERVAL = int(os.getenv("POLL_INTERVAL_SECONDS", 5))

TOPIC_PREFIX = "challenges/" 
TRACKING_FILE = "/app/published_modules.json"

# --- Database Logging Function ---
def log_event_to_db(event_text, username="esp32"):
    """Logs an entry into the eventLog table."""
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASS
        )
        cur = conn.cursor()
        query = "INSERT INTO eventLog (userName, eventText) VALUES (%s, %s);"
        cur.execute(query, (username, event_text))
        conn.commit()
        logger.info(f"Event logged to eventLog table for user '{username}': {event_text}")
        cur.close()
        conn.close()
    except Exception as e:
        logger.error(f"Failed to log event to database: {e}")

# --- Database Update Function ---
def update_database_from_mqtt(module_name, new_value):
    """
    Updates the moduleValue in the Challenges table.
    Returns True if a row was updated, False otherwise.
    """
    updated = False
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASS
        )
        cur = conn.cursor()
        
        query = "UPDATE Challenges SET moduleValue = %s WHERE moduleName = %s;"
        cur.execute(query, (new_value, module_name))
        conn.commit()
        
        if cur.rowcount > 0:
            logger.info(f"Challenges table updated: {module_name} set to '{new_value}'")
            updated = True
        else:
            logger.debug(f"No match in Challenges table for: {module_name}")
            updated = False
            
        cur.close()
        conn.close()
    except Exception as e:
        logger.error(f"Failed to update Challenges table from MQTT: {e}")
    return updated

# --- Mosquitto Client Setup ---
def on_connect(client, userdata, flags, rc):
    logger.info(f"Connected to MQTT broker: {mqtt.connack_string(rc)}")
    client.subscribe(f"{TOPIC_PREFIX}#")
    logger.info(f"Subscribed to {TOPIC_PREFIX}# for incoming data.")

def on_message(client, userdata, msg):
    """Triggered when the ESP32 publishes data back to the server."""
    try:
        topic = msg.topic
        payload = msg.payload.decode('utf-8')
        logger.debug(f"Received MQTT message: Topic={topic}, Payload={payload}")
        if topic.startswith(TOPIC_PREFIX):
            module_name = topic[len(TOPIC_PREFIX):]
            logger.info(f"Incoming MQTT message: Topic={topic}, Payload={payload}")
            
            # 1. Attempt to update Challenges table
            success = update_database_from_mqtt(module_name, payload)
            
            # 2. If no match was found in Challenges, log to eventLog instead
            if not success:
                log_text = f"Unmatched topic '{topic}' received with payload: {payload}"
                log_event_to_db(log_text, username="esp32")
                
    except Exception as e:
        logger.error(f"Error processing incoming MQTT message: {e}")

logger.info(f"Connecting to MQTT broker... {MQTT_HOST}:{MQTT_PORT}")
mqtt_client = mqtt.Client(callback_api_version=mqtt.CallbackAPIVersion.VERSION1)
mqtt_client.on_connect = on_connect
mqtt_client.on_message = on_message 
mqtt_client.connect(MQTT_HOST, MQTT_PORT, 60)
mqtt_client.loop_start()

# --- Topic Tracking Functions ---
def load_previous_modules():
    if os.path.exists(TRACKING_FILE):
        with open(TRACKING_FILE, 'r') as f:
            try:
                return set(json.load(f))
            except json.JSONDecodeError:
                logger.warning(f"Could not decode {TRACKING_FILE}. Starting fresh.")
    return set()

def save_current_modules(current_module_names):
    with open(TRACKING_FILE, 'w') as f:
        json.dump(list(current_module_names), f)
    logger.debug(f"Saved {len(current_module_names)} current modules to {TRACKING_FILE}.")

# --- Core Logic ---
def read_and_publish_data():
    previous_module_names = load_previous_modules()
    current_module_names = set()

    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASS
        )
        cur = conn.cursor(dictionary=True)

        query = "SELECT moduleName, moduleValue FROM Challenges;"
        cur.execute(query)
        results = cur.fetchall()
        
        published_count = 0
        
        if results:
            for record in results:
                module_name = record.get('moduleName')
                module_value = record.get('moduleValue')

                if not module_name or module_value is None:
                    continue 

                module_name_clean = str(module_name).strip().replace(' ', '_').replace('/', '_')
                unique_topic = f"{TOPIC_PREFIX}{module_name_clean}"
                payload = str(module_value)
                
                mqtt_client.publish(unique_topic, payload, qos=1, retain=True)
                published_count += 1
                current_module_names.add(module_name)
            
            logger.info(f"Polled DB: Published/Updated {published_count} values to MQTT.")
        
        cur.close()
        conn.close()

        # Topic Clearing Logic
        topics_to_clear = previous_module_names - current_module_names
        for module_name_to_clear in topics_to_clear:
            module_name_clean = str(module_name_to_clear).strip().replace(' ', '_').replace('/', '_')
            clear_topic = f"{TOPIC_PREFIX}{module_name_clean}"
            mqtt_client.publish(clear_topic, payload=None, qos=1, retain=True)
            logger.debug(f"Cleared retained topic: {clear_topic}")

    except Exception as e:
        logger.error(f"Poll Cycle Error: {e}") 
        return 

    save_current_modules(current_module_names)

if __name__ == "__main__":
    logger.info("DB Sync service started. bidirectional MQTT <-> DB active with Event Logging.")
    time.sleep(10) 
    while True:
        read_and_publish_data()
        time.sleep(POLL_INTERVAL)