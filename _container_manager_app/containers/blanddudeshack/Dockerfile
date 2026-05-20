FROM ubuntu:latest

RUN apt-get update && apt-get install -y openssh-server

RUN mkdir /var/run/sshd

RUN sed -i 's/PermitRootLogin yes/PermitRootLogin yes/' /etc/ssh/sshd_config

RUN useradd -m -s /bin/bash SpiceGuys && echo 'SpiceGuys:posh_spice' | chpasswd

COPY Beige.jpeg /var/www/html/_container_manager_app/containers/blanddudeshack/Beige.jpeg

CMD ["/usr/sbin/sshd", "-D"] 
# line 7 will open the ssh server in the foreground
EXPOSE 22

EXPOSE 8081

ENTRYPOINT service ssh start && bash

FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

COPY blandWebpage.php /var/www/html