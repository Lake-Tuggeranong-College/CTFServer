-- Grant REPLICATION SLAVE privilege to devuser to allow reading the binary log
-- This is the standard privilege for binlog monitoring.
-- The user and password match the environment variables in docker-compose.yml: devuser / devpass

GRANT REPLICATION SLAVE ON *.* TO 'devuser'@'%' IDENTIFIED BY 'devpass';
GRANT BINLOG MONITOR ON *.* TO 'devuser'@'%' IDENTIFIED BY 'devpass';
FLUSH PRIVILEGES;