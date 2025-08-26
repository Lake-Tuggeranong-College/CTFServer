FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

EXPOSE 80
