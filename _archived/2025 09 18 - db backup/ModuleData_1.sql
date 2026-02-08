create table IF NOT EXISTS ModuleData
(
    id       int auto_increment
        primary key,
    ModuleID int      null,
    DateTime datetime null,
    Data     text     null,
    constraint ModuleData_RegisteredModules_ID_fk
        foreign key (ModuleID) references archivedRegisteredModules (ID)
);

