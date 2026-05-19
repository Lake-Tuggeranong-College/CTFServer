RUN apt-get update && apt-get install -y openssh-server

RUN sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

RUN useradd -m -s /bin/bash SpiceGuys && echo 'SpiceGuys:posh_spice' | chpasswd

FROM ubuntu:latest

WORKDIR "./var/www/html/_container_manager_app/containers/blanddudeshack"

COPY Beige.jpeg /var/www/html/_container_manager_app/containers/blanddudeshack/Beige.jpeg

CMD ["/usr/sbin/sshd", "-D"] 
# line 7 will open the ssh server in the foreground
EXPOSE 22

EXPOSE 8081

ENTRYPOINT service ssh start && bash