FROM ubuntu:latest

# Install SSH and sudo
RUN apt-get update && apt-get install -y openssh-server sudo \
    && mkdir /var/run/sshd

# Set root password (change 'ctfpassword' to your choice)
RUN echo 'root:ctfpassword' | chpasswd

# Permit root login and disable PAM restrictions
RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -i 's/Include \/etc\/ssh\/sshd_config.d\/\*.conf/#Include \/etc\/ssh\/sshd_config.d\/\*.conf/' /etc/ssh/sshd_config

# Expose the internal SSH port
EXPOSE 22

# Start SSH daemon in the foreground so the container stays alive
CMD ["/usr/sbin/sshd", "-D"]