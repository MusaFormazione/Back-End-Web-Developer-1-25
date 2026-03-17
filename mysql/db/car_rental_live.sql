DROP DATABASE IF EXISTS car_rental;
CREATE DATABASE IF NOT EXISTS car_rental;
USE car_rental;
-- WEBSTORM
DROP TABLE IF EXISTS customers;
CREATE TABLE customers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    birth_date DATE NOT NULL
);

DROP TABLE IF EXISTS cars;
CREATE TABLE cars (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL
);

DROP TABLE IF EXISTS rentals;
CREATE TABLE rentals (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    car_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    CONSTRAINT `fk_rentals_customers`
        FOREIGN KEY (customer_id)
            REFERENCES customers(id),
    CONSTRAINT `fk_rentals_cars`
        FOREIGN KEY (car_id)
            REFERENCES cars(id)
);

RENAME TABLE rentals TO noleggi;
RENAME TABLE cars TO auto;
ALTER TABLE auto RENAME COLUMN brand TO marca;
ALTER TABLE auto RENAME COLUMN model TO modello;

ALTER TABLE noleggi RENAME KEY `fk_car_rental_cars` TO `fk_car_rental_auto`;
ALTER TABLE noleggi RENAME COLUMN car_id TO auto_id;
SHOW KEYS FROM noleggi;

ALTER TABLE auto MODIFY marca VARCHAR(50) NOT NULL;
ALTER TABLE auto MODIFY modello VARCHAR(100) NOT NULL;
ALTER TABLE auto DROP anno;
ALTER TABLE auto ADD anno YEAR NOT NULL AFTER ID;
ALTER TABLE auto ADD INDEX idx_marca (marca);
ALTER TABLE auto ADD INDEX idx_modello (modello);
