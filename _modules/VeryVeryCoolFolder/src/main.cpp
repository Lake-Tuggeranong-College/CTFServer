/*
  This is a template for all ESP32's using MQTT within the CyberRange.
  Read through the steps and ensure everything is setup correctly.
  This is made such that programming physical functions is as easy as possible.
  Ensure to only change what is asked, and not to remove any required libraries.
*/

/*
  This works using the MQTT broker (mosquitto, installed on the CyberRange server), in combination with the database,
  and the databaseToMQTT.py script, setup as a service on the CyberRange server. The script detects any changes to the
  'challenges/CurrentOutput' column, and sends them to the broker, using the information from the row of the change
  in order to send it to the associated topic (challenges/Module).

  This means that, if you register a module on the website with the 'Module' set as 'AnnoyingPeizo'. And write '4' to the
  'CurrentOutput' of the 'AnnoyingPeizo' row, the script will send to the topic:

  'challenges/AnnoyingPeizo'

  The message:

  '4'

  That message is sent to the ESP32 subscribed to that topic.

  that topic, is what needs to be put into the 'mqttTopic' const char* inside of sensitiveInformation.h, in order to associate
  the ESP32 with its respective database row within challenges.

  Example inside of sensitveInformation.h:

  const char* mqttTopic = "challenges/Servo";

  STEP 0.
  ENSURE THE sensitiveInformation.h FILE IS CONFIGURED CORRECTLY.
  OPEN THE sensitiveInformation.h FILE AND ENSURE THE FOLLOWING VARIABLES ARE CORRECT:
  - mqttClient (Should be unique for each ESP32, e.g: "ESP32_Servo", "ESP32_Piezo", etc)
  - mqttTopic  (Should match the 'ModuleName' column of the database row for this ESP32)
  - mqttServer (Should be the IP address of the DEV or PROD server.)
*/

// REQUIRED LIBRARIES, DONT REMOVE
#include <Arduino.h>
#include <WiFi.h>
#include <PubSubClient.h>
#include "sensitiveInformation.h" //ENSURE WIFI & MQTT IS CONFIGURED CORRECTLY
#include "Adafruit_ADT7410.h"

// ANY MISSING LIBRARIES SHOULD BE ADDED TO THIS PLATFORMIO PROJECT USING: PLATFORMIO HOME > LIBRARIES

// Follow the steps:

/*
  STEP 1.
  DECLARE REQUIRED LIBRARIES, e.g:

  #include <ESP32Servo.h> // For servos.

  Do it below this comment
*/

/*
  STEP 2.
  DECLARE REQUIRED PINS, e.g:

  #declare redLEDPin 17

  OR

  int redLEDPin = 17; // Red LED pin.

  Do it below this comment
*/

#define redLEDPin 13

// Global variables for topic and timing
String topicBuffer;
unsigned long lastUpdate = 0;
const unsigned long updateInterval = 5000; // Time between random number updates (5 seconds)

/*
  STEP 2.1.
  SET pinMode() FOR DECLARED PINS IN setup() OR callback() FUNCTION.
  setup() is probably better, but callback() should work too.

  Go to the setup() function for additional instructions (Examples).
*/

/*
  STEP 3.
  PROGRAM THE callback() FUNCTION TO USE THE WIRED UP COMPONENTS AS DESIRED.

  callback() is below.
*/


void performActionBasedOnPayload(byte *payload)
{
  // Implement your action logic here based on the payload
  // For example, if the payload represents a number, you could convert it and use it to control a motor speed
  // Add your action code here

  /*
  Example: turn on/off an LED based on the message received (this is specialised, if you dont need it dont use it.)

  if ((char)payload[0] == '1') {
    Serial.println("LED ON");
    digitalWrite(redLEDPin, HIGH);
  } else {
    Serial.println("LED OFF");
    digitalWrite(redLEDPin, LOW);
  }

  Example: turn on/off an LED based on ANY message received (this is how this is intended to work, activating when this ESP32's respective
  challenge is completed)

  if ((char)payload[0]) {
    Serial.println("LED ON");
    digitalWrite(redLEDPin, HIGH);
    delay(250);
    Serial.println("LED OFF");
    digitalWrite(redLEDPin, LOW);
  }
  */
  Serial.print("Payload:");
  Serial.println((char)payload[0]);

  if ((char)payload[0] == '1') {
    Serial.println("LED ON");
    digitalWrite(redLEDPin, HIGH);
  } else {
    Serial.println("LED OFF");
    digitalWrite(redLEDPin, LOW);
  }
}


void callback(char *topic, byte *payload, unsigned int length)
{
  Serial.print("Message arrived [");
  Serial.print(topic);
  Serial.print("] ");
  for (int i = 0; i < length; i++)
  {
    Serial.print((char)payload[i]);
  }
  Serial.println();

  performActionBasedOnPayload(payload);
}


// Declare the callback function prototype before setup()
//void callback(char *topic, byte *payload, unsigned int length);

// MQTT client setup
WiFiClient espClient;
PubSubClient client(espClient);

void setup()
{
  /*
    STEP 3. CONTINUED.
    DECLARE YOUR pinMode()'s below, e.g:

    pinMode(redLEDPin, OUTPUT);
  */

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
  Serial.print("Connected to WiFI");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  // Setting up MQTT
  client.setServer(mqttServer, mqttPort);
  client.setCallback(callback); // Set the callback function to handle incoming messages

  // Connecting to MQTT Broker
  while (!client.connected())
  {
    Serial.println("Connecting to MQTT...");
    if (client.connect(mqttClient))
    {
      Serial.println("Connected to MQTT");
      client.subscribe(mqttTopic); // Subscribe to the control topic
      Serial.println("Connected to topic");
    }
    else
    {
      Serial.print("Failed with state ");
      Serial.print(client.state());
      delay(2000);
    }
  }
  pinMode(redLEDPin, OUTPUT); // Example pinMode for the red LED pin, change as needed
}


void sendDataToServer(String topic, String message)
{
  // 1. Connection Check: Only proceed if the MQTT client is connected
  if (client.connected())
  {
    // 2. Debug: Print the topic and message to the Serial Monitor
    Serial.print("Sending message to topic [");
    Serial.print(topic);
    Serial.print("]: ");
    Serial.println(message);

    // 3. Publishing: Convert Strings to C-strings and send to the broker
    client.publish(topic.c_str(), message.c_str());
  }
  else
  {
    Serial.println("Send failed: MQTT not connected.");
  }
}


void sendPeriodicUpdate()
{
  // 1. Timer: Check if 5 seconds (updateInterval) have passed since the last update
  unsigned long now = millis();
  /*if (now - lastUpdate > updateInterval)
  {
    lastUpdate = now; // Reset the timer
    
    // 2. Data: Generate a random "sensor" value between 0 and 1
    int randomNumber = random(0, 1);
    
    // 3. Topic: Construct the special update topic
    // We use "updateChallenges/" so the server knows this is incoming data
    String updateTopic = "updateChallenges/" + String(mqttClient);
    
    // 4. Transmit: Use the helper function to send the data to the broker
    sendDataToServer(updateTopic, String(randomNumber));

  }*/
}


void loop()
{ // The loop function likely does not require change in the majority of circumstances.
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
        Serial.println("Connected to topic");
      }
      else
      {
        Serial.print("Failed to reconnect, state ");
        Serial.print(client.state());
        delay(2000);
      }
    }
  }

  // 2. Handle periodic data transmission
  // We call our function here so it checks the timer every single loop
  sendPeriodicUpdate();

  client.loop(); // Check for incoming messages and keep the connection alive
}





