/*
  This is a template for all ESP32's using MQTT within the CyberRange.
  Read through the steps and ensure everything is setup correctly.
  This is made such that programming physical functions is as easy as possible.
  Ensure to only change what is asked, and not to remove any required libraries.
*/

// REQUIRED LIBRARIES, DONT REMOVE
#include <Arduino.h>
#include "comms.h"

// ADD Libraries Required for specific module.
#include <Adafruit_GFX.h>
#include "Adafruit_LEDBackpack.h"

// Hardware setup
Adafruit_8x16minimatrix matrix = Adafruit_8x16minimatrix();



/*
  Communication Channels

  There are three channels of communicating information from the ESP32 to the MQTT Broker. 
  All three channels are taken from the MQTT broker and stored in the database.

  `sendDataToServer()` is used for all three channels. It requires two arguments - `topic` and `message`.

  Each topic has a keyword, followed by the `mqttClient`, defined in `comms.h`. 
  For example: "updateChallenges/Windmill".

  `message` is the data to be sent, as a String.

  Channel Keywords:

  // Update the `moduleValue` field in the database by sending to the broker
  // This can be used to 'reset' the challenge for the next user.
  // For instance, after a set amount of time, you could reset the moduleValue to 0, which would make the challenge unsolved again until a user solves it and updates the moduleValue to 1.
  sendDataToServer("updateChallenges/" + String(mqttClient), String(randomNumber));

  // Upload data for the module, which is attached to the challenge. The data shows on the challenge page on the website.
  // The data can be anything. It could be data used to assist the user with the challenge, or it could be fake data meant to mislead the user. It's up to you to decide how to use it!
  // When the module data is entered into the database, it will be timestamped.
  sendDataToServer("moduleData/" + String(mqttClient), String(randomNumber));

  // Upload event logs for the module. Use this field for Debugging.
  // Log events such as : Module startup, module restarted, resetting the challenge data etc.
  // When the log is entered into the database, it will be timestamped.
  sendDataToServer("eventLog/" + String(mqttClient), String(randomNumber));

*/





/*
This function is executed/called when new data has been received from the MQTT broker.
Customise this function to perform action that is required for this module.
For example: If the payload is '1', then turn the motor on. If the payload is '0', then turn the motor off.

@params payload: String. The data received from the MQTT broker.
@return: null
*/
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

void setup()
{
  // Seed random number generator using noise from an analog pin
  randomSeed(analogRead(0));
  Serial.begin(9600);
  wifiSetup();
  mqttSetup();

  while (!Serial)
  {
    delay(10);
  }
  delay(1000);

  matrix.begin(0x70);
}

void loop()
{
  // 1. Handle Connection Persistence
  mqttConnect(); // Ensure we are connected to the MQTT broker. If not, this will attempt to reconnect.

  // 2. Generate and send a random number periodically
  unsigned long now = millis();
  if (now - lastUpdate > updateInterval)
  {
    lastUpdate = now;

    // TODO: Upload data to server as required.
    
  }

  client.loop(); // Check for incoming messages and keep the connection alive
}