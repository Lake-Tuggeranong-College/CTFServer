-- Grant REPLICATION SLAVE privilege to CyberCity to allow reading the binary log
-- This is the standard privilege for binlog monitoring.
-- The user and password match the environment variables in docker-compose.yml: CyberCity / Cyb3rC1ty

GRANT REPLICATION SLAVE ON *.* TO 'CyberCity'@'%' IDENTIFIED BY 'Cyb3rC1ty';
GRANT BINLOG MONITOR ON *.* TO 'CyberCity'@'%' IDENTIFIED BY 'Cyb3rC1ty';
FLUSH PRIVILEGES;