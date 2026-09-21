# FROM php:8.2-apache

# RUN docker-php-ext-install pdo pdo_mysql

# RUN a2enmod rewrite

# RUN chown -R www-data:www-data /var/www/html

# COPY index.php /var/www/html


FROM php:8.2-apache

# Install OS packages and PHP extensions (pdo_mysql, mysqli, gd for simple image handling)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
  && docker-php-ext-install pdo pdo_mysql mysqli \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install gd \
  && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers expires

# Allow .htaccess (optional; useful if you use rewrites)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf || true

# Copy site files into the web root
WORKDIR /var/www/html
COPY . /var/www/html/

# Ensure proper permissions for www-data
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 80

# Use the default Apache foreground command
CMD ["apache2-foreground"]