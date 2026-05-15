FROM ubuntu:latest

WORKDIR "./var/www/html/_container_manager_app/containers/blanddudeshack"

COPY Beige.jpeg /var/www/html/_container_manager_app/containers/blanddudeshack/Beige.jpeg

CMD ["/usr/sbin/sshd", "-D"] 
# line 7 will open the ssh server in the foreground
EXPOSE 22

EXPOSE 80