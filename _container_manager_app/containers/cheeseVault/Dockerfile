FROM ubuntu:latest

RUN apt-get update && apt-get install -y openssh-server

RUN mkdir -p /var/run/sshd

RUN useradd -m -s /bin/bash CheeseWitch && echo 'CheeseWitch:TotallySecure01' | chpasswd

RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config

RUN sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

# Disable Ubuntu MOTD
RUN rm -f /etc/update-motd.d/*

# Disable PAM MOTD
RUN sed -i '/pam_motd.so/d' /etc/pam.d/sshd
# --- ADD THESE LINES ---
# Copy your script into the container
COPY challengeFiles /usr/local/bin/

RUN echo "/usr/local/bin/challengeFiles/" >> /etc/profile
# Make it executable
RUN chmod +x /usr/local/bin/*.sh

# Run it on login by appending to /etc/profile
RUN echo "/usr/local/bin/login-sequence.sh" >> /etc/profile
# --- END ADDITIONS ---

EXPOSE 22

CMD ["/usr/sbin/sshd", "-D"]
