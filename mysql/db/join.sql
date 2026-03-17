-- CLIENTI CHE HANNO COMPRATO
SELECT * FROM customer
WHERE customer_id NOT IN (SELECT customer.customer_id
                               -- , first_name, last_name, sum(amount) as total_amount
                          FROM customer
                                   INNER JOIN payment ON payment.customer_id = customer.customer_id
                          GROUP BY customer.customer_id);

(
    SELECT customer.customer_id, first_name, last_name, sum(amount) as total_amount
    FROM customer
    INNER JOIN payment ON payment.customer_id = customer.customer_id
    GROUP BY customer.customer_id
    ORDER BY total_amount DESC
    LIMIT 3
)
UNION
(SELECT customer_id, first_name, last_name, 0 FROM customer
WHERE customer_id NOT IN (SELECT customer.customer_id
                          FROM customer
                          INNER JOIN payment ON payment.customer_id = customer.customer_id
                          GROUP BY customer.customer_id)
LIMIT 3);

-- 1003
SELECT count(*) FROM film;

-- 1003
-- 958 con la INNER
SELECT title, count(rental.rental_date) as noleggi FROM film
LEFT JOIN inventory ON film.film_id = inventory.film_id
LEFT JOIN rental ON rental.inventory_id = inventory.inventory_id
GROUP BY film.film_id
ORDER BY noleggi;


CREATE TABLE customer_payment_temp_1
SELECT customer.customer_id, first_name, last_name, IFNULL(sum(amount), 0) as total_amount
FROM customer
LEFT JOIN payment ON payment.customer_id = customer.customer_id
GROUP BY customer.customer_id
HAVING total_amount > 195 OR total_amount = 0
ORDER BY total_amount;

SELECT * FROM customer_payment_temp_1;


SELECT customer.customer_id, first_name, last_name, IFNULL(sum(amount), 0) as total_amount
FROM customer
         LEFT JOIN payment ON payment.customer_id = customer.customer_id
GROUP BY customer.customer_id
HAVING total_amount > 195 OR total_amount = 0
ORDER BY total_amount;


INSERT INTO customer
    (
     first_name,
     last_name,
     email,
     store_id,
     address_id,
     create_date
    )
VALUES (
        'TEST1',
        'TEST1',
        'TEST1',
        1,
        1,
        NOW()
       )
