# The initial image is from the official PHP image with Apache. It is fixed at 8.2 to ensure compatibility
FROM php:8.2-apache

# Install necessary dependencies for PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# mask the true file hierarchy of the container
RUN a2enmod rewrite

# chown means 'change owner', -R means 'recursive', apache can read/write files without access conflicts
RUN chown -R www-data:www-data /var/www/html

# copy the webpage file to the Apache server's root directory
COPY blandWebpage.php /var/www/html
