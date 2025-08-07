create table ChallengeData
(
    id        int auto_increment
        primary key,
    moduleID  int  null,
    reference text null,
    data      text null
);

INSERT INTO devdb.ChallengeData (id, moduleID, reference, data) VALUES (1, 44, 'Email_1: John.R: \'Don\'t forget to have no repeating charaters Xen\'', 'Email 1');
INSERT INTO devdb.ChallengeData (id, moduleID, reference, data) VALUES (2, 44, 'E_2', 'Email 2');
INSERT INTO devdb.ChallengeData (id, moduleID, reference, data) VALUES (3, 44, 'E_3', 'Email 3');
INSERT INTO devdb.ChallengeData (id, moduleID, reference, data) VALUES (4, 44, 'E_4', 'Email 4');
INSERT INTO devdb.ChallengeData (id, moduleID, reference, data) VALUES (5, 44, 'E_5', 'Email 5');
