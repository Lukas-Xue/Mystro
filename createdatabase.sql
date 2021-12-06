/* create mystro schema */
CREATE DATABASE mystro;

/* specify the database to use */
USE mystro;

/* create student table */
CREATE TABLE students
(
  login_id VARCHAR(25) 	NOT NULL,
  fname VARCHAR(15) 	NOT NULL,
  lname VARCHAR(15) 	NOT NULL,
  student_id CHAR(10)	NOT NULL, 
  PRIMARY KEY (student_id)
);

/* create course table */
CREATE TABLE course
(
  course_id INT 		NOT NULL,
  number TINYTEXT 	NOT NULL,
  semester VARCHAR(6) 	NOT NULL,
  year YEAR		NOT NULL,
  instructor_id CHAR(10) 	NOT NULL,
  PRIMARY KEY (course_id),
  FOREIGN KEY (instructor_id) REFERENCES students(student_id)
);

/* create take table */
CREATE TABLE take
(
  letter_grade VARCHAR(2) 	NOT NULL,
  student_id CHAR(10) 	NOT NULL,
  course_id INT 		NOT NULL,
  PRIMARY KEY (student_id,course_id),
  FOREIGN KEY (student_id) REFERENCES students(student_id),
  FOREIGN KEY (course_id) REFERENCES course(course_id)
);

/* create assignment table */
CREATE TABLE assignment
(
  assignment_id INT 	NOT NULL AUTO_INCREMENT,
  name TINYTEXT		NOT NULL,
  total_pts INT		NOT NULL,
  text TEXT		NOT NULL,
  due TIMESTAMP 		NOT NULL,
  course_id INT		NOT NULL,
  PRIMARY KEY (assignment_id),
  FOREIGN KEY (course_id) REFERENCES course(course_id)
);

/* create grade table */
CREATE TABLE grade
(
  assignment_id INT	NOT NULL,
  grade int(10) unsigned DEFAULT NULL,
  student_id CHAR(10) 	NOT NULL,
  PRIMARY KEY (assignment_id,student_id),
  FOREIGN KEY (assignment_id) REFERENCES assignment(assignment_id),
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE QA
(
  qa_id INT		NOT NULL AUTO_INCREMENT,
  course_id INT		NOT NULL,
  title TINYTEXT 	NOT NULL,
  text TEXT		NOT NULL,
  post_date TIMESTAMP	NOT NULL,
  std_id CHAR(10)	NOT NULL,
  PRIMARY KEY (qa_id),
  FOREIGN KEY (course_id) REFERENCES course(course_id),
  FOREIGN KEY (std_id) REFERENCES students(student_id)
);

CREATE TABLE tag_category
(
  tag VARCHAR(25)	NOT NULL,
  tag_id INT	NOT NULL,
  course_id INT	NOT NULL,
  FOREIGN KEY (course_id) REFERENCES course(course_id),
  PRIMARY KEY (tag_id)
  
);

CREATE TABLE qa_category
(
  qa_id INT	NOT NULL,
  tag_id INT	NOT NULL,
  PRIMARY KEY (qa_id,tag_id),
  FOREIGN KEY (qa_id) REFERENCES QA(qa_id),
  FOREIGN KEY (tag_id) REFERENCES tag_category(tag_id)
);

CREATE TABLE reply
(
  reply_id INT		NOT NULL AUTO_INCREMENT,
  student_id CHAR(10)	NOT NULL,
  text TEXT		NOT NULL,
  time TIMESTAMP		NOT NULL,
  qa_id INT		NOT NULL,
  PRIMARY KEY (reply_id),
  FOREIGN KEY (qa_id) REFERENCES QA(qa_id),
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE course_name
(
  name VARCHAR(255)	NOT NULL,
  course_id INT		NOT NULL,
  FOREIGN KEY (course_id) REFERENCES course(course_id),
  PRIMARY KEY (course_id)
);

CREATE TABLE ta
(
  course_id INT	NOT NULL,
  ta_id CHAR(10) NOT NULL,
  PRIMARY KEY (course_id,ta_id),
  FOREIGN KEY (course_id) REFERENCES course(course_id),
  FOREIGN KEY (ta_id) REFERENCES students(student_id)
);
