create table IF NOT EXISTS DockerContainers
(
    ID              int auto_increment
        primary key,
    timeInitialised timestamp not null,
    userID          int       not null,
    challengeID     text      not null,
    port            int       null
);

INSERT INTO devdb.DockerContainers (ID, timeInitialised, userID, challengeID, port) VALUES (47, '2025-02-11 13:44:41', 6, 'chmod', 1034);
