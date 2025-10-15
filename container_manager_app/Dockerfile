# Use a slim Python image for efficiency
FROM python:3.11-slim

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

# Copy the requirements file (we still need it for pymysql)
COPY requirements.txt .

# Install pymysql from requirements.txt, but install pymysqlreplication via git
# Ensure pymysql is installed first.
RUN pip install --no-cache-dir pymysql && \
    pip install --no-cache-dir git+https://github.com/noplay/python-mysql-replication.git

# We can remove git and the build tools now to reduce image size
RUN apt-get purge -y git gcc default-libmysqlclient-dev pkg-config && \
    apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/*

# Copy the manager script
COPY manager.py .

# Command to run the manager script when the container starts
CMD ["python", "manager.py"]
