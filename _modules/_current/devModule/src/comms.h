/*
This file contains the communication logic for the development module, including MQTT setup, message handling, and periodic updates.

DO NO CHANGE ANYTHING IN THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING, AS THIS FILE CONTAINS THE CORE COMMUNICATION LOGIC FOR THE MODULE.

*/

#include <WiFi.h>
#include <PubSubClient.h>
#include "sensitiveInformation.h" // ENSURE WIFI & MQTT IS CONFIGURED CORRECTLY

// MQTT client setup
WiFiClient espClient;
PubSubClient client(espClient);
String topicBuffer;


// Replace with the MQTT broker IP address and port (default port for MQTT is 1883)
const char* mqttServer = "192.168.68.104";  
const int mqttPort = 1883;


unsigned long lastUpdate = 0;
const unsigned long updateInterval = 5000; // Time between random number updates (5 seconds)

void performActionBasedOnPayload(String payload);

void wifiSetup()
{

    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED)
    {
        delay(1000);
        Serial.println("Connecting to WiFi..");
    }
    Serial.println();
    Serial.print("Connected to WiFI. IP address: ");
    Serial.println(WiFi.localIP());
}



/*
  Use this to send data back to the MQTT broker.
  Example usage: sendDataToServer("challenges/Status", "Task Completed");
*/
void sendDataToServer(String topic, String message)
{
    if (client.connected())
    {
        Serial.print("Sending message to topic [");
        Serial.print(topic);
        Serial.print("]: ");
        Serial.println(message);

        // Convert String to char array for the PubSubClient library
        client.publish(topic.c_str(), message.c_str());
    }
    else
    {
        Serial.println("Send failed: MQTT not connected.");
    }
}

void sendPeriodicUpdate(String topic, String dataToSend)
{
    // 1. Timer: Check if 5 seconds (updateInterval) have passed since the last update
    unsigned long now = millis();
    if (now - lastUpdate > updateInterval)
    {
        lastUpdate = now; // Reset the timer

        // --- Next steps will go here ---

        // 3. Topic: Construct the special update topic
        // We use "updateChallenges/" so the server knows this is incoming data
        String updateTopic = topic + "/" + String(mqttClient);

        // 4. Transmit: Use the helper function to send the data to the broker
        sendDataToServer(updateTopic, dataToSend);
    }
}

void callback(char *topic, byte *payload, unsigned int length)
{
    String message = "";
    for (int i = 0; i < length; i++)
    {
        message += (char)payload[i];
    }

    String internalPrefix = "__INTERNAL__";
    if (message.startsWith(internalPrefix))
    {
        message = message.substring(internalPrefix.length());
    }

    Serial.print("Message arrived [");
    Serial.print(topic);
    Serial.print("] ");
    Serial.println(message);

    performActionBasedOnPayload(message);
}


void mqttConnect()
{
    while (!client.connected())
    {
        Serial.println("Connecting to MQTT...");
        if (client.connect(mqttClient))
        {
            Serial.println("Connected to MQTT");
            client.subscribe(mqttTopic);
            sendDataToServer("EventLog", String(mqttClient) + " is online.");
        }
        else
        {
            Serial.print("Failed with state ");
            Serial.print(client.state());
            delay(2000);
        }
    }
}

void mqttSetup()
{
    // Construct the MQTT topic dynamically
    topicBuffer = "challenges/" + String(mqttClient);
    mqttTopic = topicBuffer.c_str();

    client.setServer(mqttServer, mqttPort);
    client.setCallback(callback);
    mqttConnect();
}

