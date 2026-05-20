FROM ubuntu:latest

RUN apt-get update && apt-get install -y openssh-server

RUN mkdir /var/run/sshd

COPY Alarm.png /home/CheeseWitch/Alarm.png

COPY Burgle.txt /home/CheeseWitch/Burgle.txt

RUN useradd -m -s /bin/bash CheeseWitch & echo 'CheeseWitch:TotallySecure01' | chpasswd

RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

RUN sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

EXPOSE 22

CMD ["/usr/sbin/sshd", "-D"]
