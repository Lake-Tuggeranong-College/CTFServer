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
  // Global variables for topic and timing

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

// Global variables for topic and timing




// REQUIRED LIBRARIES, DONT REMOVE
#include <Arduino.h>
#include <WiFi.h>
#include <PubSubClient.h>
#include "sensitiveInformation.h" //ENSURE WIFI & MQTT IS CONFIGURED CORRECTLY
#include "Adafruit_ADT7410.h"
String topicBuffer;
unsigned long lastUpdate = 0;
const unsigned long updateInterval = 5000; // Time between random number updates (5 seconds)
// MQTT client setup
WiFiClient espClient;
PubSubClient client(espClient);

// Morse code definitions
#define DOT_DURATION 200
#define DASH_DURATION 800
#define SYMBOL_PAUSE 200
#define LETTER_PAUSE 600
#define redLEDPin 13

char* morseCode[26] = {
  ".-", "-...", "-.-.", "-..", ".", "..-.", "--.", "....", "..", ".---", "-.-", ".-..", "--", "-.", "---", ".--.", "--.-", ".-.", "...", "-", "..-", "...-", ".--", "-..-", "-.--", "--.."
};

// Function to blink Morse code for a letter
char* blinkMorse(char letter) {
  int index = toupper(letter) - 'A';
  if (index < 0 || index > 25) return NULL; // Ignore non-letters
  char* code = morseCode[index];
  for (int i = 0; code[i] != '\0'; i++) {
    digitalWrite(redLEDPin, HIGH);
    if (code[i] == '.') {
      delay(DOT_DURATION);
    } else {
      delay(DASH_DURATION);
    }
    digitalWrite(redLEDPin, LOW);
    delay(SYMBOL_PAUSE);
  }
  delay(LETTER_PAUSE - SYMBOL_PAUSE); // Pause between letters
  return code;
}


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
void performActionBasedOnPayload(byte *payload, unsigned int length)
{
  // Blink the payload as Morse code
  Serial.print("Blinking Morse code for: ");
  for (int i = 0; i < length; i++) {
    Serial.print((char)payload[i]);
    blinkMorse((char)payload[i]);
  }
  Serial.println();
}


void sendDataToServer(String topic, String message)
{
  // 1. Connection Check: Only proceed if the MQTT client is connected
  // 2. Debug: Print the topic and message to the Serial Monitor
    Serial.print("Sending message to topic [");
    Serial.print(topic);
    Serial.print("]: ");
    Serial.println(message);

  if (client.connected())
  {
    // --- Logic for sending goes here ---
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
  if (now - lastUpdate > updateInterval)
  {
    lastUpdate = now; // Reset the timer
    // 2. Data: Generate a random "sensor" value between 0 and 100,000
    long randomNumber = random(0, 100001);
    // --- Next steps will go here ---
      // 3. Topic: Construct the special update topic
    // We use "updateChallenges/" so the server knows this is incoming data
    String updateTopic = "updateChallenges/" + String(mqttClient);
    
    // 4. Transmit: Use the helper function to send the data to the broker
    sendDataToServer(updateTopic, String(blinkMorse('I')));
    sendDataToServer(updateTopic, String(blinkMorse('N')));
    sendDataToServer(updateTopic, String(blinkMorse('K')));
    sendDataToServer(updateTopic, String(blinkMorse('M')));
    sendDataToServer(updateTopic, String(blinkMorse('A')));
    sendDataToServer(updateTopic, String(blinkMorse('N')));

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

  performActionBasedOnPayload(payload, length);
}

void loop()
{ // The loop function likely does not require change in the majority of circumstances.
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
  sendPeriodicUpdate(); 
  client.loop(); // Check for incoming messages and keep the connection alive
}




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

  // Connecting to MQTT Broker68
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
  pinMode(redLEDPin, OUTPUT);
}


