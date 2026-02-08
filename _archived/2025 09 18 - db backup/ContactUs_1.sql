create table IF NOT EXISTS ContactUs
(
    ID       int auto_increment
        primary key,
    Username text       not null,
    Email    text       not null,
    IsRead   tinyint(1) not null
);

INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (1, 'Oliver', 'test123@gmail.com', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (2, 'Oliver', 'teser1@gmail.com', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (3, 'fef', 'test123', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (4, 'dewf', 'test12', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (5, 'agfadfga', 'ryan.cather@ed.act.edu.au', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (6, 'User21', '27@gmail.com', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (7, 'saxo', 'test.com', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (8, 'Oliver', '123@test.com', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (9, 'no', 'doesthisevenwork@notgmail.com', 1);
INSERT INTO devdb.ContactUs (ID, Username, Email, IsRead) VALUES (10, 'Problum chiels', 'tjis page isnt working', 0);
