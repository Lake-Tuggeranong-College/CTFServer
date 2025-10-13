FROM python:3.9-slim

WORKDIR /app

# Install necessary system packages for MySQL client and building Python libs
RUN apt-get update && apt-get install -y \
    default-libmysqlclient-dev \
    gcc \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY publisher.py .

CMD ["python", "publisher.py"]