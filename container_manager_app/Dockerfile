# Use a slim Python image for efficiency
FROM python:3.11-slim

# Install dependencies needed for MySQL client
# The mysql-connector-python package requires build tools for the C connector library
RUN apt-get update && \
    apt-get install -y default-libmysqlclient-dev gcc pkg-config && \
    rm -rf /var/lib/apt/lists/*

# Set the working directory inside the container to the new location (e.g., /home/manager)
WORKDIR /root/manager

# Copy the requirements file and install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the manager script from the host's ./container_manager_app/ directory
# to the container's /home/manager/ directory.
COPY manager.py .

# Command to run the manager script when the container starts
CMD ["python", "manager.py"]
