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
const char* ssid = "gogogadgetnodes";       // Wifi Network Name
const char* password = "st@rw@rs";  // Wifi Password

