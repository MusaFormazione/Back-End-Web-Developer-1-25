# Esercizi 2

## Numero film per categoria

```sql

SELECT category.name AS categoria, COUNT(film_category.film_id) as numero_film FROM category
LEFT JOIN film_category ON category.category_id = film_category.category_id
GROUP BY category.category_id;

```
## Categorie di film preferite dai clienti

```sql

-- category
-- film_category
-- inventory
-- rental
-- customer

SELECT first_name, last_name,
    (
        SELECT GROUP_CONCAT(name) FROM (
           (
               SELECT category.name FROM customer
                                             INNER JOIN rental ON rental.customer_id = customer.customer_id
                                             INNER JOIN inventory ON inventory.inventory_id = rental.inventory_id
                                             INNER JOIN film ON film.film_id = inventory.film_id
                                             INNER JOIN film_category ON film_category.film_id = film.film_id
                                             INNER JOIN category ON category.category_id = film_category.category_id
               WHERE customer.customer_id = outer_query.customer_id
               GROUP BY category.category_id, category.name, customer.customer_id
               ORDER BY customer.customer_id, COUNT(category.category_id) DESC
               LIMIT 3
           )
       ) as favorites_categories_source
    )  as favorites_categories 
FROM customer AS outer_query;



SELECT first_name, last_name,
       (
           SELECT category.name FROM customer
             INNER JOIN rental ON rental.customer_id = customer.customer_id
             INNER JOIN inventory ON inventory.inventory_id = rental.inventory_id
             INNER JOIN film ON film.film_id = inventory.film_id
             INNER JOIN film_category ON film_category.film_id = film.film_id
             INNER JOIN category ON category.category_id = film_category.category_id
           WHERE customer.customer_id = outer_query.customer_id
           GROUP BY category.category_id, category.name, customer.customer_id
           ORDER BY customer.customer_id, COUNT(category.category_id) DESC
           LIMIT 1
       )  as favorites_category_1,
       (
           SELECT category.name FROM customer
                                         INNER JOIN rental ON rental.customer_id = customer.customer_id
                                         INNER JOIN inventory ON inventory.inventory_id = rental.inventory_id
                                         INNER JOIN film ON film.film_id = inventory.film_id
                                         INNER JOIN film_category ON film_category.film_id = film.film_id
                                         INNER JOIN category ON category.category_id = film_category.category_id
           WHERE customer.customer_id = outer_query.customer_id
           GROUP BY category.category_id, category.name, customer.customer_id
           ORDER BY customer.customer_id, COUNT(category.category_id) DESC
           LIMIT 1, 1
       )  as favorites_category_2,
       (
           SELECT category.name FROM customer
                                         INNER JOIN rental ON rental.customer_id = customer.customer_id
                                         INNER JOIN inventory ON inventory.inventory_id = rental.inventory_id
                                         INNER JOIN film ON film.film_id = inventory.film_id
                                         INNER JOIN film_category ON film_category.film_id = film.film_id
                                         INNER JOIN category ON category.category_id = film_category.category_id
           WHERE customer.customer_id = outer_query.customer_id
           GROUP BY category.category_id, category.name, customer.customer_id
           ORDER BY customer.customer_id, COUNT(category.category_id) DESC
           LIMIT 2, 1 
       )  as favorites_category_3
    
FROM customer AS outer_query;


SELECT category.name, customer.customer_id as customer_id FROM customer
   INNER JOIN rental ON rental.customer_id = customer.customer_id
   INNER JOIN inventory ON inventory.inventory_id = rental.inventory_id
   INNER JOIN film ON film.film_id = inventory.film_id
   INNER JOIN film_category ON film_category.film_id = film.film_id
   INNER JOIN category ON category.category_id = film_category.category_id
GROUP BY category.category_id, category.name, customer.customer_id
ORDER BY customer.customer_id, COUNT(category.category_id) DESC

    


```

## Numero di film in ciascuno store

```sql

SELECT store_id, COUNT(inventory_id) AS disc, COUNT(DISTINCT(film_id)) AS film, 
       (SELECT COUNT(*) FROM film) AS totale_film_catalogo
       FROM inventory
GROUP BY store_id;

```

## Numero di film presenti nello store 1 ma non nel 2

```sql

SELECT DISTINCT inventory.film_id, film.title, 'PRESENTI SOLO IN 1' as comment FROM inventory
INNER JOIN film ON film.film_id = inventory.film_id
WHERE 
    inventory.store_id = 1 AND
    inventory.film_id NOT IN (
    SELECT DISTINCT film_id FROM inventory WHERE inventory.store_id = 2
)
UNION
SELECT DISTINCT inventory.film_id, film.title, 'PRESENTI SOLO IN 2' as comment FROM inventory
                                                                INNER JOIN film ON film.film_id = inventory.film_id
WHERE
    inventory.store_id = 2 AND
    inventory.film_id NOT IN (
        SELECT DISTINCT film_id FROM inventory WHERE inventory.store_id = 1
    )
;

```
## Film non presenti in nessuno store

```sql

SELECT film_id, title FROM film
WHERE film.film_id NOT IN (
    SELECT film_id FROM inventory
)

```
