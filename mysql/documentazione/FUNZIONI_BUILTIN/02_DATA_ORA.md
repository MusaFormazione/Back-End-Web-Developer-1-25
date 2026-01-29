# 📅 Funzioni Data e Ora

MySQL offre un set completo di funzioni per gestire date, orari e intervalli temporali. Queste funzioni sono essenziali per applicazioni che richiedono elaborazione temporale.

---

## 📋 Indice

### Data e Ora Corrente
- [NOW](#now)
- [CURDATE / CURRENT_DATE](#curdate)
- [CURTIME / CURRENT_TIME](#curtime)
- [SYSDATE](#sysdate)
- [UTC_DATE](#utc_date)
- [UTC_TIME](#utc_time)
- [UTC_TIMESTAMP](#utc_timestamp)

### Estrazione Componenti
- [YEAR](#year)
- [MONTH](#month)
- [DAY / DAYOFMONTH](#day)
- [HOUR](#hour)
- [MINUTE](#minute)
- [SECOND](#second)
- [MICROSECOND](#microsecond)

### Nomi e Informazioni
- [DAYNAME](#dayname)
- [MONTHNAME](#monthname)
- [DAYOFWEEK](#dayofweek)
- [DAYOFYEAR](#dayofyear)
- [WEEK](#week)
- [WEEKOFYEAR](#weekofyear)
- [QUARTER](#quarter)

### Calcoli e Differenze
- [DATE_ADD / ADDDATE](#date_add)
- [DATE_SUB / SUBDATE](#date_sub)
- [DATEDIFF](#datediff)
- [TIMEDIFF](#timediff)
- [TIMESTAMPDIFF](#timestampdiff)
- [TIMESTAMPADD](#timestampadd)

### Formattazione e Parsing
- [DATE_FORMAT](#date_format)
- [TIME_FORMAT](#time_format)
- [STR_TO_DATE](#str_to_date)
- [CONVERT_TZ](#convert_tz)

### Utility e Conversioni
- [DATE](#date)
- [TIME](#time)
- [TIMESTAMP](#timestamp)
- [UNIX_TIMESTAMP](#unix_timestamp)
- [FROM_UNIXTIME](#from_unixtime)
- [MAKEDATE](#makedate)
- [MAKETIME](#maketime)

---

## NOW

🔥 **Funzione molto utilizzata**

**Sintassi:** `NOW([fsp])` dove fsp è la precisione dei microsecondi (0-6)

**Descrizione:** Restituisce la data e ora corrente del sistema.

**Esempio Sakila:**
```sql
-- Timestamp corrente per audit
SELECT 
    NOW() AS current_timestamp,
    NOW(3) AS current_timestamp_microsec;

-- Output: 2026-01-29 10:30:45, 2026-01-29 10:30:45.123
```

```sql
-- Log delle operazioni con timestamp
SELECT 
    rental_id,
    rental_date,
    return_date,
    NOW() AS query_executed_at,
    CASE 
        WHEN return_date IS NULL THEN 'In corso'
        ELSE 'Restituito'
    END AS status
FROM rental
LIMIT 5;
```

```sql
-- Calcolo età noleggi attivi
SELECT 
    rental_id,
    customer_id,
    rental_date,
    DATEDIFF(NOW(), rental_date) AS days_since_rental
FROM rental
WHERE return_date IS NULL
ORDER BY rental_date
LIMIT 10;
```

---

## CURDATE

🔥 **Funzione molto utilizzata**

**Sintassi:** `CURDATE()` o `CURRENT_DATE()` o `CURRENT_DATE`

**Descrizione:** Restituisce la data corrente (senza ora).

**Esempio Sakila:**
```sql
-- Noleggi di oggi
SELECT 
    COUNT(*) AS todays_rentals
FROM rental
WHERE DATE(rental_date) = CURDATE();
```

```sql
-- Clienti per età (approssimativa)
SELECT 
    customer_id,
    first_name,
    last_name,
    create_date,
    DATEDIFF(CURDATE(), create_date) AS days_as_customer,
    FLOOR(DATEDIFF(CURDATE(), create_date) / 365) AS years_as_customer
FROM customer
ORDER BY create_date
LIMIT 5;
```

---

## CURTIME

**Sintassi:** `CURTIME([fsp])` o `CURRENT_TIME([fsp])`

**Descrizione:** Restituisce l'ora corrente (senza data).

**Esempio Sakila:**
```sql
-- Orario corrente per report
SELECT 
    CURTIME() AS current_time,
    'Report generato alle ' AS message,
    TIME_FORMAT(CURTIME(), '%H:%i') AS friendly_time;
```

```sql
-- Analisi orari noleggi
SELECT 
    HOUR(rental_date) AS rental_hour,
    COUNT(*) AS rentals_count
FROM rental
GROUP BY HOUR(rental_date)
ORDER BY rental_hour;
```

---

## YEAR

🔥 **Funzione molto utilizzata**

**Sintassi:** `YEAR(date)`

**Descrizione:** Estrae l'anno da una data.

**Esempio Sakila:**
```sql
-- Noleggi per anno
SELECT 
    YEAR(rental_date) AS rental_year,
    COUNT(*) AS total_rentals,
    COUNT(DISTINCT customer_id) AS unique_customers
FROM rental
GROUP BY YEAR(rental_date)
ORDER BY rental_year;
```

```sql
-- Film per anno di uscita
SELECT 
    release_year,
    COUNT(*) AS films_count,
    AVG(rental_rate) AS avg_rental_rate,
    AVG(length) AS avg_duration
FROM film
GROUP BY release_year
ORDER BY release_year;
```

```sql
-- Trend temporali
SELECT 
    YEAR(rental_date) AS year,
    MONTH(rental_date) AS month,
    COUNT(*) AS rentals,
    SUM(amount) AS total_revenue
FROM rental r
JOIN payment p ON r.rental_id = p.rental_id
GROUP BY YEAR(rental_date), MONTH(rental_date)
ORDER BY year, month;
```

---

## MONTH

🔥 **Funzione molto utilizzata**

**Sintassi:** `MONTH(date)`

**Descrizione:** Estrae il mese (1-12) da una data.

**Esempio Sakila:**
```sql
-- Stagionalità noleggi
SELECT 
    MONTH(rental_date) AS rental_month,
    MONTHNAME(rental_date) AS month_name,
    COUNT(*) AS rentals_count,
    AVG(DATEDIFF(return_date, rental_date)) AS avg_rental_days
FROM rental
WHERE return_date IS NOT NULL
GROUP BY MONTH(rental_date), MONTHNAME(rental_date)
ORDER BY rental_month;
```

```sql
-- Performance mensile
SELECT 
    YEAR(payment_date) AS year,
    MONTH(payment_date) AS month,
    MONTHNAME(payment_date) AS month_name,
    COUNT(*) AS transactions,
    SUM(amount) AS total_amount,
    AVG(amount) AS avg_amount
FROM payment
GROUP BY YEAR(payment_date), MONTH(payment_date), MONTHNAME(payment_date)
ORDER BY year, month;
```

---

## DAY

🔥 **Funzione molto utilizzata**

**Sintassi:** `DAY(date)` o `DAYOFMONTH(date)`

**Descrizione:** Estrae il giorno del mese (1-31) da una data.

**Esempio Sakila:**
```sql
-- Noleggi per giorno del mese
SELECT 
    DAY(rental_date) AS day_of_month,
    COUNT(*) AS rentals_count
FROM rental
GROUP BY DAY(rental_date)
ORDER BY day_of_month;
```

```sql
-- Pagamenti di fine mese
SELECT 
    payment_id,
    amount,
    payment_date,
    DAY(payment_date) AS payment_day
FROM payment
WHERE DAY(payment_date) >= 28
ORDER BY payment_date
LIMIT 10;
```

---

## HOUR

**Sintassi:** `HOUR(time)`

**Descrizione:** Estrae l'ora (0-23) da un datetime o time.

**Esempio Sakila:**
```sql
-- Distribuzione oraria noleggi
SELECT 
    HOUR(rental_date) AS rental_hour,
    COUNT(*) AS rentals_count,
    CASE 
        WHEN HOUR(rental_date) BETWEEN 6 AND 11 THEN 'Mattina'
        WHEN HOUR(rental_date) BETWEEN 12 AND 17 THEN 'Pomeriggio'
        WHEN HOUR(rental_date) BETWEEN 18 AND 23 THEN 'Sera'
        ELSE 'Notte'
    END AS time_period
FROM rental
GROUP BY HOUR(rental_date)
ORDER BY rental_hour;
```

```sql
-- Orari di punta
SELECT 
    HOUR(rental_date) AS peak_hour,
    COUNT(*) AS activity
FROM rental
GROUP BY HOUR(rental_date)
HAVING COUNT(*) > (
    SELECT AVG(hourly_count) 
    FROM (
        SELECT COUNT(*) as hourly_count 
        FROM rental 
        GROUP BY HOUR(rental_date)
    ) t
)
ORDER BY activity DESC;
```

---

## DAYNAME

**Sintassi:** `DAYNAME(date)`

**Descrizione:** Restituisce il nome del giorno della settimana.

**Esempio Sakila:**
```sql
-- Noleggi per giorno della settimana
SELECT 
    DAYNAME(rental_date) AS day_name,
    DAYOFWEEK(rental_date) AS day_number,
    COUNT(*) AS rentals_count,
    AVG(HOUR(rental_date)) AS avg_rental_hour
FROM rental
GROUP BY DAYNAME(rental_date), DAYOFWEEK(rental_date)
ORDER BY day_number;
```

```sql
-- Pattern weekend vs weekday
SELECT 
    CASE 
        WHEN DAYOFWEEK(rental_date) IN (1, 7) THEN 'Weekend'
        ELSE 'Weekday'
    END AS day_type,
    COUNT(*) AS rentals,
    AVG(HOUR(rental_date)) AS avg_hour,
    SUM(amount) AS total_revenue
FROM rental r
JOIN payment p ON r.rental_id = p.rental_id
GROUP BY CASE 
    WHEN DAYOFWEEK(rental_date) IN (1, 7) THEN 'Weekend'
    ELSE 'Weekday'
END;
```

---

## DATE_ADD

🔥 **Funzione molto utilizzata**

**Sintassi:** `DATE_ADD(date, INTERVAL expr unit)` o `ADDDATE(date, INTERVAL expr unit)`

**Descrizione:** Aggiunge un intervallo di tempo a una data.

**Esempio Sakila:**
```sql
-- Calcolo date di scadenza
SELECT 
    rental_id,
    customer_id,
    rental_date,
    DATE_ADD(rental_date, INTERVAL 7 DAY) AS due_date,
    DATE_ADD(rental_date, INTERVAL 1 WEEK) AS same_due_date,
    return_date,
    CASE 
        WHEN return_date IS NULL THEN 'In corso'
        WHEN return_date <= DATE_ADD(rental_date, INTERVAL 7 DAY) THEN 'In tempo'
        ELSE 'In ritardo'
    END AS return_status
FROM rental
LIMIT 10;
```

```sql
-- Proiezioni future
SELECT 
    'Oggi' AS period,
    COUNT(*) AS active_rentals
FROM rental
WHERE return_date IS NULL

UNION ALL

SELECT 
    'Fra 1 settimana' AS period,
    COUNT(*) AS projected_returns
FROM rental
WHERE return_date IS NULL 
  AND rental_date <= DATE_ADD(CURDATE(), INTERVAL -1 WEEK);
```

```sql
-- Analisi retention clienti
SELECT 
    customer_id,
    MIN(rental_date) AS first_rental,
    MAX(rental_date) AS last_rental,
    DATE_ADD(MIN(rental_date), INTERVAL 30 DAY) AS retention_target,
    CASE 
        WHEN MAX(rental_date) >= DATE_ADD(MIN(rental_date), INTERVAL 30 DAY) 
        THEN 'Retained'
        ELSE 'Not Retained'
    END AS retention_status
FROM rental
GROUP BY customer_id
LIMIT 10;
```

---

## DATE_SUB

🔥 **Funzione molto utilizzata**

**Sintassi:** `DATE_SUB(date, INTERVAL expr unit)` o `SUBDATE(date, INTERVAL expr unit)`

**Descrizione:** Sottrae un intervallo di tempo da una data.

**Esempio Sakila:**
```sql
-- Noleggi degli ultimi 30 giorni
SELECT 
    COUNT(*) AS recent_rentals,
    COUNT(DISTINCT customer_id) AS active_customers
FROM rental
WHERE rental_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);
```

```sql
-- Trend settimanale
SELECT 
    'Questa settimana' AS period,
    COUNT(*) AS rentals
FROM rental
WHERE rental_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)

UNION ALL

SELECT 
    'Settimana scorsa' AS period,
    COUNT(*) AS rentals
FROM rental
WHERE rental_date >= DATE_SUB(CURDATE(), INTERVAL 2 WEEK)
  AND rental_date < DATE_SUB(CURDATE(), INTERVAL 1 WEEK);
```

---

## DATEDIFF

🔥 **Funzione molto utilizzata**

**Sintassi:** `DATEDIFF(date1, date2)`

**Descrizione:** Calcola la differenza in giorni tra due date (date1 - date2).

**Esempio Sakila:**
```sql
-- Durata noleggi
SELECT 
    rental_id,
    customer_id,
    rental_date,
    return_date,
    DATEDIFF(return_date, rental_date) AS rental_duration_days,
    CASE 
        WHEN DATEDIFF(return_date, rental_date) <= 3 THEN 'Breve'
        WHEN DATEDIFF(return_date, rental_date) <= 7 THEN 'Normale'
        ELSE 'Lungo'
    END AS rental_type
FROM rental
WHERE return_date IS NOT NULL
ORDER BY rental_duration_days DESC
LIMIT 10;
```

```sql
-- Analisi ritardi
SELECT 
    AVG(DATEDIFF(return_date, rental_date)) AS avg_rental_days,
    MIN(DATEDIFF(return_date, rental_date)) AS min_rental_days,
    MAX(DATEDIFF(return_date, rental_date)) AS max_rental_days,
    COUNT(CASE WHEN DATEDIFF(return_date, rental_date) > 7 THEN 1 END) AS late_returns
FROM rental
WHERE return_date IS NOT NULL;
```

```sql
-- Clienti più fedeli
SELECT 
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    MIN(r.rental_date) AS first_rental,
    MAX(r.rental_date) AS last_rental,
    DATEDIFF(MAX(r.rental_date), MIN(r.rental_date)) AS customer_lifespan_days,
    COUNT(r.rental_id) AS total_rentals
FROM customer c
JOIN rental r ON c.customer_id = r.customer_id
GROUP BY c.customer_id, c.first_name, c.last_name
HAVING COUNT(r.rental_id) > 30
ORDER BY customer_lifespan_days DESC
LIMIT 10;
```

---

## DATE_FORMAT

🔥 **Funzione molto utilizzata**

**Sintassi:** `DATE_FORMAT(date, format)`

**Descrizione:** Formatta una data secondo il pattern specificato.

**Formati comuni:**
- `%Y` - Anno a 4 cifre
- `%y` - Anno a 2 cifre  
- `%m` - Mese numerico (01-12)
- `%c` - Mese numerico (1-12)
- `%M` - Nome mese completo
- `%b` - Nome mese abbreviato
- `%d` - Giorno del mese (01-31)
- `%e` - Giorno del mese (1-31)
- `%W` - Nome giorno completo
- `%a` - Nome giorno abbreviato
- `%H` - Ora (00-23)
- `%h` - Ora (01-12)
- `%i` - Minuti (00-59)
- `%s` - Secondi (00-59)

**Esempio Sakila:**
```sql
-- Formattazioni diverse per report
SELECT 
    rental_id,
    rental_date,
    DATE_FORMAT(rental_date, '%W, %M %e, %Y') AS formal_date,
    DATE_FORMAT(rental_date, '%d/%m/%Y %H:%i') AS european_format,
    DATE_FORMAT(rental_date, '%Y-%m-%d') AS iso_date,
    DATE_FORMAT(rental_date, '%b %Y') AS month_year
FROM rental
LIMIT 5;

-- Output esempi:
-- "Wednesday, January 29, 2026"
-- "29/01/2026 14:30"
-- "2026-01-29"
-- "Jan 2026"
```

```sql
-- Report mensile formattato
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') AS year_month,
    DATE_FORMAT(payment_date, '%M %Y') AS month_name,
    COUNT(*) AS transactions,
    SUM(amount) AS total_amount,
    DATE_FORMAT(SUM(amount), '$%,.2f') AS formatted_amount
FROM payment
GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
ORDER BY year_month
LIMIT 12;
```

```sql
-- Log formattato per audit
SELECT 
    rental_id,
    customer_id,
    DATE_FORMAT(rental_date, 'Noleggiato il %d %M %Y alle ore %H:%i') AS rental_log,
    CASE 
        WHEN return_date IS NOT NULL 
        THEN DATE_FORMAT(return_date, 'Restituito il %d %M %Y alle ore %H:%i')
        ELSE 'Ancora in prestito'
    END AS return_log
FROM rental
LIMIT 5;
```

---

## STR_TO_DATE

**Sintassi:** `STR_TO_DATE(str, format)`

**Descrizione:** Converte una stringa in data usando il formato specificato.

**Esempio Sakila:**
```sql
-- Parsing date da formati diversi
SELECT 
    STR_TO_DATE('2026-01-29', '%Y-%m-%d') AS iso_date,
    STR_TO_DATE('29/01/2026', '%d/%m/%Y') AS european_date,
    STR_TO_DATE('January 29, 2026', '%M %d, %Y') AS us_date,
    STR_TO_DATE('29-Jan-26', '%d-%b-%y') AS short_date;
```

```sql
-- Importazione dati con date in formato testo
SELECT 
    'Customer A' AS customer_name,
    STR_TO_DATE('15/03/2026 14:30:00', '%d/%m/%Y %H:%i:%s') AS parsed_datetime,
    STR_TO_DATE('15/03/2026', '%d/%m/%Y') AS parsed_date_only;
```

---

## TIMESTAMPDIFF

🔥 **Funzione molto utilizzata**

**Sintassi:** `TIMESTAMPDIFF(unit, datetime1, datetime2)`

**Units:** MICROSECOND, SECOND, MINUTE, HOUR, DAY, WEEK, MONTH, QUARTER, YEAR

**Descrizione:** Calcola la differenza tra due timestamp nell'unità specificata.

**Esempio Sakila:**
```sql
-- Durata noleggi in diverse unità
SELECT 
    rental_id,
    rental_date,
    return_date,
    TIMESTAMPDIFF(DAY, rental_date, return_date) AS days_rented,
    TIMESTAMPDIFF(HOUR, rental_date, return_date) AS hours_rented,
    TIMESTAMPDIFF(MINUTE, rental_date, return_date) AS minutes_rented
FROM rental
WHERE return_date IS NOT NULL
LIMIT 5;
```

```sql
-- Analisi età clienti (approssimativa dalla data di registrazione)
SELECT 
    customer_id,
    first_name,
    last_name,
    create_date,
    TIMESTAMPDIFF(YEAR, create_date, NOW()) AS years_registered,
    TIMESTAMPDIFF(MONTH, create_date, NOW()) AS months_registered,
    TIMESTAMPDIFF(DAY, create_date, NOW()) AS days_registered
FROM customer
ORDER BY create_date
LIMIT 10;
```

```sql
-- Performance temporale staff
SELECT 
    s.staff_id,
    s.first_name,
    s.last_name,
    COUNT(r.rental_id) AS total_rentals,
    MIN(r.rental_date) AS first_rental,
    MAX(r.rental_date) AS last_rental,
    TIMESTAMPDIFF(MONTH, MIN(r.rental_date), MAX(r.rental_date)) AS active_months
FROM staff s
LEFT JOIN rental r ON s.staff_id = r.staff_id
GROUP BY s.staff_id, s.first_name, s.last_name;
```

---

## QUARTER

**Sintassi:** `QUARTER(date)`

**Descrizione:** Restituisce il trimestre (1-4) di una data.

**Esempio Sakila:**
```sql
-- Analisi trimestrale
SELECT 
    YEAR(rental_date) AS year,
    QUARTER(rental_date) AS quarter,
    CONCAT('Q', QUARTER(rental_date), ' ', YEAR(rental_date)) AS quarter_label,
    COUNT(*) AS rentals_count,
    SUM(amount) AS total_revenue
FROM rental r
JOIN payment p ON r.rental_id = p.rental_id
GROUP BY YEAR(rental_date), QUARTER(rental_date)
ORDER BY year, quarter;
```

---

## WEEK

**Sintassi:** `WEEK(date [, mode])`

**Descrizione:** Restituisce il numero della settimana (0-53) nell'anno.

**Esempio Sakila:**
```sql
-- Noleggi per settimana
SELECT 
    YEAR(rental_date) AS year,
    WEEK(rental_date) AS week_number,
    DATE_FORMAT(rental_date, '%Y-W%u') AS week_label,
    COUNT(*) AS rentals_count
FROM rental
GROUP BY YEAR(rental_date), WEEK(rental_date)
ORDER BY year, week_number
LIMIT 10;
```

---

## UNIX_TIMESTAMP

**Sintassi:** `UNIX_TIMESTAMP([date])`

**Descrizione:** Converte una data in timestamp Unix (secondi dal 1970-01-01).

**Esempio Sakila:**
```sql
-- Timestamp Unix per API
SELECT 
    rental_id,
    rental_date,
    UNIX_TIMESTAMP(rental_date) AS rental_timestamp,
    return_date,
    UNIX_TIMESTAMP(return_date) AS return_timestamp
FROM rental
WHERE return_date IS NOT NULL
LIMIT 5;
```

---

## FROM_UNIXTIME

**Sintassi:** `FROM_UNIXTIME(unix_timestamp [, format])`

**Descrizione:** Converte un timestamp Unix in data/ora.

**Esempio Sakila:**
```sql
-- Conversione da timestamp
SELECT 
    1706515200 AS unix_timestamp,
    FROM_UNIXTIME(1706515200) AS converted_datetime,
    FROM_UNIXTIME(1706515200, '%Y-%m-%d') AS converted_date,
    FROM_UNIXTIME(1706515200, '%H:%i:%s') AS converted_time;
```

---

## Esempi Pratici Combinati

### Dashboard Temporale Completo
```sql
-- Dashboard attività rental con multiple metriche temporali
SELECT 
    DATE_FORMAT(rental_date, '%Y-%m') AS month,
    COUNT(*) AS total_rentals,
    COUNT(DISTINCT customer_id) AS unique_customers,
    AVG(TIMESTAMPDIFF(DAY, rental_date, return_date)) AS avg_rental_days,
    COUNT(CASE WHEN DAYOFWEEK(rental_date) IN (1,7) THEN 1 END) AS weekend_rentals,
    COUNT(CASE WHEN HOUR(rental_date) BETWEEN 18 AND 22 THEN 1 END) AS evening_rentals
FROM rental
WHERE return_date IS NOT NULL
GROUP BY DATE_FORMAT(rental_date, '%Y-%m')
ORDER BY month;
```

### Analisi Stagionale Avanzata
```sql
-- Analisi comportamenti stagionali
SELECT 
    QUARTER(rental_date) AS quarter,
    MONTHNAME(rental_date) AS month_name,
    DAYNAME(rental_date) AS day_name,
    HOUR(rental_date) AS hour,
    COUNT(*) as frequency,
    RANK() OVER (PARTITION BY QUARTER(rental_date) ORDER BY COUNT(*) DESC) as popularity_rank
FROM rental
GROUP BY QUARTER(rental_date), MONTH(rental_date), DAYOFWEEK(rental_date), HOUR(rental_date)
HAVING COUNT(*) > 10
ORDER BY quarter, frequency DESC;
```

### Report Scadenze e Alerting
```sql
-- Sistema di alerting per scadenze
SELECT 
    r.rental_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    f.title AS film_title,
    r.rental_date,
    DATE_ADD(r.rental_date, INTERVAL 7 DAY) AS due_date,
    DATEDIFF(CURDATE(), DATE_ADD(r.rental_date, INTERVAL 7 DAY)) AS days_overdue,
    CASE 
        WHEN r.return_date IS NOT NULL THEN 'Returned'
        WHEN CURDATE() > DATE_ADD(r.rental_date, INTERVAL 7 DAY) THEN 'OVERDUE'
        WHEN CURDATE() = DATE_ADD(r.rental_date, INTERVAL 7 DAY) THEN 'DUE TODAY'
        WHEN DATEDIFF(DATE_ADD(r.rental_date, INTERVAL 7 DAY), CURDATE()) <= 1 THEN 'DUE SOON'
        ELSE 'OK'
    END AS status,
    DATE_FORMAT(DATE_ADD(r.rental_date, INTERVAL 7 DAY), '%W, %M %e') AS due_date_formatted
FROM rental r
JOIN customer c ON r.customer_id = c.customer_id
JOIN inventory i ON r.inventory_id = i.inventory_id
JOIN film f ON i.film_id = f.film_id
WHERE r.return_date IS NULL
ORDER BY due_date;
```
