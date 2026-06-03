FROM php:8.2-apache

RUN sed -i 's/80/8081/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:8081/g' /etc/apache2/sites-enabled/000-default.conf

COPY blandWebpage.php /var/www/html/index.php
COPY Beige.jpeg /var/www/html/Beige.jpeg

EXPOSE 8081