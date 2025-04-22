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
  PRIMARY KEY (student_id),
  UNIQUE KEY email (email));


