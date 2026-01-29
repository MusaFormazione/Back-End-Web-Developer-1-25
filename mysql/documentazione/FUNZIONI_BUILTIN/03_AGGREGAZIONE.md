# 📊 Funzioni di Aggregazione

Le funzioni di aggregazione permettono di eseguire calcoli su gruppi di righe e sono fondamentali per analisi statistiche e reporting.

---

## 📋 Indice

### Funzioni Base
- [COUNT](#count)
- [SUM](#sum)
- [AVG](#avg)
- [MIN](#min)
- [MAX](#max)

### Funzioni Statistiche
- [STDDEV / STD](#stddev)
- [VARIANCE / VAR_POP](#variance)
- [STDDEV_POP](#stddev_pop)
- [STDDEV_SAMP](#stddev_samp)
- [VAR_SAMP](#var_samp)

### Funzioni di Posizione
- [GROUP_CONCAT](#group_concat_agg)
- [BIT_AND](#bit_and)
- [BIT_OR](#bit_or)
- [BIT_XOR](#bit_xor)

### Window Functions (Analitiche)
- [ROW_NUMBER](#row_number)
- [RANK](#rank)
- [DENSE_RANK](#dense_rank)
- [PERCENT_RANK](#percent_rank)
- [NTILE](#ntile)
- [FIRST_VALUE](#first_value)
- [LAST_VALUE](#last_value)
- [LAG](#lag)
- [LEAD](#lead)

---

## COUNT

🔥 **Funzione molto utilizzata**

**Sintassi:** `COUNT(*) | COUNT(expr) | COUNT(DISTINCT expr)`

**Descrizione:** Conta il numero di righe o valori non NULL.

**Esempio Sakila:**
```sql
-- Conteggi base
SELECT 
    COUNT(*) AS total_films,
    COUNT(description) AS films_with_description,
    COUNT(DISTINCT release_year) AS unique_release_years,
    COUNT(DISTINCT rating) AS unique_ratings
FROM film;
```

```sql
-- Analisi attività clienti
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(r.rental_id) AS total_rentals,
    COUNT(CASE WHEN r.return_date IS NULL THEN 1 END) AS active_rentals,
    COUNT(CASE WHEN YEAR(r.rental_date) = 2005 THEN 1 END) AS rentals_2005,
    COUNT(DISTINCT DATE(r.rental_date)) AS active_days
FROM customer c
LEFT JOIN rental r ON c.customer_id = r.customer_id
GROUP BY c.customer_id, c.first_name, c.last_name
ORDER BY total_rentals DESC
LIMIT 10;
```

```sql
-- Statistiche inventario per negozio
SELECT 
    s.store_id,
    COUNT(DISTINCT i.film_id) AS unique_films,
    COUNT(i.inventory_id) AS total_copies,
    COUNT(DISTINCT f.rating) AS ratings_available,
    COUNT(DISTINCT c.category_id) AS categories_available
FROM store s
JOIN inventory i ON s.store_id = i.store_id
JOIN film f ON i.film_id = f.film_id
JOIN film_category fc ON f.film_id = fc.film_id
JOIN category c ON fc.category_id = c.category_id
GROUP BY s.store_id;
```

---

## SUM

🔥 **Funzione molto utilizzata**

**Sintassi:** `SUM([DISTINCT] expr)`

**Descrizione:** Calcola la somma di tutti i valori numerici.

**Esempio Sakila:**
```sql
-- Revenue totale e per categoria
SELECT 
    SUM(amount) AS total_revenue,
    SUM(CASE WHEN amount > 5.00 THEN amount ELSE 0 END) AS premium_revenue,
    SUM(DISTINCT amount) AS unique_amounts_sum
FROM payment;
```

```sql
-- Analisi revenue per categoria film
SELECT 
    c.name AS category,
    COUNT(r.rental_id) AS total_rentals,
    SUM(p.amount) AS total_revenue,
    SUM(f.rental_rate * COUNT(r.rental_id)) AS potential_max_revenue,
    SUM(f.replacement_cost) AS total_replacement_cost
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
JOIN inventory i ON f.film_id = i.film_id
JOIN rental r ON i.inventory_id = r.inventory_id
JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.category_id, c.name
ORDER BY total_revenue DESC;
```

```sql
-- Performance finanziaria mensile
SELECT 
    YEAR(payment_date) AS year,
    MONTH(payment_date) AS month,
    MONTHNAME(payment_date) AS month_name,
    SUM(amount) AS monthly_revenue,
    SUM(SUM(amount)) OVER (
        PARTITION BY YEAR(payment_date) 
        ORDER BY MONTH(payment_date)
    ) AS running_total_year
FROM payment
GROUP BY YEAR(payment_date), MONTH(payment_date), MONTHNAME(payment_date)
ORDER BY year, month;
```

---

## AVG

🔥 **Funzione molto utilizzata**

**Sintassi:** `AVG([DISTINCT] expr)`

**Descrizione:** Calcola la media aritmetica dei valori.

**Esempio Sakila:**
```sql
-- Medie film per rating
SELECT 
    rating,
    COUNT(*) AS films_count,
    AVG(length) AS avg_duration_minutes,
    AVG(rental_rate) AS avg_rental_rate,
    AVG(replacement_cost) AS avg_replacement_cost,
    AVG(length) / 60 AS avg_duration_hours
FROM film
GROUP BY rating
ORDER BY avg_rental_rate DESC;
```

```sql
-- Performance media clienti
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(r.rental_id) AS total_rentals,
    AVG(p.amount) AS avg_payment,
    AVG(DATEDIFF(r.return_date, r.rental_date)) AS avg_rental_days,
    AVG(HOUR(r.rental_date)) AS avg_rental_hour
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
JOIN payment p ON r.rental_id = p.rental_id
WHERE r.return_date IS NOT NULL
GROUP BY c.customer_id, c.first_name, c.last_name
HAVING COUNT(r.rental_id) >= 20
ORDER BY avg_payment DESC
LIMIT 10;
```

```sql
-- Benchmark per categorie
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    AVG(f.length) AS avg_duration,
    AVG(f.rental_rate) AS avg_rate,
    AVG(f.length) - (
        SELECT AVG(length) FROM film
    ) AS duration_vs_overall_avg,
    CASE 
        WHEN AVG(f.rental_rate) > (SELECT AVG(rental_rate) FROM film) 
        THEN 'Above Average'
        ELSE 'Below Average'
    END AS rate_category
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY avg_rate DESC;
```

---

## MIN

🔥 **Funzione molto utilizzata**

**Sintassi:** `MIN(expr)`

**Descrizione:** Trova il valore minimo.

**Esempio Sakila:**
```sql
-- Range valori per ogni tabella
SELECT 
    'Films' AS entity,
    MIN(length) AS min_duration,
    MAX(length) AS max_duration,
    MIN(rental_rate) AS min_rate,
    MAX(rental_rate) AS max_rate,
    MIN(release_year) AS oldest_year,
    MAX(release_year) AS newest_year
FROM film

UNION ALL

SELECT 
    'Payments',
    NULL, NULL,
    MIN(amount),
    MAX(amount),
    MIN(YEAR(payment_date)),
    MAX(YEAR(payment_date))
FROM payment;
```

```sql
-- Prima e ultima attività per cliente
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    MIN(r.rental_date) AS first_rental,
    MAX(r.rental_date) AS last_rental,
    MIN(p.amount) AS min_payment,
    MAX(p.amount) AS max_payment,
    DATEDIFF(MAX(r.rental_date), MIN(r.rental_date)) AS customer_lifespan_days
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.customer_id, c.first_name, c.last_name
HAVING COUNT(r.rental_id) > 10
ORDER BY customer_lifespan_days DESC
LIMIT 10;
```

---

## MAX

🔥 **Funzione molto utilizzata**

**Sintassi:** `MAX(expr)`

**Descrizione:** Trova il valore massimo.

**Esempio Sakila:**
```sql
-- Film più lunghi per categoria
SELECT 
    c.name AS category,
    MAX(f.length) AS longest_film_minutes,
    MAX(f.length) / 60 AS longest_film_hours,
    f_longest.title AS longest_film_title
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
JOIN film f_longest ON f_longest.length = MAX(f.length) 
    AND f_longest.film_id IN (
        SELECT fc2.film_id 
        FROM film_category fc2 
        WHERE fc2.category_id = c.category_id
    )
GROUP BY c.category_id, c.name, f_longest.title
ORDER BY longest_film_minutes DESC;
```

---

## STDDEV

**Sintassi:** `STDDEV(expr)` o `STD(expr)`

**Descrizione:** Calcola la deviazione standard di un campione.

**Esempio Sakila:**
```sql
-- Variabilità prezzi e durate
SELECT 
    rating,
    COUNT(*) AS films_count,
    AVG(rental_rate) AS avg_rate,
    STDDEV(rental_rate) AS rate_stddev,
    AVG(length) AS avg_duration,
    STDDEV(length) AS duration_stddev,
    STDDEV(rental_rate) / AVG(rental_rate) * 100 AS rate_coefficient_variation
FROM film
GROUP BY rating
ORDER BY rate_coefficient_variation DESC;
```

```sql
-- Consistenza pagamenti clienti
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(p.payment_id) AS payments_count,
    AVG(p.amount) AS avg_payment,
    STDDEV(p.amount) AS payment_stddev,
    MIN(p.amount) AS min_payment,
    MAX(p.amount) AS max_payment,
    CASE 
        WHEN STDDEV(p.amount) / AVG(p.amount) < 0.3 THEN 'Consistent'
        WHEN STDDEV(p.amount) / AVG(p.amount) < 0.6 THEN 'Moderate'
        ELSE 'Variable'
    END AS payment_pattern
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.customer_id, c.first_name, c.last_name
HAVING COUNT(p.payment_id) >= 15
ORDER BY payment_stddev DESC
LIMIT 10;
```

---

## VARIANCE

**Sintassi:** `VARIANCE(expr)` o `VAR_POP(expr)`

**Descrizione:** Calcola la varianza di popolazione.

**Esempio Sakila:**
```sql
-- Varianza revenue per store
SELECT 
    s.store_id,
    COUNT(p.payment_id) AS total_payments,
    AVG(p.amount) AS avg_payment,
    VARIANCE(p.amount) AS payment_variance,
    SQRT(VARIANCE(p.amount)) AS payment_stddev
FROM store s
JOIN staff st ON s.store_id = st.store_id
JOIN payment p ON st.staff_id = p.staff_id
GROUP BY s.store_id;
```

---

## ROW_NUMBER

🔥 **Funzione molto utilizzata (Window Function)**

**Sintassi:** `ROW_NUMBER() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Assegna un numero sequenziale a ogni riga all'interno di una partizione.

**Esempio Sakila:**
```sql
-- Ranking film per durata
SELECT 
    film_id,
    title,
    length,
    rating,
    ROW_NUMBER() OVER (ORDER BY length DESC) AS overall_rank,
    ROW_NUMBER() OVER (PARTITION BY rating ORDER BY length DESC) AS rank_in_rating
FROM film
ORDER BY length DESC
LIMIT 15;
```

```sql
-- Top 3 clienti per revenue per store
SELECT 
    store_id,
    customer_id,
    customer_name,
    total_spent,
    customer_rank
FROM (
    SELECT 
        st.store_id,
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        SUM(p.amount) AS total_spent,
        ROW_NUMBER() OVER (PARTITION BY st.store_id ORDER BY SUM(p.amount) DESC) AS customer_rank
    FROM customer c
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    JOIN staff st ON p.staff_id = st.staff_id
    GROUP BY st.store_id, c.customer_id, c.first_name, c.last_name
) ranked_customers
WHERE customer_rank <= 3
ORDER BY store_id, customer_rank;
```

---

## RANK

🔥 **Funzione molto utilizzata (Window Function)**

**Sintassi:** `RANK() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Assegna rank con gap per valori uguali.

**Esempio Sakila:**
```sql
-- Ranking film con gestione pareggi
SELECT 
    film_id,
    title,
    rental_rate,
    RANK() OVER (ORDER BY rental_rate DESC) AS price_rank,
    DENSE_RANK() OVER (ORDER BY rental_rate DESC) AS price_dense_rank,
    ROW_NUMBER() OVER (ORDER BY rental_rate DESC, title) AS row_num
FROM film
ORDER BY rental_rate DESC, title
LIMIT 20;
```

```sql
-- Performance ranking staff
SELECT 
    s.staff_id,
    CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
    COUNT(p.payment_id) AS transactions_processed,
    SUM(p.amount) AS total_revenue,
    RANK() OVER (ORDER BY SUM(p.amount) DESC) AS revenue_rank,
    RANK() OVER (ORDER BY COUNT(p.payment_id) DESC) AS transaction_rank
FROM staff s
JOIN payment p ON s.staff_id = p.staff_id
GROUP BY s.staff_id, s.first_name, s.last_name
ORDER BY total_revenue DESC;
```

---

## DENSE_RANK

**Sintassi:** `DENSE_RANK() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Assegna rank senza gap per valori uguali.

**Esempio Sakila:**
```sql
-- Dense ranking categorie per popolarità
SELECT 
    c.name AS category,
    COUNT(r.rental_id) AS rental_count,
    DENSE_RANK() OVER (ORDER BY COUNT(r.rental_id) DESC) AS popularity_rank
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
JOIN inventory i ON f.film_id = i.film_id
JOIN rental r ON i.inventory_id = r.inventory_id
GROUP BY c.category_id, c.name
ORDER BY rental_count DESC;
```

---

## NTILE

**Sintassi:** `NTILE(n) OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Divide i risultati in n gruppi (tiles) approssimativamente uguali.

**Esempio Sakila:**
```sql
-- Segmentazione clienti in quartili
SELECT 
    customer_id,
    customer_name,
    total_spent,
    customer_quartile,
    CASE customer_quartile
        WHEN 1 THEN 'Top 25% (Premium)'
        WHEN 2 THEN 'High Value'
        WHEN 3 THEN 'Medium Value'
        WHEN 4 THEN 'Low Value'
    END AS segment_name
FROM (
    SELECT 
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        SUM(p.amount) AS total_spent,
        NTILE(4) OVER (ORDER BY SUM(p.amount) DESC) AS customer_quartile
    FROM customer c
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY c.customer_id, c.first_name, c.last_name
) customer_segments
ORDER BY total_spent DESC;
```

```sql
-- Decili film per durata
SELECT 
    title,
    length,
    NTILE(10) OVER (ORDER BY length) AS duration_decile,
    CASE NTILE(10) OVER (ORDER BY length)
        WHEN 1 THEN 'Shortest 10%'
        WHEN 10 THEN 'Longest 10%'
        ELSE CONCAT('Decile ', NTILE(10) OVER (ORDER BY length))
    END AS duration_category
FROM film
ORDER BY length;
```

---

## LAG / LEAD

**Sintassi:** `LAG(expr [, offset [, default]]) OVER (...)` / `LEAD(expr [, offset [, default]]) OVER (...)`

**Descrizione:** Accede al valore di una riga precedente (LAG) o successiva (LEAD).

**Esempio Sakila:**
```sql
-- Analisi trend pagamenti mensili
SELECT 
    payment_month,
    monthly_revenue,
    previous_month_revenue,
    monthly_revenue - previous_month_revenue AS revenue_change,
    CASE 
        WHEN previous_month_revenue IS NULL THEN 'N/A'
        WHEN monthly_revenue > previous_month_revenue THEN 'Growth'
        WHEN monthly_revenue < previous_month_revenue THEN 'Decline'
        ELSE 'Stable'
    END AS trend
FROM (
    SELECT 
        DATE_FORMAT(payment_date, '%Y-%m') AS payment_month,
        SUM(amount) AS monthly_revenue,
        LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS previous_month_revenue
    FROM payment
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY payment_month
) monthly_trends;
```

```sql
-- Intervalli tra noleggi per cliente
SELECT 
    customer_id,
    rental_date,
    next_rental_date,
    DATEDIFF(next_rental_date, rental_date) AS days_between_rentals
FROM (
    SELECT 
        customer_id,
        rental_date,
        LEAD(rental_date) OVER (PARTITION BY customer_id ORDER BY rental_date) AS next_rental_date
    FROM rental
) rental_intervals
WHERE next_rental_date IS NOT NULL
  AND customer_id <= 10
ORDER BY customer_id, rental_date;
```

---

## FIRST_VALUE / LAST_VALUE

**Sintassi:** `FIRST_VALUE(expr) OVER (...)` / `LAST_VALUE(expr) OVER (...)`

**Descrizione:** Restituisce il primo o ultimo valore nella finestra.

**Esempio Sakila:**
```sql
-- Confronto con primo e ultimo noleggio per categoria
SELECT 
    c.name AS category,
    f.title,
    r.rental_date,
    FIRST_VALUE(f.title) OVER (
        PARTITION BY c.category_id 
        ORDER BY r.rental_date 
        ROWS UNBOUNDED PRECEDING
    ) AS first_rented_film,
    LAST_VALUE(f.title) OVER (
        PARTITION BY c.category_id 
        ORDER BY r.rental_date 
        ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
    ) AS last_rented_film
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
JOIN inventory i ON f.film_id = i.film_id
JOIN rental r ON i.inventory_id = r.inventory_id
WHERE c.category_id <= 5
ORDER BY c.name, r.rental_date
LIMIT 50;
```

---

## Esempi Pratici Combinati

### Dashboard Completo Performance
```sql
-- Dashboard performance completo con multiple aggregazioni
SELECT 
    'Overall Statistics' AS metric_type,
    COUNT(DISTINCT c.customer_id) AS total_customers,
    COUNT(r.rental_id) AS total_rentals,
    SUM(p.amount) AS total_revenue,
    AVG(p.amount) AS avg_payment,
    STDDEV(p.amount) AS payment_stddev,
    MIN(p.amount) AS min_payment,
    MAX(p.amount) AS max_payment
FROM customer c
LEFT JOIN rental r ON c.customer_id = r.customer_id
LEFT JOIN payment p ON r.rental_id = p.rental_id

UNION ALL

SELECT 
    'Film Statistics',
    NULL,
    COUNT(*),
    SUM(replacement_cost),
    AVG(rental_rate),
    STDDEV(rental_rate),
    MIN(rental_rate),
    MAX(rental_rate)
FROM film;
```

### Analisi Cohort con Window Functions
```sql
-- Analisi cohort clienti per mese di registrazione
SELECT 
    registration_month,
    customers_count,
    total_revenue,
    avg_revenue_per_customer,
    cumulative_customers,
    revenue_rank,
    CASE 
        WHEN revenue_rank <= 3 THEN 'Top Performing'
        WHEN revenue_rank <= 6 THEN 'Above Average'
        ELSE 'Standard'
    END AS cohort_performance
FROM (
    SELECT 
        DATE_FORMAT(c.create_date, '%Y-%m') AS registration_month,
        COUNT(DISTINCT c.customer_id) AS customers_count,
        COALESCE(SUM(p.amount), 0) AS total_revenue,
        COALESCE(SUM(p.amount) / COUNT(DISTINCT c.customer_id), 0) AS avg_revenue_per_customer,
        SUM(COUNT(DISTINCT c.customer_id)) OVER (ORDER BY DATE_FORMAT(c.create_date, '%Y-%m')) AS cumulative_customers,
        RANK() OVER (ORDER BY COALESCE(SUM(p.amount), 0) DESC) AS revenue_rank
    FROM customer c
    LEFT JOIN rental r ON c.customer_id = r.customer_id
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY DATE_FORMAT(c.create_date, '%Y-%m')
) cohort_analysis
ORDER BY registration_month;
```

### Segmentazione Avanzata Clienti
```sql
-- Segmentazione RFM semplificata (Recency, Frequency, Monetary)
SELECT 
    customer_id,
    customer_name,
    days_since_last_rental,
    total_rentals,
    total_spent,
    NTILE(5) OVER (ORDER BY days_since_last_rental) AS recency_score,
    NTILE(5) OVER (ORDER BY total_rentals DESC) AS frequency_score,
    NTILE(5) OVER (ORDER BY total_spent DESC) AS monetary_score,
    CASE 
        WHEN NTILE(5) OVER (ORDER BY total_spent DESC) <= 2 
         AND NTILE(5) OVER (ORDER BY total_rentals DESC) <= 2 
        THEN 'Champions'
        WHEN NTILE(5) OVER (ORDER BY days_since_last_rental) >= 4 
        THEN 'At Risk'
        WHEN NTILE(5) OVER (ORDER BY total_rentals DESC) >= 4 
        THEN 'Hibernating'
        ELSE 'Regular'
    END AS customer_segment
FROM (
    SELECT 
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        DATEDIFF(CURDATE(), MAX(r.rental_date)) AS days_since_last_rental,
        COUNT(r.rental_id) AS total_rentals,
        SUM(p.amount) AS total_spent
    FROM customer c
    LEFT JOIN rental r ON c.customer_id = r.customer_id
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY c.customer_id, c.first_name, c.last_name
    HAVING COUNT(r.rental_id) > 0
) customer_metrics
ORDER BY total_spent DESC;
```
