SELECT title, category FROM film_list
WHERE category LIKE 'Sci-fi'
   OR category LIKE 'Family'
   OR category LIKE 'Sport'
   OR category LIKE 'Action';

SELECT title, category FROM film_list
WHERE category IN ('Sci-fi','Family','Sport','Action');

SELECT title, category FROM film_list
WHERE category IN (
    SELECT name FROM category WHERE name LIKE '%f%' -- subquery
)
LIMIT 5 OFFSET 1;

SELECT FID, title, category FROM film_list
WHERE category IN (
    SELECT name FROM category WHERE name LIKE '%f%' -- subquery
)
ORDER BY FID DESC
LIMIT 1, 5;

SELECT * FROM (
            SELECT FID, title, category
            FROM film_list
            WHERE category IN (SELECT name
                               FROM category
                               WHERE name LIKE '%f%' -- subquery
            )
            LIMIT 1, 5
        ) as res
ORDER BY FID DESC;


31;APACHE DIVINE;Family
43;ATLANTIS CAUSE;Family
50;BAKED CLEOPATRA;Family
53;BANG KWAI;Family
63;BEDAZZLED MARRIED;Family


SELECT email FROM user
WHERE user_id IN (
    SELECT user_id FROM film_view
);

SELECT * FROM user where user_id = 1;

INSERT INTO user (user_id, username, email, password) VALUES (1, 'test', 'test@email,it', 'testtest');
INSERT INTO film_view (film_view_id, film_id, user_id) VALUES (1,1, 1);

-- deduplica rispetto a un campo
SELECT first_name
FROM actor
WHERE first_name IN ('GENE', 'MERYL')
ORDER BY first_name;

SELECT first_name, count(*) as items FROM actor
WHERE first_name IN ('GENE', 'MERYL')
GROUP BY first_name;

SELECT first_name as nome, count(*) as ricorrenze FROM actor
WHERE first_name IN ('GENE', 'MERYL')
GROUP BY first_name;

SELECT customer_id, count(*) as count, sum(amount) as sum, max(amount) as max, min(amount) as min, avg(amount) as avg
FROM payment
GROUP BY customer_id
HAVING count > 40 OR sum > 150
ORDER BY sum DESC;



SELECT first_name as nome, count(*) as ricorrenze FROM actor
WHERE nome IN ('GENE', 'MERYL');

-- FROM payment
-- GROUP BY
-- SELECT ...
-- HAVING
-- ORDER BY

SELECT  substring(first_name, 0, 3) as prefix,
        first_name, last_name, 100 as films,
        now() as data
FROM actor;

SELECT * FROM actor
         WHERE first_name = 'JIM' and last_name = 'CAREY';

SELECT first_name, last_name, count(*) as film FROM actor
INNER JOIN film_actor USING(actor_id)
GROUP BY actor_id;

SELECT first_name, last_name, count(*) as film FROM actor AS a
INNER JOIN film_actor AS fa ON fa.actor_id = a.actor_id
GROUP BY fa.actor_id;



SELECT actor_id FROM actor
WHERE actor_id NOT IN (
    SELECT actor_id FROM film_actor
    );
