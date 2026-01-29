# 🪟 Funzioni Window (Analitiche)

Le funzioni window permettono di eseguire calcoli complessi su set di righe correlate alla riga corrente, senza dover raggruppare i dati. Sono essenziali per analisi avanzate e business intelligence.

---

## 📋 Indice

### Funzioni di Ranking
- [ROW_NUMBER](#row_number)
- [RANK](#rank)
- [DENSE_RANK](#dense_rank)
- [PERCENT_RANK](#percent_rank)
- [CUME_DIST](#cume_dist)
- [NTILE](#ntile)

### Funzioni di Navigazione
- [LAG](#lag)
- [LEAD](#lead)
- [FIRST_VALUE](#first_value)
- [LAST_VALUE](#last_value)
- [NTH_VALUE](#nth_value)

### Funzioni Aggregate Window
- [SUM() OVER](#sum_over)
- [COUNT() OVER](#count_over)
- [AVG() OVER](#avg_over)
- [MIN() OVER](#min_over)
- [MAX() OVER](#max_over)

### Sintassi Window Frame
- [ROWS](#rows)
- [RANGE](#range)
- [Frame Boundaries](#frame_boundaries)

---

## ROW_NUMBER

🔥 **Funzione molto utilizzata**

**Sintassi:** `ROW_NUMBER() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Assegna un numero sequenziale univoco a ogni riga all'interno di una partizione.

**Esempio Sakila:**
```sql
-- Ranking film per categoria
SELECT 
    f.film_id,
    f.title,
    c.name AS category,
    f.rental_rate,
    f.length,
    
    -- Row number globale
    ROW_NUMBER() OVER (ORDER BY f.rental_rate DESC) AS global_rank,
    
    -- Row number per categoria
    ROW_NUMBER() OVER (PARTITION BY c.name ORDER BY f.rental_rate DESC) AS category_rank,
    
    -- Row number per durata
    ROW_NUMBER() OVER (PARTITION BY c.name ORDER BY f.length DESC) AS duration_rank_in_category,
    
    -- Combinazione multiple
    ROW_NUMBER() OVER (PARTITION BY f.rating ORDER BY f.rental_rate DESC, f.length DESC) AS complex_rank

FROM film f
JOIN film_category fc ON f.film_id = fc.film_id
JOIN category c ON fc.category_id = c.category_id
ORDER BY c.name, category_rank
LIMIT 50;
```

```sql
-- Top performer clienti per store
SELECT 
    store_performance.*
FROM (
    SELECT 
        s.store_id,
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        COUNT(r.rental_id) AS total_rentals,
        SUM(p.amount) AS total_spent,
        AVG(p.amount) AS avg_payment,
        
        -- Ranking multipli
        ROW_NUMBER() OVER (
            PARTITION BY s.store_id 
            ORDER BY COUNT(r.rental_id) DESC
        ) AS frequency_rank,
        
        ROW_NUMBER() OVER (
            PARTITION BY s.store_id 
            ORDER BY SUM(p.amount) DESC
        ) AS revenue_rank,
        
        ROW_NUMBER() OVER (
            PARTITION BY s.store_id 
            ORDER BY AVG(p.amount) DESC
        ) AS avg_payment_rank
        
    FROM store s
    JOIN customer c ON s.store_id = c.store_id
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY s.store_id, c.customer_id, c.first_name, c.last_name
) store_performance
WHERE frequency_rank <= 5 OR revenue_rank <= 5
ORDER BY store_id, frequency_rank;
```

```sql
-- Paginazione avanzata con row_number
SELECT 
    page_info.*,
    CASE 
        WHEN row_num <= 10 THEN 'Page 1'
        WHEN row_num <= 20 THEN 'Page 2'
        WHEN row_num <= 30 THEN 'Page 3'
        ELSE 'Page 4+'
    END AS page_number
FROM (
    SELECT 
        f.film_id,
        f.title,
        f.rental_rate,
        f.length,
        ROW_NUMBER() OVER (ORDER BY f.rental_rate DESC, f.title) AS row_num
    FROM film f
) page_info
WHERE row_num BETWEEN 11 AND 20  -- Pagina 2
ORDER BY row_num;
```

---

## RANK

🔥 **Funzione molto utilizzata**

**Sintassi:** `RANK() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Assegna rank con gap per valori identici (1, 2, 2, 4...).

**Esempio Sakila:**
```sql
-- Ranking con gestione pareggi
SELECT 
    f.film_id,
    f.title,
    f.rental_rate,
    f.rating,
    
    -- Ranking con gap
    RANK() OVER (ORDER BY f.rental_rate DESC) AS price_rank,
    
    -- Ranking per rating
    RANK() OVER (PARTITION BY f.rating ORDER BY f.rental_rate DESC) AS rank_in_rating,
    
    -- Comparazione con ROW_NUMBER
    ROW_NUMBER() OVER (ORDER BY f.rental_rate DESC) AS row_number_comparison,
    
    -- Dense rank senza gap
    DENSE_RANK() OVER (ORDER BY f.rental_rate DESC) AS dense_rank_comparison,
    
    -- Business logic con ranking
    CASE 
        WHEN RANK() OVER (ORDER BY f.rental_rate DESC) <= 10 THEN 'Top Tier'
        WHEN RANK() OVER (ORDER BY f.rental_rate DESC) <= 50 THEN 'Premium'
        WHEN RANK() OVER (ORDER BY f.rental_rate DESC) <= 200 THEN 'Standard'
        ELSE 'Budget'
    END AS pricing_tier

FROM film f
ORDER BY f.rental_rate DESC, f.title
LIMIT 25;
```

```sql
-- Analisi performance staff con ranking
SELECT 
    staff_performance.*,
    CASE 
        WHEN performance_rank = 1 THEN '🥇 Top Performer'
        WHEN performance_rank <= 3 THEN '🥈 High Performer'
        ELSE '📊 Standard Performer'
    END AS performance_badge
FROM (
    SELECT 
        s.staff_id,
        CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
        COUNT(DISTINCT r.rental_id) AS rentals_processed,
        COUNT(DISTINCT p.payment_id) AS payments_processed,
        SUM(p.amount) AS total_revenue_generated,
        AVG(p.amount) AS avg_transaction_value,
        
        -- Multiple ranking criteria
        RANK() OVER (ORDER BY COUNT(DISTINCT r.rental_id) DESC) AS rental_processing_rank,
        RANK() OVER (ORDER BY SUM(p.amount) DESC) AS revenue_generation_rank,
        RANK() OVER (ORDER BY AVG(p.amount) DESC) AS avg_value_rank,
        
        -- Composite performance rank
        RANK() OVER (
            ORDER BY 
                (COUNT(DISTINCT r.rental_id) * 0.4) + 
                (SUM(p.amount) * 0.6)
            DESC
        ) AS performance_rank
        
    FROM staff s
    LEFT JOIN rental r ON s.staff_id = r.staff_id
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY s.staff_id, s.first_name, s.last_name
) staff_performance
ORDER BY performance_rank;
```

---

## DENSE_RANK

**Sintassi:** `DENSE_RANK() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Assegna rank senza gap per valori identici (1, 2, 2, 3...).

**Esempio Sakila:**
```sql
-- Dense ranking per categorie
SELECT 
    category_stats.*,
    CONCAT('Rank #', category_popularity_rank) AS rank_display,
    CASE 
        WHEN category_popularity_rank <= 3 THEN 'Top Categories'
        WHEN category_popularity_rank <= 6 THEN 'Popular Categories'
        ELSE 'Niche Categories'
    END AS category_tier
FROM (
    SELECT 
        c.category_id,
        c.name AS category_name,
        COUNT(f.film_id) AS films_in_category,
        COUNT(r.rental_id) AS total_rentals,
        SUM(p.amount) AS total_revenue,
        AVG(f.rental_rate) AS avg_rental_rate,
        
        -- Dense ranking per popolarità
        DENSE_RANK() OVER (ORDER BY COUNT(r.rental_id) DESC) AS category_popularity_rank,
        
        -- Dense ranking per revenue
        DENSE_RANK() OVER (ORDER BY SUM(p.amount) DESC) AS category_revenue_rank,
        
        -- Dense ranking per numero film
        DENSE_RANK() OVER (ORDER BY COUNT(f.film_id) DESC) AS category_size_rank
        
    FROM category c
    LEFT JOIN film_category fc ON c.category_id = fc.category_id
    LEFT JOIN film f ON fc.film_id = f.film_id
    LEFT JOIN inventory i ON f.film_id = i.film_id
    LEFT JOIN rental r ON i.inventory_id = r.inventory_id
    LEFT JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY c.category_id, c.name
) category_stats
ORDER BY category_popularity_rank, category_name;
```

---

## PERCENT_RANK

**Sintassi:** `PERCENT_RANK() OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Calcola il rango percentuale (0-1) di ogni valore.

**Esempio Sakila:**
```sql
-- Percentili performance film
SELECT 
    f.film_id,
    f.title,
    f.rental_rate,
    f.length,
    f.replacement_cost,
    
    -- Percentili diversi
    ROUND(PERCENT_RANK() OVER (ORDER BY f.rental_rate), 3) AS price_percentile,
    ROUND(PERCENT_RANK() OVER (ORDER BY f.length), 3) AS duration_percentile,
    ROUND(PERCENT_RANK() OVER (ORDER BY f.replacement_cost), 3) AS cost_percentile,
    
    -- Conversione in percentuali leggibili
    CONCAT(ROUND(PERCENT_RANK() OVER (ORDER BY f.rental_rate) * 100, 1), '%') AS price_percentile_display,
    
    -- Classificazione basata su percentili
    CASE 
        WHEN PERCENT_RANK() OVER (ORDER BY f.rental_rate) >= 0.9 THEN 'Top 10% - Premium'
        WHEN PERCENT_RANK() OVER (ORDER BY f.rental_rate) >= 0.75 THEN 'Top 25% - High End'
        WHEN PERCENT_RANK() OVER (ORDER BY f.rental_rate) >= 0.5 THEN 'Top 50% - Above Average'
        WHEN PERCENT_RANK() OVER (ORDER BY f.rental_rate) >= 0.25 THEN 'Bottom 50% - Below Average'
        ELSE 'Bottom 25% - Budget'
    END AS price_tier_percentile

FROM film f
ORDER BY f.rental_rate DESC
LIMIT 20;
```

```sql
-- Analisi percentili clienti
SELECT 
    customer_analysis.*,
    CASE 
        WHEN spending_percentile >= 0.95 THEN '💎 VIP (Top 5%)'
        WHEN spending_percentile >= 0.8 THEN '🥇 Premium (Top 20%)'
        WHEN spending_percentile >= 0.6 THEN '🥈 Gold (Top 40%)'
        WHEN spending_percentile >= 0.4 THEN '🥉 Silver (Top 60%)'
        ELSE '📊 Standard'
    END AS customer_tier
FROM (
    SELECT 
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        COUNT(r.rental_id) AS total_rentals,
        SUM(p.amount) AS total_spent,
        AVG(p.amount) AS avg_payment,
        
        ROUND(PERCENT_RANK() OVER (ORDER BY SUM(p.amount)), 3) AS spending_percentile,
        ROUND(PERCENT_RANK() OVER (ORDER BY COUNT(r.rental_id)), 3) AS frequency_percentile,
        ROUND(PERCENT_RANK() OVER (ORDER BY AVG(p.amount)), 3) AS avg_payment_percentile
        
    FROM customer c
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY c.customer_id, c.first_name, c.last_name
    HAVING COUNT(r.rental_id) > 5
) customer_analysis
ORDER BY spending_percentile DESC
LIMIT 30;
```

---

## NTILE

🔥 **Funzione molto utilizzata**

**Sintassi:** `NTILE(n) OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Divide i risultati in n gruppi (buckets) approssimativamente uguali.

**Esempio Sakila:**
```sql
-- Segmentazione clienti in quartili
SELECT 
    customer_segments.*,
    CASE customer_quartile
        WHEN 1 THEN '🥇 Top 25% - Champions'
        WHEN 2 THEN '🥈 Second 25% - Loyal'
        WHEN 3 THEN '🥉 Third 25% - Regular'
        WHEN 4 THEN '📊 Bottom 25% - Potential'
    END AS segment_name,
    CASE customer_quartile
        WHEN 1 THEN 'Premium pricing, exclusive offers'
        WHEN 2 THEN 'Loyalty rewards, special previews'
        WHEN 3 THEN 'Targeted promotions'
        WHEN 4 THEN 'Engagement campaigns, discounts'
    END AS recommended_strategy
FROM (
    SELECT 
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        COUNT(r.rental_id) AS total_rentals,
        SUM(p.amount) AS total_spent,
        AVG(p.amount) AS avg_payment,
        
        -- Segmentazione in quartili
        NTILE(4) OVER (ORDER BY SUM(p.amount) DESC) AS customer_quartile,
        
        -- Segmentazione per frequency
        NTILE(5) OVER (ORDER BY COUNT(r.rental_id) DESC) AS frequency_quintile,
        
        -- Segmentazione decili per analisi fine
        NTILE(10) OVER (ORDER BY SUM(p.amount) DESC) AS customer_decile
        
    FROM customer c
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY c.customer_id, c.first_name, c.last_name
) customer_segments
ORDER BY customer_quartile, total_spent DESC;
```

```sql
-- Analisi ABC inventory con NTILE
SELECT 
    inventory_analysis.*,
    CASE abc_category
        WHEN 1 THEN 'A - High Priority'
        WHEN 2 THEN 'B - Medium Priority'  
        WHEN 3 THEN 'C - Low Priority'
    END AS abc_classification,
    CASE abc_category
        WHEN 1 THEN 'Focus inventory, premium placement'
        WHEN 2 THEN 'Standard inventory management'
        WHEN 3 THEN 'Minimize stock, consider removal'
    END AS inventory_strategy
FROM (
    SELECT 
        f.film_id,
        f.title,
        c.name AS category,
        COUNT(r.rental_id) AS rental_frequency,
        SUM(p.amount) AS total_revenue,
        AVG(DATEDIFF(r.return_date, r.rental_date)) AS avg_rental_days,
        
        -- ABC Analysis usando NTILE
        NTILE(3) OVER (ORDER BY COUNT(r.rental_id) DESC) AS abc_category,
        
        -- Performance quintiles
        NTILE(5) OVER (ORDER BY SUM(p.amount) DESC) AS revenue_quintile
        
    FROM film f
    JOIN film_category fc ON f.film_id = fc.film_id
    JOIN category c ON fc.category_id = c.category_id
    JOIN inventory i ON f.film_id = i.film_id
    JOIN rental r ON i.inventory_id = r.inventory_id
    JOIN payment p ON r.rental_id = p.rental_id
    WHERE r.return_date IS NOT NULL
    GROUP BY f.film_id, f.title, c.name
) inventory_analysis
ORDER BY abc_category, rental_frequency DESC;
```

---

## LAG

🔥 **Funzione molto utilizzata**

**Sintassi:** `LAG(expr [, offset [, default]]) OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Accede al valore di una riga precedente nella finestra.

**Esempio Sakila:**
```sql
-- Analisi trend mensili revenue
SELECT 
    monthly_trends.*,
    CASE 
        WHEN revenue_change > 0 THEN CONCAT('📈 +', FORMAT(revenue_change, 2))
        WHEN revenue_change < 0 THEN CONCAT('📉 ', FORMAT(revenue_change, 2))
        ELSE '➡️ No Change'
    END AS trend_indicator,
    CASE 
        WHEN revenue_change_pct > 10 THEN 'Strong Growth'
        WHEN revenue_change_pct > 5 THEN 'Moderate Growth'
        WHEN revenue_change_pct > -5 THEN 'Stable'
        WHEN revenue_change_pct > -10 THEN 'Moderate Decline'
        ELSE 'Strong Decline'
    END AS trend_category
FROM (
    SELECT 
        DATE_FORMAT(payment_date, '%Y-%m') AS month,
        DATE_FORMAT(payment_date, '%M %Y') AS month_display,
        COUNT(*) AS transactions,
        SUM(amount) AS monthly_revenue,
        AVG(amount) AS avg_transaction,
        
        -- Valori del mese precedente
        LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS prev_month_revenue,
        LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS prev_month_transactions,
        LAG(AVG(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS prev_avg_transaction,
        
        -- Calcoli di cambiamento
        SUM(amount) - LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS revenue_change,
        COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS transaction_change,
        
        -- Percentuali di cambiamento
        ROUND(
            ((SUM(amount) - LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m'))) /
             NULLIF(LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')), 0)) * 100, 2
        ) AS revenue_change_pct
        
    FROM payment
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY month
) monthly_trends;
```

```sql
-- Analisi customer journey con LAG
SELECT 
    customer_journey.*,
    CASE 
        WHEN days_between_rentals <= 7 THEN 'High Frequency'
        WHEN days_between_rentals <= 30 THEN 'Regular'
        WHEN days_between_rentals <= 90 THEN 'Occasional'
        ELSE 'Sporadic'
    END AS rental_pattern,
    CASE 
        WHEN payment_change > 0 THEN 'Increasing Value'
        WHEN payment_change < 0 THEN 'Decreasing Value'
        ELSE 'Stable Value'
    END AS value_trend
FROM (
    SELECT 
        r.customer_id,
        r.rental_id,
        r.rental_date,
        p.amount AS current_payment,
        f.title AS film_title,
        c.name AS category,
        
        -- Rental precedente
        LAG(r.rental_date) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS prev_rental_date,
        LAG(p.amount) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS prev_payment,
        LAG(f.title) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS prev_film,
        
        -- Calcoli temporali
        DATEDIFF(
            r.rental_date, 
            LAG(r.rental_date) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date)
        ) AS days_between_rentals,
        
        -- Cambiamenti di valore
        p.amount - LAG(p.amount) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS payment_change,
        
        -- Numero sequenziale rental per customer
        ROW_NUMBER() OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS rental_sequence
        
    FROM rental r
    JOIN payment p ON r.rental_id = p.rental_id
    JOIN inventory i ON r.inventory_id = i.inventory_id
    JOIN film f ON i.film_id = f.film_id
    JOIN film_category fc ON f.film_id = fc.film_id
    JOIN category c ON fc.category_id = c.category_id
) customer_journey
WHERE rental_sequence > 1  -- Esclude il primo rental
  AND customer_id <= 10
ORDER BY customer_id, rental_sequence;
```

---

## LEAD

**Sintassi:** `LEAD(expr [, offset [, default]]) OVER ([PARTITION BY col] ORDER BY col)`

**Descrizione:** Accede al valore di una riga successiva nella finestra.

**Esempio Sakila:**
```sql
-- Previsione e analisi forward-looking
SELECT 
    future_analysis.*,
    CASE 
        WHEN next_rental_days <= 1 THEN 'Same Day Return Customer'
        WHEN next_rental_days <= 7 THEN 'Weekly Regular'
        WHEN next_rental_days <= 30 THEN 'Monthly Customer'
        WHEN next_rental_days IS NULL THEN 'Last Rental'
        ELSE 'Irregular Pattern'
    END AS customer_behavior_pattern
FROM (
    SELECT 
        r.customer_id,
        CONCAT(cust.first_name, ' ', cust.last_name) AS customer_name,
        r.rental_date,
        f.title AS current_film,
        p.amount AS current_payment,
        
        -- Prossimo rental info
        LEAD(r.rental_date) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS next_rental_date,
        LEAD(f.title) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS next_film,
        LEAD(p.amount) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) AS next_payment,
        
        -- Calcoli forward
        DATEDIFF(
            LEAD(r.rental_date) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date),
            r.rental_date
        ) AS next_rental_days,
        
        -- Predizioni di valore
        LEAD(p.amount) OVER (PARTITION BY r.customer_id ORDER BY r.rental_date) - p.amount AS next_payment_change
        
    FROM rental r
    JOIN customer cust ON r.customer_id = cust.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    JOIN inventory i ON r.inventory_id = i.inventory_id
    JOIN film f ON i.film_id = f.film_id
) future_analysis
WHERE customer_id <= 5
ORDER BY customer_id, rental_date;
```

---

## FIRST_VALUE / LAST_VALUE

**Sintassi:** 
- `FIRST_VALUE(expr) OVER (window_spec)`
- `LAST_VALUE(expr) OVER (window_spec)`

**Descrizione:** Restituisce il primo o ultimo valore nella finestra specificata.

**Esempio Sakila:**
```sql
-- Analisi primo e ultimo comportamento per categoria
SELECT 
    category_timeline.*,
    CASE 
        WHEN current_rental_rate = first_rate_in_category THEN 'Entry Level Pricing'
        WHEN current_rental_rate = last_rate_in_category THEN 'Premium Pricing'
        ELSE 'Mid-tier Pricing'
    END AS pricing_position,
    
    ROUND(
        (current_rental_rate - first_rate_in_category) / 
        NULLIF(last_rate_in_category - first_rate_in_category, 0) * 100, 2
    ) AS price_range_percentage
FROM (
    SELECT 
        f.film_id,
        f.title,
        c.name AS category,
        f.rental_rate AS current_rental_rate,
        f.length,
        
        -- Primo e ultimo per category ordinati per prezzo
        FIRST_VALUE(f.rental_rate) OVER (
            PARTITION BY c.category_id 
            ORDER BY f.rental_rate 
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS first_rate_in_category,
        
        LAST_VALUE(f.rental_rate) OVER (
            PARTITION BY c.category_id 
            ORDER BY f.rental_rate 
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS last_rate_in_category,
        
        -- Primo e ultimo per durata
        FIRST_VALUE(f.title) OVER (
            PARTITION BY c.category_id 
            ORDER BY f.length 
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS shortest_film_in_category,
        
        LAST_VALUE(f.title) OVER (
            PARTITION BY c.category_id 
            ORDER BY f.length 
            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
        ) AS longest_film_in_category
        
    FROM film f
    JOIN film_category fc ON f.film_id = fc.film_id
    JOIN category c ON fc.category_id = c.category_id
) category_timeline
ORDER BY category, current_rental_rate;
```

---

## SUM() OVER

🔥 **Funzione molto utilizzata**

**Sintassi:** `SUM(expr) OVER ([PARTITION BY col] ORDER BY col [frame_clause])`

**Descrizione:** Calcola somme cumulative o mobili all'interno di finestre.

**Esempio Sakila:**
```sql
-- Running totals e analisi cumulative
SELECT 
    cumulative_analysis.*,
    CONCAT(ROUND(cumulative_revenue_pct, 2), '%') AS cumulative_revenue_display,
    CASE 
        WHEN cumulative_revenue_pct <= 80 THEN 'Core Revenue Driver'
        WHEN cumulative_revenue_pct <= 95 THEN 'Important Contributor'
        ELSE 'Long Tail'
    END AS revenue_importance
FROM (
    SELECT 
        DATE(p.payment_date) AS payment_date,
        COUNT(*) AS daily_transactions,
        SUM(p.amount) AS daily_revenue,
        
        -- Running totals
        SUM(COUNT(*)) OVER (ORDER BY DATE(p.payment_date)) AS cumulative_transactions,
        SUM(SUM(p.amount)) OVER (ORDER BY DATE(p.payment_date)) AS cumulative_revenue,
        
        -- Moving averages (7-day window)
        AVG(SUM(p.amount)) OVER (
            ORDER BY DATE(p.payment_date) 
            ROWS BETWEEN 6 PRECEDING AND CURRENT ROW
        ) AS avg_7day_revenue,
        
        -- Percentage of total
        ROUND(
            SUM(SUM(p.amount)) OVER (ORDER BY DATE(p.payment_date)) / 
            SUM(SUM(p.amount)) OVER () * 100, 2
        ) AS cumulative_revenue_pct,
        
        -- Revenue trend (3-day rolling sum)
        SUM(SUM(p.amount)) OVER (
            ORDER BY DATE(p.payment_date) 
            ROWS BETWEEN 2 PRECEDING AND CURRENT ROW
        ) AS rolling_3day_revenue
        
    FROM payment p
    GROUP BY DATE(p.payment_date)
    ORDER BY payment_date
) cumulative_analysis
LIMIT 30;
```

```sql
-- Customer lifetime value accumulation
SELECT 
    clv_analysis.*,
    CASE 
        WHEN customer_percentile <= 20 THEN 'Top 20% CLV'
        WHEN customer_percentile <= 50 THEN 'Top 50% CLV'
        ELSE 'Standard CLV'
    END AS clv_segment
FROM (
    SELECT 
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        r.rental_date,
        p.amount AS transaction_amount,
        
        -- CLV accumulation
        SUM(p.amount) OVER (
            PARTITION BY c.customer_id 
            ORDER BY r.rental_date
        ) AS customer_lifetime_value,
        
        -- Transaction sequence
        ROW_NUMBER() OVER (PARTITION BY c.customer_id ORDER BY r.rental_date) AS transaction_sequence,
        
        -- Average transaction value up to this point
        AVG(p.amount) OVER (
            PARTITION BY c.customer_id 
            ORDER BY r.rental_date
        ) AS avg_transaction_to_date,
        
        -- Percentile ranking based on current CLV
        PERCENT_RANK() OVER (ORDER BY SUM(p.amount) OVER (PARTITION BY c.customer_id ORDER BY r.rental_date)) AS customer_percentile_running
        
    FROM customer c
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
) clv_inner
JOIN (
    SELECT 
        customer_id,
        PERCENT_RANK() OVER (ORDER BY SUM(amount)) * 100 AS customer_percentile
    FROM payment p2
    JOIN rental r2 ON p2.rental_id = r2.rental_id
    GROUP BY customer_id
) clv_analysis ON clv_inner.customer_id = clv_analysis.customer_id
WHERE clv_inner.customer_id <= 10
ORDER BY clv_inner.customer_id, clv_inner.transaction_sequence;
```

---

## Esempi Pratici Combinati

### Dashboard Analitico Completo
```sql
-- Dashboard business intelligence completo con window functions
SELECT 
    'CUSTOMER_ANALYTICS' AS dashboard_section,
    customer_analytics.*
FROM (
    SELECT 
        c.customer_id,
        CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
        COUNT(r.rental_id) AS total_rentals,
        SUM(p.amount) AS total_spent,
        AVG(p.amount) AS avg_payment,
        
        -- Rankings multipli
        ROW_NUMBER() OVER (ORDER BY SUM(p.amount) DESC) AS revenue_rank,
        DENSE_RANK() OVER (ORDER BY COUNT(r.rental_id) DESC) AS frequency_rank,
        NTILE(5) OVER (ORDER BY SUM(p.amount) DESC) AS revenue_quintile,
        
        -- Percentili
        ROUND(PERCENT_RANK() OVER (ORDER BY SUM(p.amount)) * 100, 1) AS revenue_percentile,
        
        -- Comparazioni con LAG
        LAG(SUM(p.amount)) OVER (ORDER BY c.customer_id) AS prev_customer_revenue,
        
        -- Running totals
        SUM(SUM(p.amount)) OVER (ORDER BY SUM(p.amount) DESC) AS cumulative_revenue_contribution,
        
        -- Business segmentation
        CASE 
            WHEN NTILE(5) OVER (ORDER BY SUM(p.amount) DESC) = 1 THEN '💎 VIP (Top 20%)'
            WHEN NTILE(5) OVER (ORDER BY SUM(p.amount) DESC) = 2 THEN '🥇 Gold (Next 20%)'
            WHEN NTILE(5) OVER (ORDER BY SUM(p.amount) DESC) = 3 THEN '🥈 Silver (Middle 20%)'
            WHEN NTILE(5) OVER (ORDER BY SUM(p.amount) DESC) = 4 THEN '🥉 Bronze (Next 20%)'
            ELSE '📊 Standard (Bottom 20%)'
        END AS customer_tier
        
    FROM customer c
    JOIN rental r ON c.customer_id = r.customer_id
    JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY c.customer_id, c.first_name, c.last_name
    HAVING COUNT(r.rental_id) >= 10
    ORDER BY total_spent DESC
    LIMIT 20
) customer_analytics

UNION ALL

SELECT 
    'FILM_PERFORMANCE',
    film_performance.*
FROM (
    SELECT 
        f.film_id,
        f.title,
        c.name AS category,
        COUNT(r.rental_id) AS rental_count,
        SUM(p.amount) AS total_revenue,
        
        -- Performance rankings
        RANK() OVER (ORDER BY COUNT(r.rental_id) DESC) AS popularity_rank,
        RANK() OVER (PARTITION BY c.category_id ORDER BY COUNT(r.rental_id) DESC) AS rank_in_category,
        
        -- Market share analysis
        ROUND(
            COUNT(r.rental_id) * 100.0 / 
            SUM(COUNT(r.rental_id)) OVER (), 2
        ) AS market_share_pct,
        
        -- Category performance
        ROUND(
            COUNT(r.rental_id) * 100.0 / 
            SUM(COUNT(r.rental_id)) OVER (PARTITION BY c.category_id), 2
        ) AS category_share_pct,
        
        -- Revenue contribution
        ROUND(
            SUM(p.amount) * 100.0 / 
            SUM(SUM(p.amount)) OVER (), 2
        ) AS revenue_contribution_pct
        
    FROM film f
    JOIN film_category fc ON f.film_id = fc.film_id
    JOIN category c ON fc.category_id = c.category_id
    JOIN inventory i ON f.film_id = i.film_id
    JOIN rental r ON i.inventory_id = r.inventory_id
    JOIN payment p ON r.rental_id = p.rental_id
    GROUP BY f.film_id, f.title, c.category_id, c.name
    ORDER BY rental_count DESC
    LIMIT 15
) film_performance

ORDER BY dashboard_section, revenue_rank;
```

### Cohort Analysis Avanzato
```sql
-- Cohort analysis completo con retention
SELECT 
    cohort_analysis.*,
    CASE 
        WHEN retention_rate >= 80 THEN 'Excellent Retention'
        WHEN retention_rate >= 60 THEN 'Good Retention'
        WHEN retention_rate >= 40 THEN 'Average Retention'
        ELSE 'Poor Retention'
    END AS retention_category
FROM (
    SELECT 
        cohort_month,
        period_number,
        customers_in_period,
        original_cohort_size,
        
        -- Retention rate calculation
        ROUND(
            customers_in_period * 100.0 / 
            FIRST_VALUE(customers_in_period) OVER (
                PARTITION BY cohort_month 
                ORDER BY period_number
                ROWS UNBOUNDED PRECEDING
            ), 2
        ) AS retention_rate,
        
        -- Cumulative retention
        SUM(customers_in_period) OVER (
            PARTITION BY cohort_month 
            ORDER BY period_number
        ) AS cumulative_active_customers,
        
        -- Period-over-period change
        customers_in_period - LAG(customers_in_period) OVER (
            PARTITION BY cohort_month 
            ORDER BY period_number
        ) AS customer_change,
        
        -- Revenue per cohort period
        revenue_in_period,
        
        -- Revenue retention
        ROUND(
            revenue_in_period * 100.0 / 
            FIRST_VALUE(revenue_in_period) OVER (
                PARTITION BY cohort_month 
                ORDER BY period_number
                ROWS UNBOUNDED PRECEDING
            ), 2
        ) AS revenue_retention_rate
        
    FROM (
        SELECT 
            customer_cohorts.cohort_month,
            customer_activity.activity_month,
            DATEDIFF(customer_activity.activity_month, customer_cohorts.cohort_month) / 30 AS period_number,
            COUNT(DISTINCT customer_activity.customer_id) AS customers_in_period,
            customer_cohorts.original_cohort_size,
            SUM(customer_activity.monthly_revenue) AS revenue_in_period
            
        FROM (
            -- Customer cohorts definition
            SELECT 
                customer_id,
                DATE_FORMAT(MIN(rental_date), '%Y-%m-01') AS cohort_month,
                COUNT(*) AS original_cohort_size
            FROM rental
            GROUP BY customer_id
        ) customer_cohorts
        
        JOIN (
            -- Monthly customer activity
            SELECT 
                r.customer_id,
                DATE_FORMAT(r.rental_date, '%Y-%m-01') AS activity_month,
                COUNT(r.rental_id) AS monthly_rentals,
                SUM(p.amount) AS monthly_revenue
            FROM rental r
            JOIN payment p ON r.rental_id = p.rental_id
            GROUP BY r.customer_id, DATE_FORMAT(r.rental_date, '%Y-%m-01')
        ) customer_activity ON customer_cohorts.customer_id = customer_activity.customer_id
        
        WHERE customer_activity.activity_month >= customer_cohorts.cohort_month
        GROUP BY 
            customer_cohorts.cohort_month,
            customer_activity.activity_month,
            customer_cohorts.original_cohort_size
    ) cohort_data
    WHERE period_number <= 6  -- Analyze first 6 months
) cohort_analysis
ORDER BY cohort_month, period_number;
```
