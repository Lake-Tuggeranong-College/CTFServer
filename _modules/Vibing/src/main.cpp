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
#define DOT_DURATION 400
#define DASH_DURATION 1200
#define SYMBOL_PAUSE 500
#define LETTER_PAUSE 1500
#define redLEDPin 16
#define yellowLEDPin 17
#define greenLEDPin 15

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
   digitalWrite(yellowLEDPin, HIGH);
   digitalWrite(greenLEDPin, HIGH);
   if (code[i] == '.') {
     delay(DOT_DURATION);
   } else {
     delay(DASH_DURATION);
   }
   digitalWrite(redLEDPin, LOW);
   digitalWrite(yellowLEDPin, LOW);
   digitalWrite(greenLEDPin, LOW);
   delay(SYMBOL_PAUSE);
 }
 delay(LETTER_PAUSE - SYMBOL_PAUSE); // Pause between letters
 return code;
}


void performActionBasedOnPayload(byte *payload, unsigned int length)
{
 // Blink the payload as Morse code
 Serial.print("Blinking Morse code for: ");

 for (int i = 0; i < length; i++) {
   Serial.print((char)payload[i]);
   blinkMorse((char)payload[i]);

 }
 Serial.println();  
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
if ((char)payload[0] == '1') {
    Serial.println("");
    digitalWrite(redLEDPin, HIGH);
  } 
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
  // sendDataToServer(updateTopic, String(blinkMorse('I')));
  // sendDataToServer(updateTopic, String(blinkMorse('N')));
  // sendDataToServer(updateTopic, String(blinkMorse('K')));
  // sendDataToServer(updateTopic, String(blinkMorse('M')));
  // sendDataToServer(updateTopic, String(blinkMorse('A')));
  // sendDataToServer(updateTopic, String(blinkMorse('N')));
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
 pinMode(yellowLEDPin, OUTPUT);
 pinMode(greenLEDPin, OUTPUT);
}

// END