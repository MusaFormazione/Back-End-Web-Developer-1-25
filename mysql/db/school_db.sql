DROP DATABASE school_db;
CREATE DATABASE IF NOT EXISTS school_db;
-- inglese
-- nomi tabelle in plurale
CREATE TABLE IF NOT EXISTS school_db.students (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    lastname VARCHAR(75),
    email VARCHAR(100),
    birth_date DATE,
    created_at TIMESTAMP
);

CREATE TABLE IF NOT EXISTS school_db.courses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    duration VARCHAR(255),
    is_live BIT,
    is_on_demand BIT,
    description TEXT,
    created_at TIMESTAMP
);

CREATE TABLE IF NOT EXISTS school_db.trainers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    lastname VARCHAR(75),
    email VARCHAR(100),
    created_at TIMESTAMP
);

CREATE TABLE IF NOT EXISTS school_db.courses_trainers (
    course_id INT UNSIGNED NOT NULL,
    trainer_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    PRIMARY KEY(course_id, trainer_id),
    CONSTRAINT `fk_courses_trainers_courses` FOREIGN KEY (course_id) REFERENCES courses(id),
    CONSTRAINT `fk_courses_trainers_trainers` FOREIGN KEY (trainer_id) REFERENCES trainers(id)
);

CREATE TABLE IF NOT EXISTS school_db.bookings (
   id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
   course_id INT UNSIGNED NOT NULL,
   student_id INT UNSIGNED NOT NULL,
   is_active BIT,
   is_started BIT,
   created_at TIMESTAMP,
   CONSTRAINT `fk_bookings_courses` FOREIGN KEY (course_id) REFERENCES courses(id),
   CONSTRAINT `fk_bookings_students` FOREIGN KEY (student_id) REFERENCES students(id)
);