# Use a slim Python image for efficiency
# FROM python:3.12-slim
FROM python:3.12

# Install dependencies needed for compiling C-extensions (like those used by some
# database connectors) and the MySQL client libraries.
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    gcc \
    default-libmysqlclient-dev \
    pkg-config \
    # Temporarily install git to pull pymysqlreplication
    git \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory inside the container
WORKDIR /home/manager

# Install required Python packages.
# NOTE: Removed 'COPY requirements.txt' since we can install everything here.
# ADDED 'docker' library for Docker client functionality.
RUN pip install --no-cache-dir \
    pymysql \
    docker \
    git+https://github.com/noplay/python-mysql-replication.git

# We can remove git and the build tools now to reduce image size
# Update package lists, install docker.io and the docker-compose-plugin
RUN apt-get update && \
    apt-get install -y docker.io docker-compose && \
    \
    # Cleanup commands (as per your original request)
    apt-get purge -y git gcc default-libmysqlclient-dev pkg-config && \
    apt-get autoremove -y && \
    \
    # Final cleanup of package lists
    rm -rf /var/lib/apt/lists/*

# Copy the manager script (this is technically overridden by the bind mount in docker-compose, 
# but kept for non-development environments)
COPY manager.py .

# Command to run the manager script when the container starts
CMD ["python", "manager.py"]