FROM ubuntu:22.04

# Prevent tzdata from asking for timezone
ENV DEBIAN_FRONTEND=noninteractive

# Set timezone (example: Australia/Sydney)
RUN ln -fs /usr/share/zoneinfo/Australia/Sydney /etc/localtime && \
    apt-get update && \
    apt-get install -y tzdata openssh-server apache2 php php-mysql && \
    dpkg-reconfigure --frontend noninteractive tzdata && \
    rm -rf /var/lib/apt/lists/*
    
RUN apt-get update && \
    apt-get install -y openssh-server apache2 php php-mysql && \
    rm -rf /var/lib/apt/lists/*

RUN sed -i 's/80/8081/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:8081/g' /etc/apache2/sites-enabled/000-default.conf

RUN mkdir /var/run/sshd
RUN echo 'root:password' | chpasswd
RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config

COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 22
EXPOSE 8081

ENTRYPOINT ["/start.sh"]
