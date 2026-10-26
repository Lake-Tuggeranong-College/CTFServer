FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y openssh-server && \
    rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/run/sshd
RUN echo 'root:password' | chpasswd
RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config

COPY start.sh /start.sh
COPY secretdocuments.webp /var/www/html/secretdocuments.webp

RUN sed -i 's/\r$//' /start.sh && chmod +x /start.sh

EXPOSE 22

ENTRYPOINT ["/start.sh"]
