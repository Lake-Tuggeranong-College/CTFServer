/*
 * Contains any sensitive Infomration that you do not want published to Github.
 * 
 * The SSID and Password variables will need to be changed if you’re connecting to another Wireless Access Point (such as at home).
 *
 * This file is supposed to be in the .gitignore
 * 
 */


// Wifi network
const char* ssid = "gogogadgetnodes";       // Wifi Network Name
const char* password = "st@rw@rs";  // Wifi Password

// MQTT client name
const char* mqttClient = "ESP32";

// MQTT Topic
const char* mqttTopic = "challenges/Windmill"; // It's worth noting that an ESP32 can subscribe to more than 1 topic

// Replace with the MQTT broker IP address and port (default port for MQTT is 1883)
const char* mqttServer = "192.168.68.105";  
const int mqttPort = 1883;
