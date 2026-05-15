FROM php:8.5-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Install mosquitto-clients for MQTT communication
RUN apt-get update && apt-get install -y mosquitto-clients && rm -rf /var/lib/apt/lists/*

EXPOSE 80
