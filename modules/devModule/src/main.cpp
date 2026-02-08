/*
  This is a template for all ESP32's using MQTT within the CyberRange.
  Read through the steps and ensure everything is setup correctly.
  This is made such that programming physical functions is as easy as possible.
  Ensure to only change what is asked, and not to remove any required libraries.
*/

// REQUIRED LIBRARIES, DONT REMOVE
#include <Arduino.h>
#include <WiFi.h>
#include <PubSubClient.h>
#include "sensitiveInformation.h" // ENSURE WIFI & MQTT IS CONFIGURED CORRECTLY

#include <Adafruit_GFX.h>
#include "Adafruit_LEDBackpack.h"

// Hardware setup
Adafruit_8x16minimatrix matrix = Adafruit_8x16minimatrix();

// Global variables for topic and timing
String topicBuffer;
unsigned long lastUpdate = 0;
const unsigned long updateInterval = 5000; // Time between random number updates (5 seconds)

// MQTT client setup
WiFiClient espClient;
PubSubClient client(espClient);

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

void performActionBasedOnPayload(String payload)
{
  Serial.print("Displaying message on matrix: ");
  Serial.println(payload);

  matrix.setRotation(1);
  matrix.clear();
  matrix.setCursor(0, 0);
  matrix.print(payload);
  matrix.writeDisplay();
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

void setup()
{
  // Seed random number generator using noise from an analog pin
  randomSeed(analogRead(0));

  // Construct the MQTT topic dynamically
  topicBuffer = "challenges/" + String(mqttClient);
  mqttTopic = topicBuffer.c_str();

  Serial.begin(9600);
  while (!Serial)
  {
    delay(10);
  }
  delay(1000);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(1000);
    Serial.println("Connecting to WiFi..");
  }
  Serial.println();
  Serial.print("Connected to WiFI. IP address: ");
  Serial.println(WiFi.localIP());

  client.setServer(mqttServer, mqttPort);
  client.setCallback(callback);

  // Connecting to MQTT Broker
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

  matrix.begin(0x70);
}

void loop()
{ 
  // 1. Handle Connection Persistence
  if (!client.connected())
  {
    while (!client.connected())
    {
      Serial.println("Reconnecting to MQTT...");
      if (client.connect(mqttClient))
      {
        Serial.println("Reconnected to MQTT");
        client.subscribe(mqttTopic);
      }
      else
      {
        Serial.print("Failed to reconnect, state ");
        Serial.print(client.state());
        delay(2000);
      }
    }
  }

  // 2. Generate and send a random number periodically
  unsigned long now = millis();
  if (now - lastUpdate > updateInterval)
  {
    lastUpdate = now;

    // Generate random number between 0 and 100,000
    long randomNumber = random(0, 100001);
    
    // Construct the update topic (e.g., updateChallenges/Windmill)
    String updateTopic = "updateChallenges/" + String(mqttClient);
    
    // Send the random number to the server
    sendDataToServer(updateTopic, String(randomNumber));
    
    // Optional: Log to ModuleData
    // sendDataToServer("ModuleData", String(mqttClient) + "," + String(randomNumber));
  }

  client.loop(); // Check for incoming messages and keep the connection alive
}