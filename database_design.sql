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
    sponsor			varchar(60)    	NOT NULL,
    requirement		varchar(100)    DEFAULT NULL,
    enrl_deadline	varchar(20)    	DEFAULT NULL,

    PRIMARY KEY(pro_id)
);

CREATE TABLE preference(
    pro_id	INT   		NOT NULL,
    csuid  	varchar(7) 	NOT NULL,

    FOREIGN KEY(pro_id) REFERENCES project(pro_id),
    FOREIGN KEY(csuid) REFERENCES student(csuid)
);
