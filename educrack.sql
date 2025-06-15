CREATE DATABASE educrack;
USE educrack;

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

CREATE TABLE students (
  student_id INT NOT NULL PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  course_id VARCHAR(6),
  FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

CREATE TABLE questions (
  question_id INT PRIMARY KEY AUTO_INCREMENT,
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
  FOREIGN KEY (lec_id) REFERENCES lecturers(lec_id)
);

CREATE TABLE quizzes (
  quiz_id INT PRIMARY KEY AUTO_INCREMENT,
  quiz_name VARCHAR(100) NOT NULL,
  unit_id VARCHAR(6) NOT NULL,
  lec_id INT NOT NULL,
  time_limit INT NOT NULL,
  pass_mark INT NOT NULL,
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
  student_id INT NOT NULL DEFAULT 0,
  quiz_id INT NOT NULL DEFAULT 0,
  score DECIMAL(5,2) NOT NULL DEFAULT 0.00, -- Removed CHECK
  grade CHAR(1) NOT NULL DEFAULT '?',
  NumberOfAttempts INT NOT NULL DEFAULT 0,
  UnitStatus VARCHAR(20) NOT NULL DEFAULT '?',
  ChangedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE answers (
  answer_id VARCHAR(7) NOT NULL PRIMARY KEY,
  student_id INT NOT NULL,
  question_id INT NOT NULL,
  select_option CHAR(1) NOT NULL,
  iscorrect TINYINT(1) NOT NULL,
  answered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id),
  FOREIGN KEY (question_id) REFERENCES questions(question_id)
);

CREATE TABLE results (
  studentId INT PRIMARY KEY,
  FName VARCHAR(50) NOT NULL DEFAULT '?',
  LName VARCHAR(50) NOT NULL DEFAULT '?',
  unit_id VARCHAR(6) NOT NULL DEFAULT '?',
  score DECIMAL(5,2) NOT NULL DEFAULT 0.00, -- Removed CHECK
  grade CHAR(1) NOT NULL DEFAULT '?',
  comment VARCHAR(30) NOT NULL DEFAULT '?'
);


CREATE TABLE notes (
  note_id INT PRIMARY KEY AUTO_INCREMENT,
  unit_id VARCHAR(6) NOT NULL,
  lec_id INT NOT NULL,
  title VARCHAR(100) NOT NULL,
  content TEXT NOT NULL,
  is_summary BOOLEAN DEFAULT TRUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (unit_id) REFERENCES units(unit_id),
  FOREIGN KEY (lec_id) REFERENCES lecturers(lec_id)
);


INSERT INTO courses (course_id, course_name) VALUES
('C001', 'BICS'),
('C002', 'BBA'),
('C003', 'BBNA');

INSERT INTO lecturers (lec_id, name, email, password) VALUES
(101, 'Dr. Jane Muthoni', 'j.muthoni@strathmore.edu', 'securehash123'),
(102, 'Prof. James Omondi', 'j.omondi@strathmore.edu', 'pwdhash456'),
(103, 'Dr. Susan Kariuki', 's.kariuki@strathmore.edu', 'hashed789');


INSERT INTO students (student_id, fullname, email, password, course_id) VALUES
(191400, 'Ann Wanjiru', 'a.wanjiru@strathmore.edu', 'studentpass1', 'C001'),
(176342, 'Brian Otieno', 'b.otieno@strathmore.edu', 'studentpass2', 'C002'),
(180982, 'Christine Akinyi', 'c.akinyi@strathmore.edu', 'studentpass3', 'C001');

INSERT INTO units (unit_id, unit_name, course_id) VALUES
('U101', 'Communication Skills', 'C001'),
('U102', 'Principles of Ethics', 'C001'),
('U103', 'Communication Skills', 'C002'),
('U104', 'Principles of Ethics', 'C002'),
('U105', 'Communication Skills', 'C003'),
('U106', 'Principles of Ethics', 'C003');


INSERT INTO questions (question_id, unit_id, question_text, optionA, optionB, optionC, optionD, correct_option, lec_id) VALUES
(1, 'U102', 'What is the object of the intellect?', 'truth', 'good', 'happiness', 'will', 'A', 101),
(2, 'U101','Which of these is the most important element of active listening?','Maintaining eye contact','Formulating your response while the speaker is talking','Asking clarifying questions','Nodding occasionally','C',101  
);

INSERT INTO quizzes (quiz_id, quiz_name, unit_id, lec_id, time_limit, pass_mark) VALUES
(1, 'Ethics Midterm', 'U102', 101, 45, 50),
(2, 'Comm Skills CAT', 'U101', 101, 30, 60);

INSERT INTO quiz_questions (quiz_id, question_id) VALUES
(1, 1), (2, 2);


INSERT INTO history (history_id, student_id, quiz_id, score, grade, NumberOfAttempts, UnitStatus) VALUES
('H001', 191400, 1, 18.50, 'B', 1, 'In Progress'),
('H002', 176342, 2, 25.00, 'A', 1, 'Completed'),
('H003', 180982, 1, 22.00, 'A', 2, 'Completed');


INSERT INTO answers (answer_id, student_id, question_id, select_option, iscorrect) VALUES
('ANS001', 191400, 1, 'A', 1),
('ANS002', 191400, 2, 'C', 1),
('ANS003', 176342, 1, 'A', 1);

INSERT INTO results (studentId, FName, LName, unit_id, score, grade, comment) VALUES
(191400, 'Ann', 'Wanjiru', 'U101', 22.00, 'A', 'Excellent performance'),
(176342, 'Brian', 'Otieno', 'U102', 25.00, 'A', 'Well done');


INSERT INTO notes (note_id, unit_id, lec_id, title, content, is_summary) VALUES
( 1,'U101', 101, 'Database Design', 'Primary keys uniquely identify records...', 1),
( 2,'U102', 102, 'OOP Principles', 'The four pillars of OOP are...', 1);

