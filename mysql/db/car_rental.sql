DROP DATABASE IF EXISTS car_rental;
CREATE DATABASE IF NOT EXISTS car_rental DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS car_rental.cars(
      id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      brand VARCHAR(255),
      model VARCHAR(255)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS car_rental.customers(
      id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255),
      surname VARCHAR(255),
      birth_date DATE
);

CREATE TABLE IF NOT EXISTS car_rental.rentals(
      id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      car_id INT UNSIGNED NOT NULL,
      customer_id INT UNSIGNED NOT NULL,
      rental_date DATE,
      return_date DATE,
      CONSTRAINT `fk_car_rental_cars` FOREIGN KEY (car_id) REFERENCES cars(id),
      CONSTRAINT `fk_car_rental_customers` FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE IF NOT EXISTS car_rental.rental_fees(
      id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      rental_id INT UNSIGNED NOT NULL,
      fee DECIMAL(10,2) NOT NULL,
      CONSTRAINT `fk_rental_fees_rentals` FOREIGN KEY (rental_id) REFERENCES rentals(id)
);

CREATE TABLE IF NOT EXISTS car_rental.car_features(
      car_id INT UNSIGNED NOT NULL,
      feature VARCHAR(255) NOT NULL,
      FOREIGN KEY (car_id) REFERENCES cars(id)
);

CREATE TABLE IF NOT EXISTS car_rental.car_images(
      car_id INT UNSIGNED NOT NULL,
      image LONGBLOB NOT NULL,
      CONSTRAINT `fk_car_images_cars` FOREIGN KEY (car_id) REFERENCES cars(id)
);