create database educrack;
use educrack;

CREATE TABLE courses (
  course_id VARCHAR(6) NOT NULL PRIMARY KEY,
  course_name VARCHAR(100) NOT NULL
);

CREATE TABLE lecturers (
  lec_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE units (
  unit_id VARCHAR(6) NOT NULL PRIMARY KEY,
  unit_name VARCHAR(100) NOT NULL,
  course_id VARCHAR(6) NOT NULL,
  FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

CREATE TABLE  students (
  student_id int NOT NULL PRIMARY KEY,
  fullname varchar(100) NOT NULL,
  email varchar(100) UNIQUE NOT NULL,
  password varchar(255) NOT NULL,
  course_id varchar(6) DEFAULT NULL,
  FOREIGN KEY (course_id) REFERENCES courses(course_id)
  );

CREATE TABLE questions (
  question_id int PRIMARY KEY auto_increment,
  unit_id VARCHAR(6) NOT NULL,
  question_text TEXT NOT NULL,
  optionA VARCHAR(255) NOT NULL,
  optionB VARCHAR(255) NOT NULL,
  optionC VARCHAR(255) NOT NULL,
  optionD VARCHAR(255) NOT NULL,
  correct_option CHAR(1) NOT NULL CHECK (correct_option IN ('A', 'B', 'C', 'D')),
  lec_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (unit_id) REFERENCES units(unit_id),
  Foreign key (lec_id) References lecturers(lec_id)
);

CREATE TABLE quizzes (
  quiz_id INT PRIMARY KEY AUTO_INCREMENT,
  quiz_name VARCHAR(100) NOT NULL,
  unit_id VARCHAR(6) NOT NULL,
  lec_id INT NOT NULL,
  time_limit INT NOT NULL,  -- in minutes
  pass_mark INT NOT NULL,   -- percentage
  FOREIGN KEY (unit_id) REFERENCES units(unit_id),
  FOREIGN KEY (lec_id) REFERENCES lecturers(lec_id)
);

CREATE TABLE quiz_questions (
  quiz_id INT NOT NULL,
  question_id INT NOT NULL,
  PRIMARY KEY (quiz_id, question_id),
  FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id),
  FOREIGN KEY (question_id) REFERENCES questions(question_id)
);

CREATE TABLE history (
    history_id VARCHAR(4) PRIMARY KEY,
    student_id  int NOT NULL DEFAULT "?",
    quiz_id      int NOT NULL DEFAULT "?" ,
    score       DECIMAL(5,2) CHECK (score BETWEEN 0 AND 30) NOT NULL DEFAULT 0.00,
    grade       CHAR(1) NOT NULL DEFAULT "?",
    NumberOfAttempts    INT  NOT NULL DEFAULT 0,
    UnitStatus VARCHAR(20) NOT NULL DEFAULT "?" ,
    ChangedAt DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
);

CREATE TABLE  answers (
  answer_id varchar(7) NOT NULL,
  student_id int NOT NULL,
  question_id int NOT NULL,
  select_option char(1) NOT NULL,
  iscorrect tinyint(1) NOT NULL,
  anwered_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (answer_id),
  KEY student_id (student_id),
  FOREIGN KEY (student_id) REFERENCES students (student_id),
  FOREIGN KEY (question_id) REFERENCES questions (question_id)
);
CREATE TABLE results (
    studentId   int PRIMARY KEY,
    FName       VARCHAR(50) NOT NULL DEFAULT "?",
    LName       VARCHAR(50) NOT NULL DEFAULT "?",
    unit_id      VARCHAR(6) NOT NULL DEFAULT "?" ,
    score       DECIMAL(5,2) CHECK (score BETWEEN 0 AND 30) NOT NULL DEFAULT 0.00,
    grade       CHAR(1) NOT NULL DEFAULT "?",
    comment     VARCHAR(30) NOT NULL DEFAULT "?"
);

CREATE TABLE notes(
note_id INT PRIMARY KEY AUTO_INCREMENT,
unit_id varchar (6) not null,
lec_id int not null,
title VARCHAR(100) NOT NULL,
content TEXT NOT NULL,
is_summary BOOLEAN DEFAULT TRUE,
created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (unit_id) REFERENCES units(unit_id),
FOREIGN KEY (lec_id) REFERENCES lecturers(lec_id)
);
insert into 
INSERT INTO courses (course_id, course_name) VALUES
('C001', 'BICS'),
('C002', 'BBA'),
('C003', 'BBNA');

INSERT INTO units (unit_id, unit_name, course_id) VALUES
('U101', 'Communication Skills', 'C001'),
('U102', 'Principles of Ethics', 'C001'),
('U103', 'Communication Skills', 'C002'),
('U104', 'Principles of Ethics', 'C002'),
('U105', 'Communication Skills', 'C003'),
('U106', 'Principles of Ethics', 'C003');




