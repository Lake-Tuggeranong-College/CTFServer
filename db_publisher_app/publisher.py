import os
import time
import json
import mysql.connector
import paho.mqtt.client as mqtt
from datetime import datetime

# --- Configuration (Defaults from docker-compose.yml) ---
DB_HOST = os.getenv("DB_HOST", "CTF-MySQL") 
DB_NAME = os.getenv("DB_NAME", "devdb")
DB_USER = os.getenv("DB_USER", "devuser")
DB_PASS = os.getenv("DB_PASS", "devpass")
MQTT_HOST = os.getenv("MQTT_HOST", "CTF-MQTT-Broker")
MQTT_PORT = int(os.getenv("MQTT_PORT", 1883))
POLL_INTERVAL = int(os.getenv("POLL_INTERVAL_SECONDS", 5))

TOPIC_PREFIX = "challenges/" 
# File to store the list of module names from the previous run
TRACKING_FILE = "/app/published_modules.json"

# --- Mosquitto Client Setup ---
def on_connect(client, userdata, flags, rc):
    print(f"Connected to MQTT broker: {mqtt.connack_string(rc)}")

mqtt_client = mqtt.Client()
mqtt_client.on_connect = on_connect
mqtt_client.connect(MQTT_HOST, MQTT_PORT, 60)
mqtt_client.loop_start()

# --- Topic Tracking Functions ---
def load_previous_modules():
    """Loads the set of module names published in the previous run."""
    if os.path.exists(TRACKING_FILE):
        with open(TRACKING_FILE, 'r') as f:
            try:
                # Load the list of names and convert to a set for fast lookup
                return set(json.load(f))
            except json.JSONDecodeError:
                print(f"Warning: Could not decode {TRACKING_FILE}. Starting fresh.")
    return set()

def save_current_modules(current_module_names):
    """Saves the set of currently published module names."""
    with open(TRACKING_FILE, 'w') as f:
        # Save the set back as a list
        json.dump(list(current_module_names), f)

# --- Core Logic ---
def read_and_publish_data():
    
    # Load the module names published in the last successful run
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
                    continue # Skip invalid records

                # 1. Prepare topic and payload
                module_name_clean = str(module_name).strip().replace(' ', '_').replace('/', '_')
                unique_topic = f"{TOPIC_PREFIX}{module_name_clean}"
                payload = str(module_value)
                
                # 2. Publish (Update/Retain) the current value
                # We use retain=True here to ensure the topic persists on the broker
                mqtt_client.publish(unique_topic, payload, qos=1, retain=True)
                published_count += 1
                
                # 3. Track the module name for the current run
                current_module_names.add(module_name)
            
            print(f"[{datetime.now().strftime('%H:%M:%S')}] Published/Updated {published_count} individual values.")
        
        cur.close()
        conn.close()

        # --- Topic Clearing Logic ---
        # Find topics that were published last time but are missing now
        topics_to_clear = previous_module_names - current_module_names
        cleared_count = 0
        
        for module_name_to_clear in topics_to_clear:
            module_name_clean = str(module_name_to_clear).strip().replace(' ', '_').replace('/', '_')
            clear_topic = f"{TOPIC_PREFIX}{module_name_clean}"
            
            # Publish with an empty payload and retain=True to clear the retained message
            mqtt_client.publish(clear_topic, payload=None, qos=1, retain=True)
            cleared_count += 1
            
        if cleared_count > 0:
            print(f"[{datetime.now().strftime('%H:%M:%S')}] Cleared {cleared_count} old retained topics.")


    except Exception as e:
        # Exception handling remains the same for database issues
        if "Challenges' doesn't exist" in str(e):
             print(f"[{datetime.now().strftime('%H:%M:%S')}] ERROR: The 'Challenges' table is missing from the database.")
        elif "Unknown column" in str(e):
             print(f"[{datetime.now().strftime('%H:%M:%S')}] ERROR: Missing required column(s) in the 'Challenges' table.")
        else:
             print(f"[{datetime.now().strftime('%H:%M:%S')}] ERROR: Could not read database or publish to MQTT: {e}")
             
        # IMPORTANT: If the run failed, we DO NOT update the tracking file, 
        # so we will re-attempt clearing on the next successful run.
        return 


    # --- Save State ---
    # Only save the new list if the database read and publishing was successful
    save_current_modules(current_module_names)


if __name__ == "__main__":
    print("DB Publisher service started. Polling database...")
    # Give the database a moment to fully initialize
    time.sleep(10) 
    while True:
        read_and_publish_data()
        time.sleep(POLL_INTERVAL)