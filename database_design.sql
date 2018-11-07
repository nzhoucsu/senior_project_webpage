CREATE DATABASE senior_project_db;
USE senior_project_db;

CREATE TABLE student(
    csuid     varchar(7)     NOT NULL,
    fname     varchar(30)    NOT NULL,
    lname     varchar(30)    NOT NULL,
    major1    varchar(30)    NOT NULL,
    major2    varchar(30),
    
    PRIMARY KEY(csuid)
); 

CREATE TABLE instructor(
    csuid    varchar(7)    NOT NULL,
    fname    varchar(30)    NOT NULL,
    lname    varchar(30)    NOT NULL,

    PRIMARY KEY(csuid)
);

CREATE TABLE project (
    pro_id			INT 			NOT NULL AUTO_INCREMENT,
    title			varchar(200)    NOT NULL,
    fname			varchar(30)    	NOT NULL,
    lname			varchar(30)    	NOT NULL,
    csuid    		varchar(7)    	NOT NULL,
    requirement		varchar(100)    DEFAULT NULL,
    reg_date		varchar(10)    	DEFAULT NULL,

    PRIMARY KEY(pro_id)
);

CREATE TABLE preference(
    pro_id		INT   		NOT NULL,
    csuid  		varchar(7) 	NOT NULL,
    enrl_date	varchar(10)    	DEFAULT NULL,

    FOREIGN KEY(pro_id) REFERENCES project(pro_id),
    FOREIGN KEY(csuid) REFERENCES student(csuid)
);

CREATE TABLE deadline(
    deadlinedate	varchar(10)    	DEFAULT NULL,
);

CREATE TABLE admin(
    fname			varchar(30)    	NOT NULL,
    lname			varchar(30)    	NOT NULL,
    csuid    		varchar(7)    	DEFAULT NULL,
);

INSERT INTO admin (fname, lname) VALUES('Yongjian', 'Fu');