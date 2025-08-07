create table Category
(
    CategoryName text not null,
    id           int auto_increment
        primary key,
    projectID    int  null
);

INSERT INTO devdb.Category (CategoryName, id, projectID) VALUES ('Tutorial', 1, 1);
INSERT INTO devdb.Category (CategoryName, id, projectID) VALUES ('Networking', 2, 1);
INSERT INTO devdb.Category (CategoryName, id, projectID) VALUES ('Cryptology', 3, 1);
INSERT INTO devdb.Category (CategoryName, id, projectID) VALUES ('OSINT', 4, 1);
INSERT INTO devdb.Category (CategoryName, id, projectID) VALUES ('Hex', 5, 1);
INSERT INTO devdb.Category (CategoryName, id, projectID) VALUES ('Web', 6, 1);
