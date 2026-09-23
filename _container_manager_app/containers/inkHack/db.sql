CREATE TABLE users(  
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email_address TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    name TEXT NOT NULL, 
    isPatient BOOLEAN DEFAULT FALSE, 
    isStaff BOOLEAN DEFAULT FALSE);
INSERT INTO users VALUES(7,'patient@patient.com','$2y$12$4N3TCo4HwH1wS1fAFL5qWuCyzTrQKQUlLs38t8Qs4Kg1L1ehQAGaG','patient ',1,0);
INSERT INTO users VALUES(8,'staff@staff.com','$2y$12$Gi8ZoihcC9.urGYH8ZAMceYZMJoTBlzSqmyeEOiqTqyELBlDlLQy2','staff',0,1);

CREATE TABLE staff_data (
    id INTEGER PRIMARY KEY,
    userId INT NOT NULL,
    bank_details TEXT,
    pay_rate DECIMAL(10,2),
    roster TEXT,
    position VARCHAR(100),
    work_history TEXT,
    leave_balance DECIMAL(5,2),
    super_details TEXT,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
);