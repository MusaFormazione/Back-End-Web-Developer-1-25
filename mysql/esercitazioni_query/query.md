# Esercitazione 1

## Visualizza ciascun customer con [città] e country

```sql

SELECT customer_id, first_name, last_name, email, 
       CONCAT(city.city,' (', country.country, ')') AS city
FROM customer
INNER JOIN address ON customer.address_id = address.address_id
INNER JOIN city ON city.city_id = address.city_id
INNER JOIN country ON country.country_id = city.country_id;

```

## Quanti film ha fatto ciascun attore

```sql

SELECT first_name, last_name, count(film_actor.film_id) as films FROM actor
LEFT JOIN film_actor ON actor.actor_id = film_actor.actor_id
GROUP BY actor.actor_id
ORDER BY films;

```

## 3 Nazioni con più customer + italia

```sql
(
    SELECT country, count(address.address_id) as customers FROM country
    INNER JOIN city ON city.country_id = country.country_id
    INNER JOIN address ON city.city_id = address.city_id
    GROUP BY country.country_id
    ORDER BY customers DESC
    LIMIT 3
)
UNION
    SELECT country, count(address.address_id) as customers FROM country
    INNER JOIN city ON city.country_id = country.country_id
    INNER JOIN address ON city.city_id = address.city_id
    WHERE country = 'Italy'
    GROUP BY country.country_id;
```


## Ottenere lo store che ha incassato di più, con i dati del manager

```sql

SELECT store.store_id, manager.first_name, manager.last_name, 
       COUNT(payment_id) AS payment_count, 
       SUM(payment.amount) AS total_amount,
       COUNT(DISTINCT(payment.customer_id)) AS customer
FROM payment 
INNER JOIN staff ON staff.staff_id = payment.staff_id
INNER JOIN store ON store.store_id = staff.store_id
INNER JOIN staff AS manager ON manager.staff_id = store.manager_staff_id
GROUP BY store.store_id
ORDER BY total_amount DESC;
```
