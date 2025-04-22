create database educrack;
use educrack;
CREATE TABLE lecturers (
  lec_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) UNIQUE NOT NULL
);
CREATE TABLE  students (
  student_id varchar(4) NOT NULL,
  fullname varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  course_Id varchar(5) DEFAULT NULL,
  course_id varchar(4),
  PRIMARY KEY (student_id),
  UNIQUE KEY email (email),
  FOREIGN KEY (course_id) REFERENCES courses(course_id)
  FOREIGN KEY (questionId) REFERENCES questions (question_Id)
  );
CREATE TABLE  answers (
  answer_id varchar(7) NOT NULL,
  student_id varchar(4) NOT NULL,
  question_id varchar(5) NOT NULL,
  select_option char(1) NOT NULL,
  iscorrect tinyint(1) NOT NULL,
  anweredAT datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (answer_id),
  KEY student_id (student_id),
  FOREIGN KEY (student_id) REFERENCES students (student_id)
) 

CREATE TABLE Results (
    studentId   VARCHAR(4) PRIMARY KEY,
    FName       VARCHAR(50) NOT NULL DEFAULT "?",
    LName       VARCHAR(50) NOT NULL DEFAULT "?",
    unitId      VARCHAR(4) NOT NULL DEFAULT "?" ,
    score       DECIMAL(2,2) CHECK (score BETWEEN 0 AND 30) NOT NULL DEFAULT 0.00,
    grade       CHAR(1) NOT NULL DEFAULT "?",
    comment     VARCHAR(30) NOT NULL DEFAULT "?"
);

CREATE TABLE HISTORY (
     HistoryId VARCHAR(4) PRIMARY KEY,
    studentId   VARCHAR(4) NOT NULL DEFAULT "?",
    unitId      VARCHAR(4) NOT NULL DEFAULT "?" ,
    score       DECIMAL(2,2) CHECK (score BETWEEN 0 AND 30) NOT NULL DEFAULT 0.00,
    grade       CHAR(1) NOT NULL DEFAULT "?",
    NumberOfAttempts    INT  NOT NULL DEFAULT 0,
    UnitStatus VARCHAR(20) NOT NULL DEFAULT "?" ,
    ChangedAt DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
);


