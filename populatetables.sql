LOAD DATA LOCAL INFILE 'students.csv'
INTO TABLE mystro.students
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'course.csv'
INTO TABLE mystro.course
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'course_name.csv'
INTO TABLE mystro.course_name
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'assignment.csv'
INTO TABLE mystro.assignment
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'grade.csv'
INTO TABLE mystro.grade
FIELDS TERMINATED BY ','
LINES TERMINATED BY "\n"
(assignment_id, @vgrade, student_id)
SET grade = NULLIF(@vgrade, '');

LOAD DATA LOCAL INFILE 'ta.csv'
INTO TABLE mystro.ta
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'take.csv'
INTO TABLE mystro.take
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'qa.csv'
INTO TABLE mystro.QA
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'tag_category.csv'
INTO TABLE mystro.tag_category
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'qa_category.csv'
INTO TABLE mystro.qa_category
FIELDS TERMINATED BY ',';

LOAD DATA LOCAL INFILE 'reply.csv'
INTO TABLE mystro.reply
FIELDS TERMINATED BY ',';
