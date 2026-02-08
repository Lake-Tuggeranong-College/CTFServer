import os
import time
import json
import mysql.connector
import paho.mqtt.client as mqtt
from datetime import datetime
import logging 
import queue # Added for thread-safe message handling

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

# Thread-safe queue to store incoming MQTT messages
msg_queue = queue.Queue()

# --- Database Functions ---

def log_event_to_db(event_text, username="esp32"):
    """Logs an entry into the eventLog table with a timestamp."""
    try:
        conn = mysql.connector.connect(host=DB_HOST, database=DB_NAME, user=DB_USER, password=DB_PASS)
        cur = conn.cursor()
        current_time = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        query = "INSERT INTO eventLog (username, eventText, datePosted) VALUES (%s, %s, %s);"
        cur.execute(query, (username, event_text, current_time))
        conn.commit()
        logger.info(f"Event logged to eventLog: {event_text}")
        cur.close()
        conn.close()
    except Exception as e:
        logger.error(f"Failed to log event: {e}")

def log_module_data_to_db(module_name, data_text):
    """
    Finds ModuleID from Challenges table and logs data to ModuleData table.
    """
    try:
        conn = mysql.connector.connect(host=DB_HOST, database=DB_NAME, user=DB_USER, password=DB_PASS)
        cur = conn.cursor(dictionary=True)
        logger.debug(f"Attempting to log ModuleData for module '{module_name}' with data: {data_text}") 

        # 1. Find the ID for this module name
        cur.execute("SELECT ID FROM Challenges WHERE moduleName = %s LIMIT 1;", (module_name,))
        result = cur.fetchone()
        if result:
            module_id = result['ID']
            current_time = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            
            # 2. Insert into ModuleData
            insert_query = "INSERT INTO ModuleData (ModuleID, DateTime, Data) VALUES (%s, %s, %s);"
            cur.execute(insert_query, (module_id, current_time, data_text))
            conn.commit()
            logger.info(f"ModuleData logged: ModuleID {module_id}, Data: {data_text}")
        else:
            logger.warning(f"Could not log ModuleData: No module found named '{module_name}'")

        cur.close()
        conn.close()
    except Exception as e:
        logger.error(f"Failed to log ModuleData: {e}")

def update_database_from_mqtt(module_name, new_value):
    """Updates Challenges table. Returns True if match found."""
    updated = False
    try:
        conn = mysql.connector.connect(host=DB_HOST, database=DB_NAME, user=DB_USER, password=DB_PASS)
        cur = conn.cursor()
        query = "UPDATE Challenges SET moduleValue = %s WHERE moduleName = %s;"
        cur.execute(query, (new_value, module_name))
        conn.commit()
        updated = (cur.rowcount > 0)
        cur.close()
        conn.close()
    except Exception as e:
        logger.error(f"Failed to update Challenges: {e}")
    return updated

# --- Mosquitto Client Setup ---

def on_connect(client, userdata, flags, rc):
    if rc == 0:
        logger.info("Connected to MQTT broker successfully.")
        # Subscribe to both the prefixed topics and the top-level special topics
        client.subscribe(f"{TOPIC_PREFIX}#", qos=1)
        client.subscribe("EventLog", qos=1)
        client.subscribe("ModuleData", qos=1)
        logger.info("Subscribed to challenges/#, EventLog, and ModuleData")
    else:
        logger.error(f"Connection failed with code {rc}")

def on_message(client, userdata, msg):
    """Callback triggered on message arrival."""
    try:
        if msg.retain:
            logger.debug(f"Ignoring retained message on {msg.topic}")
            return

        payload = msg.payload.decode('utf-8')
        # logger.debug(f"Live message received on {msg.topic}")
        msg_queue.put((msg.topic, payload))
    except Exception as e:
        logger.error(f"Error in on_message callback: {e}")

def process_incoming_messages():
    """Processes messages based on topic logic."""
    while not msg_queue.empty():
        topic, payload = msg_queue.get()
        
        # 1. Handle Top-Level special topics (No prefix)
        if topic == "EventLog":
            log_event_to_db(payload)
        
        elif topic == "ModuleData":
            if "," in payload:
                m_name, m_data = payload.split(",", 1)
                log_module_data_to_db(m_name.strip(), m_data.strip())
            else:
                logger.debug(f"ModuleData received with invalid format: {payload}")
        
        # 2. Handle Prefixed Challenge topics
        elif topic.startswith(TOPIC_PREFIX):
            sub_topic = topic[len(TOPIC_PREFIX):]
            logger.debug(f"Handling challenge sub_topic: {sub_topic}")
            
            # Standard challenge update for non-retained messages
            success = update_database_from_mqtt(sub_topic, payload)
            if not success:
                logger.debug(f"Ignoring message on unmatched challenge topic: {topic}")
                    
        msg_queue.task_done()

# Initialize MQTT Client
mqtt_client = mqtt.Client(callback_api_version=mqtt.CallbackAPIVersion.VERSION1)
mqtt_client.on_connect = on_connect
mqtt_client.on_message = on_message 
mqtt_client.connect(MQTT_HOST, MQTT_PORT, 60)
mqtt_client.loop_start()

# --- Topic Tracking & DB Polling ---

def load_previous_modules():
    if os.path.exists(TRACKING_FILE):
        with open(TRACKING_FILE, 'r') as f:
            try: return set(json.load(f))
            except: pass
    return set()

def save_current_modules(current_module_names):
    with open(TRACKING_FILE, 'w') as f:
        json.dump(list(current_module_names), f)

def read_and_publish_data():
    previous_module_names = load_previous_modules()
    current_module_names = set()
    try:
        conn = mysql.connector.connect(host=DB_HOST, database=DB_NAME, user=DB_USER, password=DB_PASS)
        cur = conn.cursor(dictionary=True)
        cur.execute("SELECT moduleName, moduleValue FROM Challenges;")
        results = cur.fetchall()
        for record in results:
            m_name = record.get('moduleName')
            m_val = record.get('moduleValue')
            if m_name and m_val is not None:
                clean_name = str(m_name).strip().replace(' ', '_').replace('/', '_')
                mqtt_client.publish(f"{TOPIC_PREFIX}{clean_name}", str(m_val), qos=1, retain=True)
                current_module_names.add(m_name)
        cur.close()
        conn.close()

        # Clear old topics
        for old_name in (previous_module_names - current_module_names):
            clean_old = str(old_name).strip().replace(' ', '_').replace('/', '_')
            mqtt_client.publish(f"{TOPIC_PREFIX}{clean_old}", payload=None, qos=1, retain=True)
    except Exception as e:
        logger.error(f"Polling error: {e}")
    save_current_modules(current_module_names)

if __name__ == "__main__":
    logger.info("Service starting with Strict Routing and No-Prefix Special Topics...")
    time.sleep(5) 
    while True:
        process_incoming_messages()
        read_and_publish_data()
        time.sleep(POLL_INTERVAL)