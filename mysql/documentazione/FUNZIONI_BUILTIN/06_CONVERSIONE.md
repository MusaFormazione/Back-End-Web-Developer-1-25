# 🔄 Funzioni di Conversione

Le funzioni di conversione permettono di trasformare dati tra diversi tipi, formati e rappresentazioni, essenziali per l'integrazione e la presentazione dei dati.

---

## 📋 Indice

### Conversioni Tipo Base
- [CAST](#cast)
- [CONVERT](#convert)
- [BINARY](#binary)

### Conversioni Numeriche
- [FORMAT](#format)
- [BIN](#bin)
- [OCT](#oct)
- [HEX](#hex_conv)
- [UNHEX](#unhex)
- [CONV](#conv)

### Conversioni Data/Ora
- [DATE](#date)
- [TIME](#time)
- [DATETIME](#datetime)
- [TIMESTAMP](#timestamp)
- [STR_TO_DATE](#str_to_date)
- [DATE_FORMAT](#date_format)
- [UNIX_TIMESTAMP](#unix_timestamp)
- [FROM_UNIXTIME](#from_unixtime)

### Conversioni Stringhe
- [CHAR](#char)
- [ASCII](#ascii)
- [ORD](#ord)

### Conversioni JSON
- [JSON_EXTRACT](#json_extract)
- [JSON_UNQUOTE](#json_unquote)
- [CAST AS JSON](#cast_json)

---

## CAST

🔥 **Funzione molto utilizzata**

**Sintassi:** `CAST(expr AS type)`

**Tipi supportati:** 
- `CHAR[(length)]` - Stringa
- `SIGNED` - Intero con segno
- `UNSIGNED` - Intero senza segno
- `DECIMAL[(M[,D])]` - Decimale
- `DATE` - Data
- `TIME` - Ora
- `DATETIME` - Data e ora
- `JSON` - Documento JSON
- `BINARY` - Dati binari

**Descrizione:** Converte esplicitamente un'espressione in un tipo specifico.

**Esempio Sakila:**
```sql
-- Conversioni base per validazione e calcoli
SELECT 
    customer_id,
    first_name,
    last_name,
    create_date,
    
    -- Conversioni numeriche
    CAST(customer_id AS CHAR) AS customer_id_string,
    CAST(customer_id AS DECIMAL(10,2)) AS customer_id_decimal,
    CAST(customer_id AS SIGNED) AS customer_id_signed,
    
    -- Conversioni date
    CAST(create_date AS DATE) AS create_date_only,
    CAST(create_date AS TIME) AS create_time_only,
    CAST(DATE_FORMAT(create_date, '%Y%m%d') AS UNSIGNED) AS create_date_numeric,
    
    -- Conversioni stringa
    CAST(CONCAT(first_name, ' ', last_name) AS CHAR(50)) AS full_name_fixed_length
    
FROM customer
LIMIT 10;
```

```sql
-- Conversioni per aggregazioni e calcoli
SELECT 
    f.film_id,
    f.title,
    f.rental_rate,
    f.replacement_cost,
    f.length,
    
    -- Conversioni per calcoli precisi
    CAST(f.rental_rate AS DECIMAL(10,4)) AS precise_rental_rate,
    CAST(f.replacement_cost AS DECIMAL(10,2)) AS precise_replacement_cost,
    
    -- Conversioni per confronti
    CAST(f.length AS SIGNED) AS length_integer,
    CAST(f.rental_rate * 100 AS UNSIGNED) AS rental_rate_cents,
    
    -- Conversione a JSON per API
    CAST(JSON_OBJECT(
        'id', f.film_id,
        'title', f.title,
        'rate', f.rental_rate,
        'length', f.length
    ) AS JSON) AS film_json_data

FROM film f
LIMIT 8;
```

```sql
-- Conversioni avanzate per business logic
SELECT 
    r.rental_id,
    r.customer_id,
    r.rental_date,
    r.return_date,
    
    -- Conversioni temporali
    CAST(r.rental_date AS DATE) AS rental_date_only,
    CAST(DATEDIFF(r.return_date, r.rental_date) AS SIGNED) AS rental_days,
    
    -- Conversioni per categorizzazione
    CASE 
        WHEN CAST(HOUR(r.rental_date) AS UNSIGNED) BETWEEN 9 AND 17 THEN 'Business Hours'
        WHEN CAST(HOUR(r.rental_date) AS UNSIGNED) BETWEEN 18 AND 22 THEN 'Evening'
        ELSE 'Off Hours'
    END AS rental_time_category,
    
    -- Conversioni per codici
    CONCAT(
        'R',
        CAST(YEAR(r.rental_date) AS CHAR),
        LPAD(CAST(r.rental_id AS CHAR), 6, '0')
    ) AS rental_reference_code

FROM rental r
WHERE r.return_date IS NOT NULL
LIMIT 10;
```

---

## CONVERT

🔥 **Funzione molto utilizzata**

**Sintassi:** `CONVERT(expr, type)` o `CONVERT(expr USING charset)`

**Descrizione:** Converte dati tra tipi o charset diversi.

**Esempio Sakila:**
```sql
-- Conversioni tipo con CONVERT
SELECT 
    payment_id,
    customer_id,
    amount,
    payment_date,
    
    -- Conversioni numeriche
    CONVERT(amount, DECIMAL(8,2)) AS amount_decimal,
    CONVERT(amount * 100, UNSIGNED) AS amount_cents,
    CONVERT(ROUND(amount), SIGNED) AS amount_rounded,
    
    -- Conversioni stringhe
    CONVERT(payment_id, CHAR) AS payment_id_string,
    CONVERT(CONCAT('PAY-', payment_id), CHAR(20)) AS payment_reference,
    
    -- Conversioni date
    CONVERT(payment_date, DATE) AS payment_date_only,
    CONVERT(payment_date, TIME) AS payment_time_only

FROM payment
LIMIT 8;
```

```sql
-- Conversioni charset per internazionalizzazione
SELECT 
    film_id,
    title,
    description,
    
    -- Conversioni charset (esempi)
    CONVERT(title USING utf8mb4) AS title_utf8,
    CONVERT(UPPER(title) USING latin1) AS title_latin1_upper,
    
    -- Conversioni per normalizzazione
    CONVERT(TRIM(LOWER(title)), CHAR(255)) AS normalized_title,
    
    -- Conversioni per confronti case-insensitive
    CONVERT(title, BINARY) AS title_binary_comparison

FROM film
WHERE description IS NOT NULL
LIMIT 5;
```

---

## FORMAT

🔥 **Funzione molto utilizzata**

**Sintassi:** `FORMAT(number, decimal_places [, locale])`

**Descrizione:** Formatta numeri con separatori migliaia e decimali.

**Esempio Sakila:**
```sql
-- Formattazione per report finanziari
SELECT 
    'Financial Summary' AS report_type,
    FORMAT(SUM(amount), 2) AS total_revenue,
    FORMAT(AVG(amount), 2) AS average_payment,
    FORMAT(MAX(amount), 2) AS highest_payment,
    FORMAT(MIN(amount), 2) AS lowest_payment,
    FORMAT(COUNT(*), 0) AS total_transactions,
    FORMAT(STDDEV(amount), 4) AS revenue_std_deviation
FROM payment

UNION ALL

SELECT 
    'Inventory Value',
    FORMAT(SUM(replacement_cost), 2),
    FORMAT(AVG(replacement_cost), 2),
    FORMAT(MAX(replacement_cost), 2),
    FORMAT(MIN(replacement_cost), 2),
    FORMAT(COUNT(*), 0),
    FORMAT(STDDEV(replacement_cost), 4)
FROM film;
```

```sql
-- Dashboard performance formattato
SELECT 
    c.name AS category,
    FORMAT(COUNT(f.film_id), 0) AS total_films,
    FORMAT(AVG(f.length), 1) AS avg_duration_minutes,
    CONCAT(FORMAT(AVG(f.length)/60, 2), ' hours') AS avg_duration_hours,
    CONCAT('$', FORMAT(AVG(f.rental_rate), 2)) AS avg_rental_rate,
    CONCAT('$', FORMAT(SUM(f.replacement_cost), 2)) AS total_inventory_value,
    FORMAT((AVG(f.rental_rate)/AVG(f.replacement_cost))*100, 2) AS roi_percentage
FROM category c
JOIN film_category fc ON c.category_id = fc.category_id  
JOIN film f ON fc.film_id = f.film_id
GROUP BY c.category_id, c.name
ORDER BY SUM(f.replacement_cost) DESC;
```

---

## BIN

**Sintassi:** `BIN(number)`

**Descrizione:** Converte un numero in rappresentazione binaria.

**Esempio Sakila:**
```sql
-- Analisi pattern binari
SELECT 
    customer_id,
    first_name,
    last_name,
    active,
    
    -- Conversioni binarie per analisi pattern
    BIN(customer_id) AS customer_id_binary,
    BIN(active) AS active_binary,
    BIN(CHAR_LENGTH(first_name)) AS name_length_binary,
    
    -- Combinazioni binarie per categorizzazione
    CONCAT(BIN(active), BIN(customer_id % 2)) AS binary_pattern,
    
    CASE 
        WHEN BIN(customer_id) LIKE '%1111%' THEN 'Pattern A'
        WHEN BIN(customer_id) LIKE '%1010%' THEN 'Pattern B'
        WHEN BIN(customer_id) LIKE '%0000%' THEN 'Pattern C'
        ELSE 'Other Pattern'
    END AS binary_classification

FROM customer
WHERE customer_id <= 20
ORDER BY customer_id;
```

---

## HEX

**Sintassi:** `HEX(str_or_number)`

**Descrizione:** Converte una stringa o numero in rappresentazione esadecimale.

**Esempio Sakila:**
```sql
-- Conversioni esadecimali per encoding
SELECT 
    film_id,
    title,
    rental_rate,
    
    -- Conversioni numeriche in hex
    HEX(film_id) AS film_id_hex,
    HEX(rental_rate * 100) AS rental_rate_cents_hex,
    
    -- Conversioni stringhe in hex
    HEX(title) AS title_hex_encoded,
    HEX(SUBSTRING(title, 1, 10)) AS title_partial_hex,
    
    -- Codici univoci hex
    CONCAT('FILM-', HEX(film_id)) AS hex_reference_code,
    
    -- Conversioni per hash semplici
    RIGHT(HEX(CRC32(title)), 8) AS title_hash_hex

FROM film
LIMIT 8;
```

```sql
-- Sistema di codifica hex per sicurezza
SELECT 
    customer_id,
    email,
    
    -- Email encoding per privacy
    HEX(email) AS email_hex_encoded,
    
    -- ID encoding
    CONCAT('CUST-', HEX(customer_id)) AS customer_hex_id,
    
    -- Hash per riferimenti anonimi
    RIGHT(HEX(CRC32(CONCAT(customer_id, email))), 10) AS anonymous_reference

FROM customer
LIMIT 8;
```

---

## UNHEX

**Sintassi:** `UNHEX(hex_string)`

**Descrizione:** Converte una stringa esadecimale nella sua rappresentazione originale.

**Esempio Sakila:**
```sql
-- Decodifica dati hex
SELECT 
    'Encoded Data' AS data_type,
    '48656C6C6F20576F726C64' AS hex_data,
    UNHEX('48656C6C6F20576F726C64') AS decoded_data,
    
    -- Roundtrip encoding/decoding
    title AS original_title,
    HEX(title) AS title_encoded,
    UNHEX(HEX(title)) AS title_decoded
    
FROM film
WHERE film_id = 1;
```

---

## CONV

**Sintassi:** `CONV(number, from_base, to_base)`

**Descrizione:** Converte numeri tra diverse basi numeriche (2-36).

**Esempio Sakila:**
```sql
-- Conversioni tra basi numeriche
SELECT 
    film_id,
    title,
    
    -- Conversioni da base 10
    CONV(film_id, 10, 2) AS film_id_binary,
    CONV(film_id, 10, 8) AS film_id_octal,
    CONV(film_id, 10, 16) AS film_id_hex,
    CONV(film_id, 10, 36) AS film_id_base36,
    
    -- Codici alfanumerici per referenze
    CONCAT('REF-', CONV(film_id, 10, 36)) AS alphanumeric_reference,
    
    -- Sistema di numerazione custom per codici
    CASE 
        WHEN CHAR_LENGTH(CONV(film_id, 10, 2)) <= 8 THEN 'Short Code'
        WHEN CHAR_LENGTH(CONV(film_id, 10, 2)) <= 12 THEN 'Medium Code'
        ELSE 'Long Code'
    END AS code_category

FROM film
WHERE film_id <= 20
ORDER BY film_id;
```

```sql
-- Sistema di codifica avanzato
SELECT 
    customer_id,
    CONCAT(first_name, ' ', last_name) AS customer_name,
    
    -- Multi-base encoding per tracking
    CONCAT(
        CONV(customer_id, 10, 16),
        '-',
        CONV(DAYOFYEAR(create_date), 10, 36),
        '-',
        CONV(YEAR(create_date), 10, 36)
    ) AS encoded_customer_reference,
    
    -- Verifica conversioni roundtrip
    CONV(CONV(customer_id, 10, 16), 16, 10) AS roundtrip_verification

FROM customer
WHERE customer_id <= 15
ORDER BY customer_id;
```

---

## DATE

🔥 **Funzione molto utilizzata**

**Sintassi:** `DATE(datetime)`

**Descrizione:** Estrae la parte data da un datetime.

**Esempio Sakila:**
```sql
-- Estrazione date per aggregazioni
SELECT 
    DATE(rental_date) AS rental_date_only,
    COUNT(*) AS rentals_per_day,
    COUNT(DISTINCT customer_id) AS unique_customers,
    AVG(HOUR(rental_date)) AS avg_rental_hour,
    
    -- Conversioni per categorizzazione
    CASE DAYOFWEEK(DATE(rental_date))
        WHEN 1 THEN 'Sunday'
        WHEN 2 THEN 'Monday'
        WHEN 3 THEN 'Tuesday'
        WHEN 4 THEN 'Wednesday'
        WHEN 5 THEN 'Thursday'
        WHEN 6 THEN 'Friday'
        WHEN 7 THEN 'Saturday'
    END AS day_name,
    
    -- Conversioni per business logic
    CASE 
        WHEN DAYOFWEEK(DATE(rental_date)) IN (1, 7) THEN 'Weekend'
        ELSE 'Weekday'
    END AS day_type

FROM rental
GROUP BY DATE(rental_date)
ORDER BY rental_date_only
LIMIT 10;
```

---

## TIME

**Sintassi:** `TIME(datetime)`

**Descrizione:** Estrae la parte ora da un datetime.

**Esempio Sakila:**
```sql
-- Analisi pattern orari
SELECT 
    TIME(rental_date) AS rental_time,
    HOUR(TIME(rental_date)) AS rental_hour,
    MINUTE(TIME(rental_date)) AS rental_minute,
    
    -- Categorizzazione temporale
    CASE 
        WHEN TIME(rental_date) BETWEEN '06:00:00' AND '11:59:59' THEN 'Morning'
        WHEN TIME(rental_date) BETWEEN '12:00:00' AND '17:59:59' THEN 'Afternoon'
        WHEN TIME(rental_date) BETWEEN '18:00:00' AND '22:59:59' THEN 'Evening'
        ELSE 'Night/Early Morning'
    END AS time_period,
    
    COUNT(*) AS rental_count

FROM rental
GROUP BY TIME(rental_date)
HAVING COUNT(*) > 1
ORDER BY rental_time
LIMIT 15;
```

---

## STR_TO_DATE

🔥 **Funzione molto utilizzata**

**Sintassi:** `STR_TO_DATE(str, format)`

**Descrizione:** Converte una stringa in data usando un formato specifico.

**Esempio Sakila:**
```sql
-- Parsing date da formati diversi per importazione
SELECT 
    'Data Import Examples' AS example_type,
    
    -- Formati comuni di importazione
    STR_TO_DATE('2005-08-23 22:50:12', '%Y-%m-%d %H:%i:%s') AS iso_datetime,
    STR_TO_DATE('23/08/2005', '%d/%m/%Y') AS european_date,
    STR_TO_DATE('Aug 23, 2005', '%M %d, %Y') AS us_format,
    STR_TO_DATE('23-Aug-05 22:50', '%d-%b-%y %H:%i') AS compact_format,
    STR_TO_DATE('2005-235', '%Y-%j') AS julian_date,
    STR_TO_DATE('200534', '%Y%U') AS year_week_format;
```

```sql
-- Sistema di parsing flessibile per log esterni
SELECT 
    customer_id,
    email,
    create_date,
    
    -- Parsing e riconversione date
    DATE_FORMAT(create_date, '%d/%m/%Y') AS european_format,
    STR_TO_DATE(DATE_FORMAT(create_date, '%d/%m/%Y'), '%d/%m/%Y') AS parsed_back,
    
    -- Validazione conversione
    CASE 
        WHEN DATE(create_date) = STR_TO_DATE(DATE_FORMAT(create_date, '%Y-%m-%d'), '%Y-%m-%d') 
        THEN 'Valid Conversion'
        ELSE 'Conversion Error'
    END AS conversion_status
    
FROM customer
LIMIT 8;
```

---

## Esempi Pratici Combinati

### Sistema di Export Multi-Formato
```sql
-- Sistema di export dati in formati multipli
SELECT 
    'JSON_FORMAT' AS export_format,
    customer_id,
    CAST(JSON_OBJECT(
        'id', customer_id,
        'name', CONCAT(first_name, ' ', last_name),
        'email', email,
        'created', DATE_FORMAT(create_date, '%Y-%m-%d'),
        'active', CAST(active AS JSON)
    ) AS CHAR) AS exported_data
FROM customer
WHERE customer_id <= 3

UNION ALL

SELECT 
    'CSV_FORMAT',
    customer_id,
    CONCAT(
        CAST(customer_id AS CHAR), ',',
        CONVERT(CONCAT('"', first_name, ' ', last_name, '"'), CHAR), ',',
        CONVERT(email, CHAR), ',',
        DATE_FORMAT(create_date, '%Y-%m-%d'), ',',
        CAST(active AS CHAR)
    )
FROM customer
WHERE customer_id <= 3

UNION ALL

SELECT 
    'XML_FORMAT',
    customer_id,
    CONCAT(
        '<customer id="', CAST(customer_id AS CHAR), '">',
        '<name>', CONVERT(CONCAT(first_name, ' ', last_name), CHAR), '</name>',
        '<email>', CONVERT(email, CHAR), '</email>',
        '<created>', DATE_FORMAT(create_date, '%Y-%m-%d'), '</created>',
        '<active>', CAST(active AS CHAR), '</active>',
        '</customer>'
    )
FROM customer
WHERE customer_id <= 3;
```

### Sistema di Encoding/Hashing Completo
```sql
-- Sistema completo di encoding per sicurezza e tracking
SELECT 
    f.film_id,
    f.title,
    
    -- Hash multipli per referenze diverse
    CONCAT('FILM-', HEX(f.film_id)) AS hex_reference,
    CONCAT('REF-', CONV(f.film_id, 10, 36)) AS base36_reference,
    CONCAT('BIN-', RIGHT(CONCAT('000000', BIN(f.film_id)), 8)) AS binary_reference,
    
    -- Hash del titolo per deduplicazione
    RIGHT(HEX(CRC32(UPPER(TRIM(f.title)))), 8) AS title_hash,
    
    -- Encoding reversibile del titolo
    HEX(f.title) AS title_hex_encoded,
    UNHEX(HEX(f.title)) AS title_decoded_verification,
    
    -- Codice composito
    CONCAT(
        CONV(f.film_id, 10, 16),
        '-',
        CONV(f.length, 10, 36),
        '-',
        RIGHT(HEX(CRC32(f.title)), 6)
    ) AS composite_tracking_code,
    
    -- Formatting per display
    FORMAT(f.rental_rate, 2) AS formatted_rate,
    CAST(f.rental_rate AS DECIMAL(5,2)) AS precise_rate

FROM film f
WHERE f.film_id <= 10
ORDER BY f.film_id;
```

### Dashboard Business Intelligence con Conversioni
```sql
-- Dashboard BI completo con conversioni multiple
SELECT 
    conversion_type,
    metric_name,
    raw_value,
    formatted_value,
    encoded_value,
    calculation_timestamp
FROM (
    SELECT 
        'FINANCIAL' AS conversion_type,
        'Total Revenue' AS metric_name,
        SUM(amount) AS raw_value,
        CONCAT('$', FORMAT(SUM(amount), 2)) AS formatted_value,
        HEX(CAST(SUM(amount) * 100 AS UNSIGNED)) AS encoded_value,
        CAST(NOW() AS CHAR) AS calculation_timestamp
    FROM payment
    
    UNION ALL
    
    SELECT 
        'OPERATIONAL',
        'Active Customers',
        COUNT(DISTINCT customer_id),
        FORMAT(COUNT(DISTINCT customer_id), 0),
        CONV(COUNT(DISTINCT customer_id), 10, 36),
        CAST(NOW() AS CHAR)
    FROM rental
    
    UNION ALL
    
    SELECT 
        'INVENTORY',
        'Total Films',
        COUNT(*),
        CONCAT(FORMAT(COUNT(*), 0), ' films'),
        BIN(COUNT(*)),
        CAST(NOW() AS CHAR)
    FROM film
    
    UNION ALL
    
    SELECT 
        'PERFORMANCE',
        'Avg Rental Duration',
        AVG(DATEDIFF(return_date, rental_date)),
        CONCAT(FORMAT(AVG(DATEDIFF(return_date, rental_date)), 1), ' days'),
        CONV(FLOOR(AVG(DATEDIFF(return_date, rental_date))), 10, 16),
        CAST(NOW() AS CHAR)
    FROM rental
    WHERE return_date IS NOT NULL
) dashboard_metrics
ORDER BY conversion_type, metric_name;
```
