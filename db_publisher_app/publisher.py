import os
import time
import json
import mysql.connector
import paho.mqtt.client as mqtt
from datetime import datetime

# Environment variables from docker-compose.yml
DB_HOST = os.getenv("DB_HOST", "CTF-MySQL") 
DB_NAME = os.getenv("DB_NAME", "devdb")
DB_USER = os.getenv("DB_USER", "devuser")
DB_PASS = os.getenv("DB_PASS", "devpass")
MQTT_HOST = os.getenv("MQTT_HOST", "CTF-MQTT-Broker")
MQTT_PORT = int(os.getenv("MQTT_PORT", 1883))
POLL_INTERVAL = int(os.getenv("POLL_INTERVAL_SECONDS", 5))

# Base Topic is now implicit in the moduleName itself, but we can define a prefix
# to organize the topics better. Let's use 'challenges/' as a prefix.
TOPIC_PREFIX = "challenges/" 

# --- Mosquitto Client Setup ---
def on_connect(client, userdata, flags, rc):
    print(f"Connected to MQTT broker: {mqtt.connack_string(rc)}")

mqtt_client = mqtt.Client()
mqtt_client.on_connect = on_connect
mqtt_client.connect(MQTT_HOST, MQTT_PORT, 60)
mqtt_client.loop_start()

# --- Main Logic ---
def read_and_publish_data():
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASS
        )
        # Use dictionary=True for clean access to column names
        cur = conn.cursor(dictionary=True)

        # SQL Query: Select moduleName and moduleValue
        query = "SELECT moduleName, moduleValue FROM Challenges;"
        cur.execute(query)
        
        results = cur.fetchall()
        
        if results:
            published_count = 0
            
            for record in results:
                
                # Ensure both fields exist before attempting to publish
                if not record.get('moduleName') or record.get('moduleValue') is None:
                    print(f"[{datetime.now().strftime('%H:%M:%S')}] Skipping record with missing moduleName or moduleValue.")
                    continue
                
                # 1. Construct the entire topic using the ModuleName
                # Example topic: challenges/Web_Exploit_101
                # Sanitizing the moduleName for a valid topic string
                module_name_clean = str(record['moduleName']).strip().replace(' ', '_').replace('/', '_')
                unique_topic = f"{TOPIC_PREFIX}{module_name_clean}"
                
                # 2. Get the payload: ONLY the Module Value, converted to a string
                payload = str(record['moduleValue'])
                
                # 3. Publish the individual record
                mqtt_client.publish(unique_topic, payload, qos=1, retain=False)
                published_count += 1
                
            print(f"[{datetime.now().strftime('%H:%M:%S')}] Published {published_count} individual values (e.g., to {unique_topic}).")
        else:
            print(f"[{datetime.now().strftime('%H:%M:%S')}] No data found in Challenges table.")
            
        cur.close()
        conn.close()

    except Exception as e:
        # Improved error handling for common database issues
        if "Challenges' doesn't exist" in str(e):
             print(f"[{datetime.now().strftime('%H:%M:%S')}] ERROR: The 'Challenges' table is missing from the database.")
        elif "Unknown column" in str(e):
             print(f"[{datetime.now().strftime('%H:%M:%S')}] ERROR: Missing required column(s) in the 'Challenges' table.")
        else:
             print(f"[{datetime.now().strftime('%H:%M:%S')}] ERROR: Could not read database or publish to MQTT: {e}")

if __name__ == "__main__":
    print("DB Publisher service started. Polling database...")
    time.sleep(10) 
    while True:
        read_and_publish_data()
        time.sleep(POLL_INTERVAL)