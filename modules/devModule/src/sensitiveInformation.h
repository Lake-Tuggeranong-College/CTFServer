/*
 * Contains any sensitive Infomration that you do not want published to Github.
 * 
 * The SSID and Password variables will need to be changed if you’re connecting to another Wireless Access Point (such as at home).
 *
 * This file is supposed to be in the .gitignore
 * 
 * mqttClient NEEDS to match the 'ModuleName' column of the database row for this ESP32, and mqttTopic NEEDS to match the 'Module' column of the database row for this ESP32.
 * 
 * For instance, if mqttClient is "Windmill"
 * then mqttTopic should be "challenges/Windmill"
 */


// Wifi network
const char* ssid = "CyberRange";       // Wifi Network Name
const char* password = "CyberRange";  // Wifi Password

// MQTT client name
const char* mqttClient = "Windmill"; // This should be unique for each ESP32, e.g: "ESP32_Servo", "ESP32_Piezo", etc

// MQTT Topic
// const char* mqttTopic = "challenges/Windmill"; // It's worth noting that an ESP32 can subscribe to more than 1 topic
const char* mqttTopic; 


// Replace with the MQTT broker IP address and port (default port for MQTT is 1883)
const char* mqttServer = "192.168.1.10";  
const int mqttPort = 1883;
