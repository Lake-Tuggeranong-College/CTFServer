create table Challenges
(
    ID                int auto_increment
        primary key,
    challengeTitle    text                 null,
    challengeText     text                 null,
    flag              text                 not null,
    pointsValue       int                  not null,
    moduleName        varchar(255)         null,
    moduleValue       varchar(255)         null,
    dockerChallengeID varchar(255)         null,
    container         int                  null,
    Image             text                 null,
    Enabled           tinyint(1) default 1 null,
    categoryID        int                  null,
    constraint xChallenges_Category_id_fk
        foreign key (categoryID) references Category (id)
);

INSERT INTO CyberCity.Challenges (ID, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) VALUES (73, 'TrafficLights', 'Traffic Jammed', 'CTF{operation_greenwave}', 5, 'TrafficLights', '1', null, -1, 'trafficLights.png', 2, 1);
INSERT INTO CyberCity.Challenges (ID, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) VALUES (74, 'Windmill', 'Turbine Takeover', 'CTF{w1ndm1ll_w1nn3r}', 5, 'Windmill', '1', null, -1, 'windmill.png', 2, 2);
INSERT INTO CyberCity.Challenges (ID, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) VALUES (75, 'GarageDoor', 'Open Sesame', 'CTF{Alohomora}', 5, 'GarageDoor', '2', '3', -1, 'windmill.png', 2, 2);
INSERT INTO CyberCity.Challenges (ID, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) VALUES (79, 'Alarm', 'Alarm', 'CTF{Alarm}', 5, 'Alarm', '1', '2', 1, 'trafficLights.png', 2, 1);
INSERT INTO CyberCity.Challenges (ID, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) VALUES (80, '6', '6', '6', 6, 'module6', '', '6', 6, '', 1, 6);
INSERT INTO CyberCity.Challenges (ID, challengeTitle, challengeText, flag, pointsValue, moduleName, moduleValue, dockerChallengeID, container, Image, Enabled, categoryID) VALUES (81, '[New Challenge]', '[Null]', '[Null]', 1, '[Null]', null, null, null, '[Null]', 1, null);
