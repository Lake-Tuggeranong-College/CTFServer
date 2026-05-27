FROM ubuntu:latest

RUN apt-get update && apt-get install -y openssh-server

RUN mkdir -p /var/run/sshd

# To create a unique username, replace both instances of 'User01'. And to create a unique password, replace 'Password01' with the new password.

RUN useradd -m -s /bin/bash User01 && echo 'User01:Password01' | chpasswd

RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

RUN sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

EXPOSE 22

CMD ["/usr/sbin/sshd", "-D"]
