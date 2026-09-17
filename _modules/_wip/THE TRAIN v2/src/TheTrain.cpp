#include <PowerFunctions.h>   // Power Functions Library
#include <Arduino.h>                          // Core Arduino functions
#include <Adafruit_GFX.h>                     // Core graphics library
#include <Adafruit_ST7789.h>                 // Hardware-specific library for ST7789 display

// -------------------------------------------------------------------
// MODE SWITCH: Set to true for offline IR testing. Set to false for WiFi/MQTT.
#define DEBUG_MODE true
// -------------------------------------------------------------------

// Pin definitions for Adafruit ESP32-S3 Reverse TFT Feather
#ifndef TFT_BACKLIGHT
  #define TFT_BACKLIGHT  45
#endif
#ifndef TFT_I2C_POWER  
  #define TFT_I2C_POWER  21
#endif
#ifndef TFT_CS
  #define TFT_CS         42
#endif
#ifndef TFT_DC
  #define TFT_DC         40
#endif
#ifndef TFT_RST
  #define TFT_RST        41
#endif

#if !DEBUG_MODE
  #include <WiFi.h>                             // WiFi connectivity library
  #include <PubSubClient.h>                     // MQTT client library
  #include "sensitiveInformation.h"             // Contains WiFi credentials and MQTT settings
  WiFiClient espClient;                         // WiFi client for MQTT communication
  PubSubClient client(espClient);   
#endif

// Declaration of ST7789 display for ESP32-S3 Reverse TFT Feather
Adafruit_ST7789 display = Adafruit_ST7789(TFT_CS, TFT_DC, TFT_RST);

// IR Channels
#define CH1 0x0
#define CH2 0x1
#define CH3 0x2
#define CH4 0x3

// IR Transmission Pin for Adafruit High Power IR Emitter Breakout (Adafruit #5639)
#define IR_TRANS_IN   13  
#define IR_DEBUG_OFF  0  
#define IR_DEBUG_ON   1  

// Call PowerFunctions parameters
PowerFunctions pf(IR_TRANS_IN, CH1, IR_DEBUG_ON);

// Timers for non-blocking execution
unsigned long lastDebugTime = 0;
const unsigned long debugInterval = 3000;     // Step through speeds every 3 seconds

String currentStatus = "STOPPED";
String lastDisplayedStatus = "";
String lastDisplayedSpeed = "";

// Single and dual motor control is defined
void step(uint8_t output, uint8_t pwm, uint16_t time) {
  pf.combo_pwm(output, pwm);
  pf.single_pwm(output, pwm);
}

#if !DEBUG_MODE
void mqttConnect() {
  while (!client.connected()) {
    Serial.println("Connecting to MQTT...");
    if (client.connect(mqttClient)) {
      Serial.println("Connected to MQTT");
      client.subscribe(mqttTopic);
    } else {
      Serial.print("MQTT connection failed, state: ");
      Serial.println(client.state());
      delay(1000);
    }
  }
}

void mqttLoop() {
  if (!client.connected()) {
    mqttConnect();
  }
  client.loop();
}

void performActionBasedOnPayload(byte *payload) {
  Serial.println("MQTT Payload detected");

  if ((char)payload[0] == '0') {
    Serial.println("HALT");
    currentStatus = "STOPPED";
    updateDisplay(currentStatus, "BRAKE", "Emergency brake activated");
    step(RED, PWM_BRK, 0);
  } 
  else if ((char)payload[0] == '1') {
    Serial.println("ADVANCE");
    currentStatus = "RUNNING";
    updateDisplay(currentStatus, "Speed 3", "MQTT Command Executed");
    step(RED, PWM_FWD3, 0);
  }
}

void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived [");
  Serial.print(topic);
  Serial.print("] ");
  for (int i = 0; i < length; i++) {
    Serial.print((char)payload[i]);
  }
  Serial.println();

  performActionBasedOnPayload(payload);
}
#endif

// Display update function for ST7789 TFT Color Display
void updateDisplay(String status, String speedText, String details = "") {
  if (status != lastDisplayedStatus || speedText != lastDisplayedSpeed) {
    display.fillScreen(ST77XX_BLACK);
    
    // Header
    display.setTextSize(2);
    display.setTextColor(ST77XX_WHITE);
    display.setCursor(10, 10);
    display.println("TRAIN STATUS:");
    
    // Current Status
    display.setTextSize(2);
    display.setTextColor(ST77XX_GREEN);
    display.setCursor(10, 35);
    display.println(status);
    
    // Transmitted Speed
    display.setTextSize(2);
    display.setTextColor(ST77XX_YELLOW);
    display.setCursor(10, 60);
    display.print("SPD: ");
    display.println(speedText);
    
    // Details
    if (details != "") {
      display.setTextSize(1);
      display.setTextColor(ST77XX_WHITE);
      display.setCursor(10, 90);
      display.println(details);
    }
    
    // Timestamp
    display.setTextSize(1);
    display.setTextColor(ST77XX_WHITE);
    display.setCursor(10, 110);
    display.println("Time: " + String(millis() / 1000) + "s");
    
    lastDisplayedStatus = status;
    lastDisplayedSpeed = speedText;
    
    Serial.println("Display Updated - Status: " + status + " | Speed: " + speedText);
  }
}

