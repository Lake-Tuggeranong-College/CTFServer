create table Projects
(
    project_id   int auto_increment
        primary key,
    project_name text not null
);

INSERT INTO CyberCity.Projects (project_id, project_name) VALUES (1, '2024 - Biolab');
INSERT INTO CyberCity.Projects (project_id, project_name) VALUES (2, '2025 - Nuclear Disaster');
