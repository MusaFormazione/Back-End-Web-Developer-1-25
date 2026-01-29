# 🔢 Funzioni Numeriche

MySQL offre un ricco set di funzioni per operazioni matematiche, calcoli statistici e manipolazione di valori numerici.

---

## 📋 Indice

### Funzioni Aritmetiche Base
- [ABS](#abs)
- [CEIL / CEILING](#ceil)
- [FLOOR](#floor)
- [ROUND](#round)
- [TRUNCATE](#truncate)
- [MOD](#mod)
- [DIV](#div)

### Funzioni di Potenza e Radice
- [POWER / POW](#power)
- [SQRT](#sqrt)
- [EXP](#exp)
- [LN / LOG](#ln)
- [LOG10](#log10)
- [LOG2](#log2)

### Funzioni Trigonometriche
- [SIN](#sin)
- [COS](#cos)
- [TAN](#tan)
- [ASIN](#asin)
- [ACOS](#acos)
- [ATAN](#atan)
- [ATAN2](#atan2)
- [PI](#pi)
- [RADIANS](#radians)
- [DEGREES](#degrees)

### Funzioni di Utility
- [SIGN](#sign)
- [RAND](#rand)
- [GREATEST](#greatest)
- [LEAST](#least)

### Funzioni di Conversione
- [FORMAT](#format)
- [BIN](#bin)
- [OCT](#oct)
- [HEX](#hex)
- [CONV](#conv)

---

## ABS

🔥 **Funzione molto utilizzata**

**Sintassi:** `ABS(number)`

**Descrizione:** Restituisce il valore assoluto di un numero.

**Esempio Sakila:**
```sql
-- Analisi differenze nei noleggi
SELECT 
    film_id,
    title,
    rental_rate,
    replacement_cost,
    replacement_cost - rental_rate AS raw_difference,
    ABS(replacement_cost - rental_rate) AS absolute_difference,
    CASE 
        WHEN replacement_cost > rental_rate THEN 'Replacement > Rental'
        WHEN replacement_cost < rental_rate THEN 'Rental > Replacement'
        ELSE 'Equal'
    END AS cost_comparison
FROM film
ORDER BY ABS(replacement_cost - rental_rate) DESC
LIMIT 10;
```

```sql
-- Deviazioni dalla media
SELECT 
    f.film_id,
    f.title,
    f.length,
    (SELECT AVG(length) FROM film) AS avg_length,
    f.length - (SELECT AVG(length) FROM film) AS deviation,
    ABS(f.length - (SELECT AVG(length) FROM film)) AS absolute_deviation,
    CASE 
        WHEN ABS(f.length - (SELECT AVG(length) FROM film)) <= 10 THEN 'Vicino alla media'
        WHEN ABS(f.length - (SELECT AVG(length) FROM film)) <= 30 THEN 'Moderatamente diverso'
        ELSE 'Molto diverso dalla media'
    END AS deviation_category
FROM film f
ORDER BY absolute_deviation DESC
LIMIT 15;
```

```sql
-- Analisi budget variance
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    AVG(f.replacement_cost) AS avg_replacement_cost,
    AVG(f.rental_rate) AS avg_rental_rate,
    ABS(AVG(f.replacement_cost) - AVG(f.rental_rate)) AS cost_variance,
    ABS(AVG(f.replacement_cost) - (SELECT AVG(replacement_cost) FROM film)) AS category_deviation
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY cost_variance DESC;
```

---

## ROUND

🔥 **Funzione molto utilizzata**

**Sintassi:** `ROUND(number [, decimals])`

**Descrizione:** Arrotonda un numero al numero specificato di cifre decimali.

**Esempio Sakila:**
```sql
-- Arrotondamenti per reporting
SELECT 
    customer_id,
    payment_id,
    amount,
    ROUND(amount) AS amount_rounded,
    ROUND(amount, 1) AS amount_one_decimal,
    ROUND(amount * 1.1, 2) AS amount_with_tax,
    ROUND(amount / 3, 2) AS amount_split_three,
    ROUND(amount, -1) AS amount_rounded_tens
FROM payment
LIMIT 10;
```

```sql
-- Statistiche arrotondate per dashboard
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    ROUND(AVG(f.length), 0) AS avg_duration_minutes,
    ROUND(AVG(f.length) / 60, 1) AS avg_duration_hours,
    ROUND(AVG(f.rental_rate), 2) AS avg_rental_rate,
    ROUND(AVG(f.replacement_cost), 2) AS avg_replacement_cost,
    ROUND(AVG(f.rental_rate) * 100 / AVG(f.replacement_cost), 1) AS rental_cost_percentage
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY avg_rental_rate DESC;
```

```sql
-- Performance metrics arrotondati
SELECT 
    s.staff_id,
    CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
    COUNT(p.payment_id) AS total_transactions,
    ROUND(SUM(p.amount), 2) AS total_revenue,
    ROUND(AVG(p.amount), 2) AS avg_transaction_amount,
    ROUND(SUM(p.amount) / COUNT(p.payment_id), 2) AS revenue_per_transaction,
    ROUND(COUNT(p.payment_id) / 30.0, 1) AS avg_daily_transactions
FROM staff s
LEFT JOIN payment p ON s.staff_id = p.staff_id
GROUP BY s.staff_id, s.first_name, s.last_name;
```

---

## CEIL / CEILING

🔥 **Funzione molto utilizzata**

**Sintassi:** `CEIL(number)` o `CEILING(number)`

**Descrizione:** Arrotonda un numero all'intero superiore più vicino.

**Esempio Sakila:**
```sql
-- Calcolo giorni necessari per progetti
SELECT 
    film_id,
    title,
    length,
    length / 60.0 AS exact_hours,
    CEIL(length / 60.0) AS hours_needed,
    CEIL(length / 1440.0) AS days_if_continuous,
    CEIL(rental_rate) AS rounded_up_rate,
    CEIL(replacement_cost / 10) AS replacement_units_of_10
FROM film
LIMIT 10;
```

```sql
-- Pianificazione risorse
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    AVG(f.length) AS avg_length,
    CEIL(AVG(f.length) / 30) AS screening_slots_needed,
    CEIL(COUNT(f.film_id) / 10.0) AS storage_units_needed,
    CEIL(AVG(f.replacement_cost)) AS budget_per_category_rounded_up
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY films_count DESC;
```

---

## FLOOR

🔥 **Funzione molto utilizzata**

**Sintassi:** `FLOOR(number)`

**Descrizione:** Arrotonda un numero all'intero inferiore più vicino.

**Esempio Sakila:**
```sql
-- Calcoli conservativi
SELECT 
    customer_id,
    payment_id,
    amount,
    FLOOR(amount) AS conservative_amount,
    FLOOR(amount * 0.9) AS discounted_amount,
    FLOOR(amount / 2.99) AS rental_units_affordable,
    CASE 
        WHEN FLOOR(amount) >= 5 THEN 'High Value'
        WHEN FLOOR(amount) >= 3 THEN 'Medium Value'
        ELSE 'Low Value'
    END AS value_category
FROM payment
WHERE amount > 0
ORDER BY amount DESC
LIMIT 15;
```

```sql
-- Analisi capacità
SELECT 
    f.film_id,
    f.title,
    f.length,
    FLOOR(f.length / 30) AS thirty_minute_segments,
    FLOOR(f.length / 60) AS full_hours,
    FLOOR(f.rental_rate * 10) AS rental_rate_cents,
    FLOOR(f.replacement_cost / f.rental_rate) AS rental_to_replacement_ratio
FROM film f
WHERE f.length > 0 AND f.rental_rate > 0
LIMIT 10;
```

---

## TRUNCATE

**Sintassi:** `TRUNCATE(number, decimals)`

**Descrizione:** Tronca un numero al numero specificato di cifre decimali (senza arrotondamento).

**Esempio Sakila:**
```sql
-- Troncamento vs arrotondamento
SELECT 
    payment_id,
    amount,
    TRUNCATE(amount, 1) AS truncated_1_decimal,
    ROUND(amount, 1) AS rounded_1_decimal,
    TRUNCATE(amount, 0) AS truncated_integer,
    ROUND(amount, 0) AS rounded_integer,
    TRUNCATE(amount * 1.15, 2) AS truncated_with_markup
FROM payment
WHERE amount BETWEEN 3.5 AND 6.5
LIMIT 10;
```

---

## MOD

🔥 **Funzione molto utilizzata**

**Sintassi:** `MOD(number, divisor)` o `number % divisor`

**Descrizione:** Restituisce il resto della divisione.

**Esempio Sakila:**
```sql
-- Raggruppamenti ciclici
SELECT 
    customer_id,
    first_name,
    last_name,
    customer_id % 4 AS group_number,
    CASE customer_id % 4
        WHEN 0 THEN 'Gruppo A'
        WHEN 1 THEN 'Gruppo B'
        WHEN 2 THEN 'Gruppo C'
        WHEN 3 THEN 'Gruppo D'
    END AS assigned_group,
    CASE customer_id % 2
        WHEN 0 THEN 'Pari'
        WHEN 1 THEN 'Dispari'
    END AS parity
FROM customer
LIMIT 20;
```

```sql
-- Analisi pattern temporali
SELECT 
    rental_id,
    rental_date,
    DAYOFWEEK(rental_date) AS day_of_week,
    DAYOFWEEK(rental_date) % 7 AS day_cycle,
    HOUR(rental_date) % 6 AS six_hour_period,
    CASE HOUR(rental_date) % 6
        WHEN 0 THEN '00-05'
        WHEN 1 THEN '06-11'
        WHEN 2 THEN '12-17'
        WHEN 3 THEN '18-23'
        WHEN 4 THEN '00-05'
        WHEN 5 THEN '06-11'
    END AS time_period
FROM rental
LIMIT 15;
```

```sql
-- Distribuzione inventory per store
SELECT 
    inventory_id,
    film_id,
    store_id,
    inventory_id % 3 AS rotation_group,
    CASE inventory_id % 3
        WHEN 0 THEN 'Rotation A'
        WHEN 1 THEN 'Rotation B'
        WHEN 2 THEN 'Rotation C'
    END AS rotation_schedule
FROM inventory
LIMIT 20;
```

---

## POWER / POW

**Sintassi:** `POWER(base, exponent)` o `POW(base, exponent)`

**Descrizione:** Calcola base elevato a exponent.

**Esempio Sakila:**
```sql
-- Calcoli esponenziali per crescita
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    AVG(f.rental_rate) AS avg_rate,
    POWER(AVG(f.rental_rate), 2) AS rate_squared,
    ROUND(POWER(1.1, COUNT(f.film_id) / 10), 2) AS compound_growth_factor,
    ROUND(AVG(f.rental_rate) * POWER(1.05, 12), 2) AS projected_annual_rate
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY films_count DESC;
```

```sql
-- Score composti
SELECT 
    customer_id,
    first_name,
    last_name,
    customer_id AS base_score,
    ROUND(POWER(customer_id, 0.5), 2) AS sqrt_score,
    ROUND(POWER(customer_id / 100, 2), 2) AS normalized_squared_score
FROM customer
WHERE customer_id <= 20;
```

---

## SQRT

**Sintassi:** `SQRT(number)`

**Descrizione:** Calcola la radice quadrata di un numero.

**Esempio Sakila:**
```sql
-- Calcoli di distanza e dispersione
SELECT 
    film_id,
    title,
    length,
    rental_rate,
    replacement_cost,
    ROUND(SQRT(length), 2) AS sqrt_length,
    ROUND(SQRT(rental_rate * replacement_cost), 2) AS geometric_mean_cost,
    ROUND(SQRT(POWER(length - 100, 2)), 2) AS distance_from_100_minutes
FROM film
WHERE length > 0
LIMIT 10;
```

```sql
-- Standard deviation manuale
SELECT 
    c.name AS category,
    COUNT(f.film_id) AS films_count,
    AVG(f.length) AS avg_length,
    ROUND(
        SQRT(
            AVG(POWER(f.length - (SELECT AVG(length) FROM film), 2))
        ), 2
    ) AS manual_std_dev_from_overall_avg
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
HAVING COUNT(f.film_id) > 5;
```

---

## LN / LOG

**Sintassi:** `LN(number)` (logaritmo naturale) / `LOG(number)` (base 10 o naturale)

**Descrizione:** Calcola il logaritmo di un numero.

**Esempio Sakila:**
```sql
-- Analisi logaritmica per scale normalizzate
SELECT 
    film_id,
    title,
    replacement_cost,
    ROUND(LN(replacement_cost), 3) AS ln_cost,
    ROUND(LOG10(replacement_cost * 100), 3) AS log10_cost_cents,
    ROUND(LN(replacement_cost) / LN(2), 3) AS log2_cost
FROM film
WHERE replacement_cost > 0
ORDER BY replacement_cost DESC
LIMIT 10;
```

---

## RAND

🔥 **Funzione molto utilizzata**

**Sintassi:** `RAND([seed])`

**Descrizione:** Genera un numero casuale tra 0 e 1.

**Esempio Sakila:**
```sql
-- Selezione casuale per campionamento
SELECT 
    film_id,
    title,
    rating,
    ROUND(RAND(), 4) AS random_value,
    FLOOR(RAND() * 100) AS random_percentage,
    CASE 
        WHEN RAND() < 0.3 THEN 'Sample A'
        WHEN RAND() < 0.6 THEN 'Sample B'
        ELSE 'Sample C'
    END AS random_group
FROM film
WHERE RAND() < 0.1  -- 10% sample
LIMIT 20;
```

```sql
-- Assegnazione casuale priorità
SELECT 
    customer_id,
    first_name,
    last_name,
    ROUND(RAND() * 10, 0) AS random_priority,
    CASE 
        WHEN RAND() < 0.2 THEN 'High Priority'
        WHEN RAND() < 0.7 THEN 'Medium Priority'
        ELSE 'Low Priority'
    END AS priority_level,
    CONCAT('CUST-', LPAD(FLOOR(RAND() * 9999), 4, '0')) AS random_reference_code
FROM customer
LIMIT 15;
```

```sql
-- Promozioni casuali
SELECT 
    f.film_id,
    f.title,
    f.rental_rate,
    ROUND(f.rental_rate * (0.8 + RAND() * 0.4), 2) AS promotional_rate,
    CASE 
        WHEN RAND() < 0.1 THEN '90% OFF - MEGA DEAL!'
        WHEN RAND() < 0.3 THEN '50% OFF'
        WHEN RAND() < 0.6 THEN '25% OFF'
        ELSE 'Regular Price'
    END AS promotion_type
FROM film f
WHERE RAND() < 0.2
LIMIT 10;
```

---

## SIGN

**Sintassi:** `SIGN(number)`

**Descrizione:** Restituisce il segno di un numero (-1, 0, 1).

**Esempio Sakila:**
```sql
-- Analisi trend e direzioni
SELECT 
    payment_id,
    customer_id,
    amount,
    amount - 4.00 AS difference_from_base,
    SIGN(amount - 4.00) AS trend_direction,
    CASE SIGN(amount - 4.00)
        WHEN 1 THEN 'Above Average'
        WHEN 0 THEN 'At Average'
        WHEN -1 THEN 'Below Average'
    END AS performance_category
FROM payment
WHERE amount BETWEEN 2.00 AND 8.00
LIMIT 15;
```

---

## FORMAT

🔥 **Funzione molto utilizzata**

**Sintassi:** `FORMAT(number, decimal_places [, locale])`

**Descrizione:** Formatta un numero con separatori delle migliaia e decimali.

**Esempio Sakila:**
```sql
-- Formattazione per report finanziari
SELECT 
    'Revenue Analysis' AS report_section,
    FORMAT(SUM(amount), 2) AS total_revenue_formatted,
    FORMAT(AVG(amount), 2) AS avg_payment_formatted,
    FORMAT(COUNT(*), 0) AS total_transactions_formatted,
    FORMAT(MAX(amount), 2) AS max_payment_formatted,
    FORMAT(MIN(amount), 2) AS min_payment_formatted
FROM payment

UNION ALL

SELECT 
    'Film Statistics',
    FORMAT(SUM(replacement_cost), 2),
    FORMAT(AVG(replacement_cost), 2),
    FORMAT(COUNT(*), 0),
    FORMAT(MAX(replacement_cost), 2),
    FORMAT(MIN(replacement_cost), 2)
FROM film;
```

```sql
-- Dashboard finanziario formattato
SELECT 
    c.name AS category,
    FORMAT(COUNT(f.film_id), 0) AS films_count,
    FORMAT(AVG(f.length), 1) AS avg_duration_formatted,
    FORMAT(SUM(f.replacement_cost), 2) AS total_inventory_value,
    FORMAT(AVG(f.rental_rate), 2) AS avg_rental_rate,
    CONCAT('$', FORMAT(AVG(f.rental_rate * f.length / 60), 2), '/hr') AS rate_per_hour
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY SUM(f.replacement_cost) DESC;
```

---

## Esempi Pratici Combinati

### Sistema di Scoring Avanzato
```sql
-- Sistema di scoring matematico complesso
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(r.rental_id) AS total_rentals,
    SUM(p.amount) AS total_spent,
    
    -- Score basato su logaritmo per normalizzare outlier
    ROUND(LN(COUNT(r.rental_id) + 1) * 10, 2) AS frequency_score,
    
    -- Score monetario con radice quadrata per ridurre impatto outlier
    ROUND(SQRT(SUM(p.amount)) * 3, 2) AS monetary_score,
    
    -- Score composto usando potenza
    ROUND(
        POWER(
            (LN(COUNT(r.rental_id) + 1) * SQRT(SUM(p.amount))),
            0.6
        ), 2
    ) AS composite_score,
    
    -- Percentile usando arrotondamenti
    ROUND(
        (RANK() OVER (ORDER BY COUNT(r.rental_id)) / 
         (SELECT COUNT(DISTINCT customer_id) FROM rental) * 100), 1
    ) AS frequency_percentile,
    
    -- Classificazione con MOD per distribuzione
    CASE c.customer_id % 5
        WHEN 0 THEN CEIL(LN(COUNT(r.rental_id) + 1) * 10) + 10
        WHEN 1 THEN CEIL(LN(COUNT(r.rental_id) + 1) * 10) + 5
        WHEN 2 THEN CEIL(LN(COUNT(r.rental_id) + 1) * 10)
        WHEN 3 THEN FLOOR(LN(COUNT(r.rental_id) + 1) * 10) - 5
        WHEN 4 THEN FLOOR(LN(COUNT(r.rental_id) + 1) * 10) - 10
    END AS adjusted_score

FROM customer c
LEFT JOIN rental r ON c.customer_id = r.customer_id
LEFT JOIN payment p ON r.rental_id = p.rental_id
GROUP BY c.customer_id, c.first_name, c.last_name
HAVING COUNT(r.rental_id) > 0
ORDER BY composite_score DESC
LIMIT 20;
```

### Analisi Performance con Calcoli Matematici
```sql
-- Analisi performance completa con funzioni matematiche
SELECT 
    cat.name AS category,
    COUNT(f.film_id) AS total_films,
    
    -- Statistiche base arrotondate
    ROUND(AVG(f.length), 1) AS avg_duration,
    ROUND(STDDEV(f.length), 2) AS duration_std_dev,
    
    -- Analisi distribuzione
    ROUND(AVG(f.length) - STDDEV(f.length), 1) AS lower_bound,
    ROUND(AVG(f.length) + STDDEV(f.length), 1) AS upper_bound,
    
    -- Coefficiente di variazione (%)
    ROUND(STDDEV(f.length) / AVG(f.length) * 100, 1) AS cv_percentage,
    
    -- Score di diversità (logaritmico)
    ROUND(LN(COUNT(DISTINCT f.rating) + 1) * 10, 2) AS diversity_score,
    
    -- Performance index composto
    ROUND(
        SQRT(COUNT(f.film_id)) * 
        LN(AVG(f.rental_rate) + 1) * 
        (1 + 1/GREATEST(STDDEV(f.length)/AVG(f.length), 0.1))
    , 2) AS performance_index,
    
    -- Classificazione basata su MOD e calcoli
    CASE 
        WHEN MOD(FLOOR(AVG(f.length)), 10) < 3 THEN 'Short Content Focus'
        WHEN MOD(FLOOR(AVG(f.length)), 10) < 7 THEN 'Balanced Content'
        ELSE 'Long Content Focus'
    END AS content_strategy,
    
    -- Price tier con POWER
    CASE 
        WHEN POWER(AVG(f.rental_rate), 2) > 16 THEN 'Premium'
        WHEN POWER(AVG(f.rental_rate), 2) > 9 THEN 'Mid-tier'
        ELSE 'Budget'
    END AS price_tier

FROM category cat
JOIN film_category fc ON cat.category_id = fc.category_id
JOIN film f ON fc.film_id = f.film_id
GROUP BY cat.category_id, cat.name
HAVING COUNT(f.film_id) >= 5
ORDER BY performance_index DESC;
```

### Dashboard Finanziario con Formattazione
```sql
-- Dashboard finanziario completo con tutte le formattazioni
SELECT 
    'SUMMARY' AS section,
    'Total Revenue' AS metric,
    CONCAT('$', FORMAT(SUM(amount), 2)) AS value,
    CONCAT(FORMAT(COUNT(*), 0), ' transactions') AS details,
    'All Time' AS period
FROM payment

UNION ALL

SELECT 
    'SUMMARY',
    'Average Transaction',
    CONCAT('$', FORMAT(AVG(amount), 2)),
    CONCAT('Range: $', FORMAT(MIN(amount), 2), ' - $', FORMAT(MAX(amount), 2)),
    'All Time'
FROM payment

UNION ALL

SELECT 
    'SUMMARY',
    'Revenue Variance',
    FORMAT(VARIANCE(amount), 4),
    CONCAT('Std Dev: $', FORMAT(STDDEV(amount), 2)),
    'All Time'
FROM payment

UNION ALL

SELECT 
    'TARGETS',
    'Monthly Goal',
    CONCAT('$', FORMAT(67000, 2)),
    CONCAT('Progress: ', ROUND((SUM(amount)/67000)*100, 1), '%'),
    DATE_FORMAT(NOW(), '%M %Y')
FROM payment
WHERE YEAR(payment_date) = 2005 AND MONTH(payment_date) = 8

UNION ALL

SELECT 
    'PROJECTIONS',
    'Annual Projection',
    CONCAT('$', FORMAT(SUM(amount) * 12, 2)),
    CONCAT('Based on ', FORMAT(COUNT(*), 0), ' sample transactions'),
    'Extrapolated'
FROM payment
WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)

ORDER BY section, metric;
```
