# VIEW

```sql

SELECT name FROM favorites_categories
WHERE customer_id = outer_query.customer_id
LIMIT 1;


SELECT first_name, last_name,
       (
           SELECT name FROM favorites_categories
           WHERE customer_id = outer_query.customer_id
           LIMIT 1
       )  as favorites_category_1,
       (
           SELECT name FROM favorites_categories
           WHERE customer_id = outer_query.customer_id
           LIMIT 1, 1
       )  as favorites_category_2,
       (
           SELECT name FROM favorites_categories
           WHERE customer_id = outer_query.customer_id
           LIMIT 2, 1
       )  as favorites_category_3

FROM customer AS outer_query;



-- Lista attori per film
SELECT
    f.film_id,
    f.title,
    GROUP_CONCAT(
            CONCAT(a.first_name, ' ', a.last_name)
            ORDER BY a.last_name, a.first_name
            SEPARATOR ', '
    ) AS actors
FROM film f
         JOIN film_actor fa ON f.film_id = fa.film_id
         JOIN actor a ON fa.actor_id = a.actor_id
GROUP BY f.film_id, f.title
LIMIT 5;
```