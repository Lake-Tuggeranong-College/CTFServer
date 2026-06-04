#!/bin/bash
set -e

# Start the SSH service
service ssh start

# If you need environment variables in your SSH session, export them here
# printenv | grep -v "no_proxy" >> /etc/environment

# Keep the container running by starting your main application 
# or a shell if it's for development
exec "$@"