// Debug function to step through FWD 1-7, STOP, REV 1-7, STOP
void debugIRTransmission() {
  unsigned long currentMillis = millis();
  
  // Non-blocking timer check
  if (currentMillis - lastDebugTime >= debugInterval) {
    lastDebugTime = currentMillis;

    static uint8_t debugStep = 0;
    String speedStr = "";
    uint8_t selectedPWM = PWM_BRK;

    switch (debugStep) {
      case 0:  speedStr = "FWD Speed 1"; selectedPWM = PWM_FWD1; currentStatus = "TEST: FWD"; break;
      case 1:  speedStr = "FWD Speed 2"; selectedPWM = PWM_FWD2; currentStatus = "TEST: FWD"; break;
      case 2:  speedStr = "FWD Speed 3"; selectedPWM = PWM_FWD3; currentStatus = "TEST: FWD"; break;
      case 3:  speedStr = "FWD Speed 4"; selectedPWM = PWM_FWD4; currentStatus = "TEST: FWD"; break;
      case 4:  speedStr = "FWD Speed 5"; selectedPWM = PWM_FWD5; currentStatus = "TEST: FWD"; break;
      case 5:  speedStr = "FWD Speed 6"; selectedPWM = PWM_FWD6; currentStatus = "TEST: FWD"; break;
      case 6:  speedStr = "FWD Speed 7"; selectedPWM = PWM_FWD7; currentStatus = "TEST: FWD MAX"; break;
      
      case 7:  speedStr = "BRAKE / STOP"; selectedPWM = PWM_BRK;  currentStatus = "TEST: STOPPED"; break;

      case 8:  speedStr = "REV Speed 1"; selectedPWM = PWM_REV1; currentStatus = "TEST: REV"; break;
      case 9:  speedStr = "REV Speed 2"; selectedPWM = PWM_REV2; currentStatus = "TEST: REV"; break;
      case 10: speedStr = "REV Speed 3"; selectedPWM = PWM_REV3; currentStatus = "TEST: REV"; break;
      case 11: speedStr = "REV Speed 4"; selectedPWM = PWM_REV4; currentStatus = "TEST: REV"; break;
      case 12: speedStr = "REV Speed 5"; selectedPWM = PWM_REV5; currentStatus = "TEST: REV"; break;
      case 13: speedStr = "REV Speed 6"; selectedPWM = PWM_REV6; currentStatus = "TEST: REV"; break;
      case 14: speedStr = "REV Speed 7"; selectedPWM = PWM_REV7; currentStatus = "TEST: REV MAX"; break;
      
      case 15: speedStr = "BRAKE / STOP"; selectedPWM = PWM_BRK;  currentStatus = "TEST: STOPPED"; break;
    }

    Serial.println("--- [IR DEBUG TX] ---");
    Serial.println("Transmitting Speed: " + speedStr);

    updateDisplay(currentStatus, speedStr, "DEBUG MODE (16-Step)");
    step(RED, selectedPWM, 0);

    debugStep = (debugStep + 1) % 16;
  }
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  // Power on ESP32-S3 Reverse TFT display power & backlight pins
  pinMode(TFT_I2C_POWER, OUTPUT);
  digitalWrite(TFT_I2C_POWER, HIGH);
  pinMode(TFT_BACKLIGHT, OUTPUT);
  digitalWrite(TFT_BACKLIGHT, HIGH);

  // Initialize integrated 1.14" ST7789 display
  // Rotation set to 1 for correct landscape alignment on Reverse TFT
  display.init(135, 240);
  display.setRotation(1);
  display.fillScreen(ST77XX_BLACK);

  // Pin mode for IR Emitter
  pinMode(IR_TRANS_IN, OUTPUT);
  digitalWrite(IR_TRANS_IN, LOW);

#if DEBUG_MODE
  Serial.println("=== RUNNING IN DEBUG MODE (No WiFi / No MQTT) ===");
  updateDisplay("DEBUG MODE", "STOPPED", "IR Output Only");
#else
  updateDisplay("STARTING", "N/A", "Connecting to WiFi...");
  
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.print("Connected! IP: ");
  Serial.println(WiFi.localIP());  

  client.setServer(mqttServer, mqttPort);
  client.setCallback(callback);
  mqttConnect();
  
  updateDisplay("IDLE", "STOPPED", "Waiting for commands...");
#endif
}

void loop() {
#if DEBUG_MODE
  debugIRTransmission();
#else
  mqttLoop();
#endif
}