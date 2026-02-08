FROM python:3.9-slim

WORKDIR /app

# Install necessary system packages for MySQL client and building Python libs
RUN apt-get update && apt-get install -y \
    default-libmysqlclient-dev \
    gcc \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Copy only requirements first to leverage Docker cache
COPY ./db_publisher_app/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# We no longer COPY the publisher.py here during build if we want 
# to rely entirely on the bind mount for development/easy updates.
# However, keeping a COPY as a fallback is fine, but the bind mount 
# in docker-compose will override it.

CMD ["python", "publisher.py"]