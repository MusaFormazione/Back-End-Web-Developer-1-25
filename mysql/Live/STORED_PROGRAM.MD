# Stored procedure

```sql

DELIMITER $$

CREATE PROCEDURE GetFilmsByCategory1(IN categoryName VARCHAR(50))
BEGIN
     SELECT f.title AS FilmTitle, f.description AS FilmDescription
     FROM film f
     JOIN film_category fc ON f.film_id = fc.film_id
     JOIN category c ON fc.category_id = c.category_id
     WHERE c.name = categoryName;
END $$

DELIMITER ;

CALL GetFilmsByCategory1('Action');

```

# Function
Questa stored function accetta il nome di una categoria e restituisce il numero di film presenti in quella categoria.

```sql

DELIMITER $$

CREATE FUNCTION CountFilmsInCategory(categoryName VARCHAR(50))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE filmCount INT;
    
    SELECT COUNT(*) INTO filmCount 
    FROM film
    INNER JOIN film_category ON film_category.film_id = film.film_id
    INNER JOIN category ON category.category_id = film_category.category_id
    WHERE category.name = categoryName;
    
    RETURN filmCount;
END $$

DELIMITER ;

SELECT CountFilmsInCategory('Comedy');







#### -------------------------------

SELECT name, CountFilmsInCategory(name) AS film 
FROM category 

```



# Trigger

```sql


CREATE TABLE inventory_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    inventory_id INT NOT NULL,
    change_date DATETIME NOT NULL
);


--

DELIMITER $$

CREATE TRIGGER after_inventory_insert
AFTER INSERT ON inventory
FOR EACH ROW
BEGIN

    INSERT INTO inventory_log (inventory_id, change_date)
        VALUES (NEW.inventory_id, NOW());
    
END $$

DELIMITER ;

SELECT film_id FROM film WHERE film_id NOT IN (SELECT film_id FROM inventory) LIMIT 1;
INSERT INTO inventory (film_id, store_id) VALUES (14, 1);

SELECT * FROM inventory_log;
```


## LOAD DATA


```sql

SHOW VARIABLES LIKE 'secure_file_priv';

```