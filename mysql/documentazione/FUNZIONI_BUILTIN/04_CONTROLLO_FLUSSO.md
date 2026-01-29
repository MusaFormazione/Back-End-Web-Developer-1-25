# 🔀 Funzioni Controllo Flusso

Le funzioni di controllo flusso permettono di implementare logica condizionale nelle query SQL, rendendo possibili elaborazioni complesse e trasformazioni dinamiche dei dati.

---

## 📋 Indice

### Funzioni Condizionali Base
- [CASE](#case)
- [IF](#if)
- [IFNULL](#ifnull)
- [NULLIF](#nullif)
- [ISNULL](#isnull)

### Funzioni di Coalescenza
- [COALESCE](#coalesce)
- [GREATEST](#greatest)
- [LEAST](#least)

### Funzioni di Validazione
- [IS NULL / IS NOT NULL](#is_null)
- [ISNULL](#isnull_func)

---

## CASE

🔥 **Funzione molto utilizzata**

**Sintassi:** 
```sql
CASE 
    WHEN condition1 THEN result1
    WHEN condition2 THEN result2
    ...
    ELSE default_result
END

-- O la forma semplice:
CASE expr
    WHEN value1 THEN result1
    WHEN value2 THEN result2
    ...
    ELSE default_result
END
```

**Descrizione:** Implementa logica condizionale complessa simile a if-else in altri linguaggi.

**Esempio Sakila:**
```sql
-- Categorizzazione film per durata
SELECT 
    film_id,
    title,
    length,
    CASE 
        WHEN length <= 60 THEN 'Cortometraggio'
        WHEN length <= 90 THEN 'Breve'
        WHEN length <= 120 THEN 'Standard'
        WHEN length <= 150 THEN 'Lungo'
        ELSE 'Molto Lungo'
    END AS duration_category,
    CASE rating
        WHEN 'G' THEN 'Per tutti'
        WHEN 'PG' THEN 'Supervisione consigliata'
        WHEN 'PG-13' THEN 'Supervisione per under 13'
        WHEN 'R' THEN 'Limitato'
        WHEN 'NC-17' THEN 'Solo adulti'
        ELSE 'Non classificato'
    END AS rating_description
FROM film
LIMIT 10;
```

```sql
-- Analisi performance clienti
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(r.rental_id) AS total_rentals,
    SUM(p.amount) AS total_spent,
    CASE 
        WHEN COUNT(r.rental_id) = 0 THEN 'Inattivo'
        WHEN COUNT(r.rental_id) <= 10 THEN 'Occasionale'
        WHEN COUNT(r.rental_id) <= 25 THEN 'Regolare'
        WHEN COUNT(r.rental_id) <= 40 THEN 'Frequente'
        ELSE 'VIP'
    END AS customer_tier,
    CASE 
        WHEN SUM(p.amount) >= 180 THEN 'Alto Valore'
        WHEN SUM(p.amount) >= 120 THEN 'Medio Valore'
        WHEN SUM(p.amount) >= 60 THEN 'Basso Valore'
        ELSE 'Valore Minimo'
    END AS value_segment,
    CASE 
        WHEN c.active = 1 THEN '✅ Attivo'
        ELSE '❌ Disattivato'
    END AS status
FROM customer c
LEFT JOIN rental r ON c.customer_id = r.customer_id
LEFT JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.customer_id, c.first_name, c.last_name, c.active
ORDER BY total_rentals DESC
LIMIT 15;
```

```sql
-- Dashboard seasonale con CASE nidificato
SELECT 
    CASE QUARTER(rental_date)
        WHEN 1 THEN 'Q1 - Inverno'
        WHEN 2 THEN 'Q2 - Primavera'
        WHEN 3 THEN 'Q3 - Estate'
        WHEN 4 THEN 'Q4 - Autunno'
    END AS season,
    COUNT(*) AS total_rentals,
    COUNT(CASE WHEN DAYOFWEEK(rental_date) IN (1,7) THEN 1 END) AS weekend_rentals,
    COUNT(CASE WHEN HOUR(rental_date) BETWEEN 18 AND 22 THEN 1 END) AS evening_rentals,
    CASE 
        WHEN COUNT(*) >= 4000 THEN '🔥 Alta Domanda'
        WHEN COUNT(*) >= 3000 THEN '📈 Media Domanda'
        ELSE '📉 Bassa Domanda'
    END AS demand_level
FROM rental
GROUP BY QUARTER(rental_date)
ORDER BY QUARTER(rental_date);
```

---

## IF

🔥 **Funzione molto utilizzata**

**Sintassi:** `IF(condition, value_if_true, value_if_false)`

**Descrizione:** Funzione condizionale semplice per valutazioni booleane.

**Esempio Sakila:**
```sql
-- Status noleggi attuali
SELECT 
    rental_id,
    customer_id,
    rental_date,
    return_date,
    IF(return_date IS NULL, 'In corso', 'Completato') AS rental_status,
    IF(return_date IS NULL, 
       DATEDIFF(CURDATE(), rental_date), 
       DATEDIFF(return_date, rental_date)
    ) AS days_duration,
    IF(DATEDIFF(CURDATE(), rental_date) > 7 AND return_date IS NULL, 
       '⚠️ In ritardo', 
       '✅ OK'
    ) AS late_status
FROM rental
ORDER BY rental_date DESC
LIMIT 10;
```

```sql
-- Pricing dinamico
SELECT 
    f.film_id,
    f.title,
    f.rental_rate AS base_rate,
    IF(f.rating IN ('R', 'NC-17'), 
       f.rental_rate * 1.2, 
       f.rental_rate
    ) AS adjusted_rate,
    IF(f.length > 120, 
       'Extended', 
       'Standard'
    ) AS length_category,
    IF(c.name IN ('Action', 'Sci-Fi', 'Horror'), 
       '🎬 Premium Genre', 
       '📺 Standard Genre'
    ) AS genre_tier
FROM film f
JOIN film_category fc ON f.film_id = fc.film_id
JOIN category c ON fc.category_id = c.category_id
LIMIT 10;
```

```sql
-- Performance comparison
SELECT 
    s.staff_id,
    CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
    COUNT(p.payment_id) AS transactions,
    SUM(p.amount) AS total_revenue,
    IF(COUNT(p.payment_id) > 8000, 'Top Performer', 'Standard') AS performance_level,
    IF(s.active = 1, '🟢 Active', '🔴 Inactive') AS status
FROM staff s
LEFT JOIN payment p ON s.staff_id = p.staff_id
GROUP BY s.staff_id, s.first_name, s.last_name, s.active;
```

---

## IFNULL

🔥 **Funzione molto utilizzata**

**Sintassi:** `IFNULL(expr, alternative)`

**Descrizione:** Sostituisce NULL con un valore alternativo.

**Esempio Sakila:**
```sql
-- Gestione valori NULL in report
SELECT 
    customer_id,
    first_name,
    last_name,
    email,
    IFNULL(email, 'Email non disponibile') AS email_display,
    create_date,
    IFNULL(last_update, create_date) AS effective_update_date,
    active,
    IFNULL(active, 0) AS active_status
FROM customer
LIMIT 10;
```

```sql
-- Calcoli con gestione NULL
SELECT 
    f.film_id,
    f.title,
    IFNULL(f.description, 'Nessuna descrizione disponibile') AS description_safe,
    f.rental_rate,
    f.replacement_cost,
    IFNULL(f.replacement_cost, f.rental_rate * 20) AS cost_estimate,
    IFNULL(f.special_features, 'Nessuna caratteristica speciale') AS features
FROM film
WHERE f.description IS NULL OR f.special_features IS NULL
LIMIT 5;
```

```sql
-- Aggregazioni sicure con NULL
SELECT 
    c.category_id,
    c.name,
    COUNT(f.film_id) AS films_count,
    AVG(IFNULL(f.length, 90)) AS avg_duration_safe,
    SUM(IFNULL(f.replacement_cost, 20)) AS total_replacement_safe,
    IFNULL(MIN(f.rental_rate), 0.99) AS min_rate_safe
FROM category c
LEFT JOIN film_category fc ON c.category_id = fc.category_id
LEFT JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY films_count DESC;
```

---

## COALESCE

🔥 **Funzione molto utilizzata**

**Sintassi:** `COALESCE(value1, value2, value3, ..., default_value)`

**Descrizione:** Restituisce il primo valore non NULL dalla lista.

**Esempio Sakila:**
```sql
-- Fallback multipli per dati mancanti
SELECT 
    a.actor_id,
    COALESCE(a.first_name, 'Nome', 'Sconosciuto') AS safe_first_name,
    COALESCE(a.last_name, 'Cognome', 'Sconosciuto') AS safe_last_name,
    COALESCE(
        CONCAT(a.first_name, ' ', a.last_name),
        a.first_name,
        a.last_name,
        CONCAT('Actor #', a.actor_id)
    ) AS display_name
FROM actor a
LIMIT 10;
```

```sql
-- Pricing con fallback multipli
SELECT 
    f.film_id,
    f.title,
    f.rental_rate,
    f.replacement_cost,
    COALESCE(
        f.rental_rate,
        f.replacement_cost / 20,
        2.99
    ) AS effective_rental_rate,
    COALESCE(
        f.description,
        CONCAT('Film: ', f.title),
        'Nessuna informazione disponibile'
    ) AS safe_description
FROM film
LIMIT 10;
```

```sql
-- Contatti customer con priorità
SELECT 
    c.customer_id,
    c.first_name,
    c.last_name,
    c.email,
    a.phone,
    COALESCE(
        c.email,
        a.phone,
        CONCAT('Customer ID: ', c.customer_id),
        'Contatto non disponibile'
    ) AS primary_contact,
    COALESCE(
        a.address,
        'Indirizzo sconosciuto'
    ) AS safe_address
FROM customer c
LEFT JOIN address a ON c.address_id = a.address_id
LIMIT 10;
```

---

## NULLIF

**Sintassi:** `NULLIF(expr1, expr2)`

**Descrizione:** Restituisce NULL se expr1 = expr2, altrimenti restituisce expr1.

**Esempio Sakila:**
```sql
-- Pulizia dati con NULLIF
SELECT 
    film_id,
    title,
    rental_rate,
    replacement_cost,
    NULLIF(rental_rate, 0) AS rental_rate_clean,
    NULLIF(length, 0) AS length_clean,
    NULLIF(TRIM(description), '') AS description_clean
FROM film
LIMIT 10;
```

```sql
-- Calcoli sicuri evitando divisioni per zero
SELECT 
    c.category_id,
    c.name,
    COUNT(f.film_id) AS films_count,
    SUM(f.replacement_cost) AS total_cost,
    AVG(f.rental_rate) AS avg_rate,
    SUM(f.replacement_cost) / NULLIF(COUNT(f.film_id), 0) AS avg_cost_per_film,
    SUM(f.rental_rate) / NULLIF(SUM(f.replacement_cost), 0) AS rate_to_cost_ratio
FROM category c
LEFT JOIN film_category fc ON c.category_id = fc.category_id
LEFT JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
HAVING COUNT(f.film_id) > 0;
```

---

## GREATEST

**Sintassi:** `GREATEST(value1, value2, ...)`

**Descrizione:** Restituisce il valore più grande tra quelli forniti.

**Esempio Sakila:**
```sql
-- Confronti multipli
SELECT 
    film_id,
    title,
    rental_rate,
    replacement_cost,
    length,
    GREATEST(rental_rate, 2.99) AS min_rental_rate,
    GREATEST(
        rental_rate * 10,
        replacement_cost * 0.8,
        15.00
    ) AS max_value_estimate,
    GREATEST(length, 90) AS min_duration
FROM film
LIMIT 10;
```

```sql
-- Analisi performance staff
SELECT 
    s.staff_id,
    CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
    COUNT(DISTINCT r.rental_id) AS rentals_handled,
    COUNT(DISTINCT p.payment_id) AS payments_processed,
    SUM(p.amount) AS total_revenue,
    GREATEST(
        COUNT(DISTINCT r.rental_id),
        COUNT(DISTINCT p.payment_id)
    ) AS max_activity,
    GREATEST(
        COUNT(DISTINCT r.rental_id) / 30,
        COUNT(DISTINCT p.payment_id) / 30,
        1
    ) AS daily_activity_rate
FROM staff s
LEFT JOIN rental r ON s.staff_id = r.staff_id
LEFT JOIN payment p ON s.staff_id = p.staff_id
GROUP BY s.staff_id, s.first_name, s.last_name;
```

---

## LEAST

**Sintassi:** `LEAST(value1, value2, ...)`

**Descrizione:** Restituisce il valore più piccolo tra quelli forniti.

**Esempio Sakila:**
```sql
-- Pricing ottimizzato
SELECT 
    film_id,
    title,
    rental_rate,
    replacement_cost,
    LEAST(rental_rate, 4.99) AS capped_rental_rate,
    LEAST(
        replacement_cost,
        rental_rate * 25,
        29.99
    ) AS adjusted_replacement_cost,
    LEAST(length, 180) AS max_viewing_time
FROM film
ORDER BY rental_rate DESC
LIMIT 10;
```

```sql
-- Budget constraints per categoria
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    AVG(f.rental_rate) AS avg_rental_rate,
    AVG(f.replacement_cost) AS avg_replacement_cost,
    LEAST(AVG(f.rental_rate), 3.99) AS budget_friendly_rate,
    LEAST(
        MAX(f.replacement_cost),
        AVG(f.replacement_cost) * 1.5,
        25.00
    ) AS max_budget_per_film
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY avg_rental_rate DESC;
```

---

## Esempi Pratici Combinati

### Sistema di Scoring Complesso
```sql
-- Sistema di scoring clienti complesso
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(r.rental_id) AS total_rentals,
    SUM(IFNULL(p.amount, 0)) AS total_spent,
    AVG(IFNULL(DATEDIFF(r.return_date, r.rental_date), 7)) AS avg_rental_days,
    
    -- Frequency Score (0-100)
    CASE 
        WHEN COUNT(r.rental_id) >= 40 THEN 100
        WHEN COUNT(r.rental_id) >= 30 THEN 80
        WHEN COUNT(r.rental_id) >= 20 THEN 60
        WHEN COUNT(r.rental_id) >= 10 THEN 40
        WHEN COUNT(r.rental_id) >= 5 THEN 20
        ELSE 0
    END AS frequency_score,
    
    -- Monetary Score (0-100)
    CASE 
        WHEN SUM(IFNULL(p.amount, 0)) >= 200 THEN 100
        WHEN SUM(IFNULL(p.amount, 0)) >= 150 THEN 80
        WHEN SUM(IFNULL(p.amount, 0)) >= 100 THEN 60
        WHEN SUM(IFNULL(p.amount, 0)) >= 50 THEN 40
        WHEN SUM(IFNULL(p.amount, 0)) >= 25 THEN 20
        ELSE 0
    END AS monetary_score,
    
    -- Recency Score (0-100)
    CASE 
        WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 30 THEN 100
        WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 60 THEN 80
        WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 90 THEN 60
        WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 180 THEN 40
        WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 365 THEN 20
        ELSE 0
    END AS recency_score,
    
    -- Overall Score
    GREATEST(
        (CASE 
            WHEN COUNT(r.rental_id) >= 40 THEN 100
            WHEN COUNT(r.rental_id) >= 30 THEN 80
            WHEN COUNT(r.rental_id) >= 20 THEN 60
            WHEN COUNT(r.rental_id) >= 10 THEN 40
            WHEN COUNT(r.rental_id) >= 5 THEN 20
            ELSE 0
        END +
        CASE 
            WHEN SUM(IFNULL(p.amount, 0)) >= 200 THEN 100
            WHEN SUM(IFNULL(p.amount, 0)) >= 150 THEN 80
            WHEN SUM(IFNULL(p.amount, 0)) >= 100 THEN 60
            WHEN SUM(IFNULL(p.amount, 0)) >= 50 THEN 40
            WHEN SUM(IFNULL(p.amount, 0)) >= 25 THEN 20
            ELSE 0
        END +
        CASE 
            WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 30 THEN 100
            WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 60 THEN 80
            WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 90 THEN 60
            WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 180 THEN 40
            WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 365 THEN 20
            ELSE 0
        END) / 3,
        0
    ) AS overall_score,
    
    -- Final Tier
    CASE 
        WHEN GREATEST(
            (CASE WHEN COUNT(r.rental_id) >= 40 THEN 100 WHEN COUNT(r.rental_id) >= 30 THEN 80 WHEN COUNT(r.rental_id) >= 20 THEN 60 WHEN COUNT(r.rental_id) >= 10 THEN 40 WHEN COUNT(r.rental_id) >= 5 THEN 20 ELSE 0 END +
             CASE WHEN SUM(IFNULL(p.amount, 0)) >= 200 THEN 100 WHEN SUM(IFNULL(p.amount, 0)) >= 150 THEN 80 WHEN SUM(IFNULL(p.amount, 0)) >= 100 THEN 60 WHEN SUM(IFNULL(p.amount, 0)) >= 50 THEN 40 WHEN SUM(IFNULL(p.amount, 0)) >= 25 THEN 20 ELSE 0 END +
             CASE WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 30 THEN 100 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 60 THEN 80 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 90 THEN 60 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 180 THEN 40 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 365 THEN 20 ELSE 0 END) / 3,
            0
        ) >= 80 THEN '🥇 Champions'
        WHEN GREATEST(
            (CASE WHEN COUNT(r.rental_id) >= 40 THEN 100 WHEN COUNT(r.rental_id) >= 30 THEN 80 WHEN COUNT(r.rental_id) >= 20 THEN 60 WHEN COUNT(r.rental_id) >= 10 THEN 40 WHEN COUNT(r.rental_id) >= 5 THEN 20 ELSE 0 END +
             CASE WHEN SUM(IFNULL(p.amount, 0)) >= 200 THEN 100 WHEN SUM(IFNULL(p.amount, 0)) >= 150 THEN 80 WHEN SUM(IFNULL(p.amount, 0)) >= 100 THEN 60 WHEN SUM(IFNULL(p.amount, 0)) >= 50 THEN 40 WHEN SUM(IFNULL(p.amount, 0)) >= 25 THEN 20 ELSE 0 END +
             CASE WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 30 THEN 100 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 60 THEN 80 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 90 THEN 60 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 180 THEN 40 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 365 THEN 20 ELSE 0 END) / 3,
            0
        ) >= 60 THEN '🥈 Loyal'
        WHEN GREATEST(
            (CASE WHEN COUNT(r.rental_id) >= 40 THEN 100 WHEN COUNT(r.rental_id) >= 30 THEN 80 WHEN COUNT(r.rental_id) >= 20 THEN 60 WHEN COUNT(r.rental_id) >= 10 THEN 40 WHEN COUNT(r.rental_id) >= 5 THEN 20 ELSE 0 END +
             CASE WHEN SUM(IFNULL(p.amount, 0)) >= 200 THEN 100 WHEN SUM(IFNULL(p.amount, 0)) >= 150 THEN 80 WHEN SUM(IFNULL(p.amount, 0)) >= 100 THEN 60 WHEN SUM(IFNULL(p.amount, 0)) >= 50 THEN 40 WHEN SUM(IFNULL(p.amount, 0)) >= 25 THEN 20 ELSE 0 END +
             CASE WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 30 THEN 100 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 60 THEN 80 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 90 THEN 60 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 180 THEN 40 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 365 THEN 20 ELSE 0 END) / 3,
            0
        ) >= 40 THEN '🥉 Regular'
        WHEN GREATEST(
            (CASE WHEN COUNT(r.rental_id) >= 40 THEN 100 WHEN COUNT(r.rental_id) >= 30 THEN 80 WHEN COUNT(r.rental_id) >= 20 THEN 60 WHEN COUNT(r.rental_id) >= 10 THEN 40 WHEN COUNT(r.rental_id) >= 5 THEN 20 ELSE 0 END +
             CASE WHEN SUM(IFNULL(p.amount, 0)) >= 200 THEN 100 WHEN SUM(IFNULL(p.amount, 0)) >= 150 THEN 80 WHEN SUM(IFNULL(p.amount, 0)) >= 100 THEN 60 WHEN SUM(IFNULL(p.amount, 0)) >= 50 THEN 40 WHEN SUM(IFNULL(p.amount, 0)) >= 25 THEN 20 ELSE 0 END +
             CASE WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 30 THEN 100 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 60 THEN 80 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 90 THEN 60 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 180 THEN 40 WHEN DATEDIFF(CURDATE(), MAX(r.rental_date)) <= 365 THEN 20 ELSE 0 END) / 3,
            0
        ) >= 20 THEN '📉 At Risk'
        ELSE '😴 Hibernating'
    END AS customer_tier

FROM customer c
LEFT JOIN rental r ON c.customer_id = r.customer_id
LEFT JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.customer_id, c.first_name, c.last_name
ORDER BY overall_score DESC
LIMIT 20;
```

### Dashboard Operativo Completo
```sql
-- Dashboard operativo con logica di business complessa
SELECT 
    'Store Performance' AS metric_category,
    st.store_id AS identifier,
    CONCAT('Store #', st.store_id) AS display_name,
    COUNT(DISTINCT r.rental_id) AS total_transactions,
    COUNT(DISTINCT r.customer_id) AS unique_customers,
    SUM(p.amount) AS total_revenue,
    
    CASE 
        WHEN COUNT(DISTINCT r.rental_id) >= 8000 THEN 'Excellent'
        WHEN COUNT(DISTINCT r.rental_id) >= 6000 THEN 'Good'
        WHEN COUNT(DISTINCT r.rental_id) >= 4000 THEN 'Average'
        ELSE 'Below Average'
    END AS performance_rating,
    
    IF(SUM(p.amount) >= 40000, '🎯 Target Reached', '📈 Below Target') AS revenue_status,
    
    COALESCE(
        CASE 
            WHEN AVG(p.amount) >= 5.00 THEN 'Premium'
            WHEN AVG(p.amount) >= 4.00 THEN 'Standard'
            ELSE 'Budget'
        END,
        'No Data'
    ) AS price_tier

FROM store st
LEFT JOIN staff s ON st.store_id = s.store_id
LEFT JOIN rental r ON s.staff_id = r.staff_id
LEFT JOIN payment p ON r.rental_id = p.rental_id
GROUP BY st.store_id

UNION ALL

SELECT 
    'Category Performance',
    c.category_id,
    c.name,
    COUNT(DISTINCT r.rental_id),
    COUNT(DISTINCT r.customer_id),
    SUM(p.amount),
    
    CASE 
        WHEN COUNT(DISTINCT r.rental_id) >= 1000 THEN 'Top Performer'
        WHEN COUNT(DISTINCT r.rental_id) >= 700 THEN 'Strong'
        WHEN COUNT(DISTINCT r.rental_id) >= 400 THEN 'Moderate'
        ELSE 'Weak'
    END,
    
    IF(AVG(f.rental_rate) >= 3.00, '💰 Premium Category', '📺 Standard Category'),
    
    NULLIF(
        CASE 
            WHEN AVG(f.length) >= 120 THEN 'Long Form'
            WHEN AVG(f.length) >= 90 THEN 'Standard'
            ELSE 'Short Form'
        END,
        ''
    )

FROM category c
LEFT JOIN film_category fc ON c.category_id = fc.category_id
LEFT JOIN film f ON fc.film_id = f.film_id
LEFT JOIN inventory i ON f.film_id = i.film_id
LEFT JOIN rental r ON i.inventory_id = r.inventory_id
LEFT JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.category_id, c.name
ORDER BY total_revenue DESC;
```
