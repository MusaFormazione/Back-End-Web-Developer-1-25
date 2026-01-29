# 🔤 Funzioni Stringhe

MySQL offre un ricco set di funzioni per manipolare, analizzare e trasformare stringhe. Queste funzioni sono fondamentali per la gestione dei dati testuali.

---

## 📋 Indice

### Trasformazione Case
- [UPPER](#upper)
- [LOWER](#lower)
- [INITCAP](#initcap)

### Lunghezza e Spazi
- [LENGTH / CHAR_LENGTH](#length)
- [TRIM](#trim)
- [LTRIM](#ltrim)
- [RTRIM](#rtrim)
- [LPAD](#lpad)
- [RPAD](#rpad)

### Ricerca e Posizione
- [LOCATE / POSITION](#locate)
- [INSTR](#instr)
- [FIND_IN_SET](#find_in_set)
- [REGEXP / RLIKE](#regexp)

### Estrazione e Suddivisione
- [SUBSTRING](#substring)
- [LEFT](#left)
- [RIGHT](#right)
- [MID](#mid)
- [SUBSTRING_INDEX](#substring_index)

### Sostituzione e Concatenazione
- [REPLACE](#replace)
- [CONCAT](#concat)
- [CONCAT_WS](#concat_ws)
- [GROUP_CONCAT](#group_concat)
- [INSERT](#insert)

### Confronto e Analisi
- [STRCMP](#strcmp)
- [SOUNDEX](#soundex)
- [SPACE](#space)
- [REPEAT](#repeat)
- [REVERSE](#reverse)

### Codifica e Decodifica
- [ASCII](#ascii)
- [CHAR](#char)
- [HEX](#hex)
- [UNHEX](#unhex)
- [BIN](#bin)

---

## UPPER

🔥 **Funzione molto utilizzata**

**Sintassi:** `UPPER(str)`

**Descrizione:** Converte tutti i caratteri di una stringa in maiuscolo.

**Esempio Sakila:**
```sql
-- Convertire i titoli dei film in maiuscolo
SELECT 
    film_id,
    title,
    UPPER(title) AS title_uppercase
FROM film
WHERE film_id <= 5;

-- Output esempio:
-- | film_id | title            | title_uppercase  |
-- |---------|------------------|------------------|
-- | 1       | ACADEMY DINOSAUR | ACADEMY DINOSAUR |
-- | 2       | Ace Goldfinger   | ACE GOLDFINGER   |
```

```sql
-- Normalizzazione per ricerca case-insensitive
SELECT 
    customer_id,
    first_name,
    last_name
FROM customer
WHERE UPPER(first_name) = UPPER('mary')
LIMIT 3;

-- Trova tutti i clienti chiamati Mary (indipendentemente dal case)
```

```sql
-- Creazione di codici standardizzati
SELECT 
    actor_id,
    CONCAT(
        UPPER(LEFT(first_name, 2)),
        UPPER(LEFT(last_name, 2)),
        LPAD(actor_id, 3, '0')
    ) AS actor_code
FROM actor
LIMIT 5;

-- Output: PE AL001, NI WA002, etc.
```

---

## LOWER

🔥 **Funzione molto utilizzata**

**Sintassi:** `LOWER(str)`

**Descrizione:** Converte tutti i caratteri di una stringa in minuscolo.

**Esempio Sakila:**
```sql
-- Email standardizzate in minuscolo
SELECT 
    customer_id,
    email,
    LOWER(email) AS email_normalized
FROM customer
WHERE customer_id <= 3;
```

```sql
-- Creazione di slug per URL
SELECT 
    film_id,
    title,
    LOWER(REPLACE(title, ' ', '-')) AS url_slug
FROM film
WHERE film_id <= 5;

-- Output: "academy-dinosaur", "ace-goldfinger"
```

---

## LENGTH

🔥 **Funzione molto utilizzata**

**Sintassi:** `LENGTH(str)` o `CHAR_LENGTH(str)`

**Descrizione:** Restituisce la lunghezza di una stringa in byte (LENGTH) o caratteri (CHAR_LENGTH).

**Esempio Sakila:**
```sql
-- Analisi lunghezza titoli film
SELECT 
    title,
    LENGTH(title) AS byte_length,
    CHAR_LENGTH(title) AS char_length
FROM film
ORDER BY LENGTH(title) DESC
LIMIT 5;
```

```sql
-- Validazione lunghezza descrizioni
SELECT 
    film_id,
    title,
    CHAR_LENGTH(description) AS desc_length,
    CASE 
        WHEN CHAR_LENGTH(description) > 1000 THEN 'Lunga'
        WHEN CHAR_LENGTH(description) > 500 THEN 'Media'
        ELSE 'Corta'
    END AS desc_category
FROM film
LIMIT 5;
```

```sql
-- Statistiche sui nomi clienti
SELECT 
    AVG(CHAR_LENGTH(first_name)) AS avg_first_name_length,
    AVG(CHAR_LENGTH(last_name)) AS avg_last_name_length,
    MAX(CHAR_LENGTH(CONCAT(first_name, ' ', last_name))) AS max_full_name_length
FROM customer;
```

---

## TRIM

🔥 **Funzione molto utilizzata**

**Sintassi:** `TRIM([{BOTH | LEADING | TRAILING} [remstr] FROM] str)`

**Descrizione:** Rimuove spazi o caratteri specificati dall'inizio e/o fine di una stringa.

**Esempio Sakila:**
```sql
-- Pulizia dati con spazi extra
SELECT 
    '  ' || first_name || '  ' AS before_trim,
    TRIM('  ' || first_name || '  ') AS after_trim
FROM customer
LIMIT 3;
```

```sql
-- Rimozione caratteri specifici
SELECT 
    address,
    TRIM('.' FROM address) AS without_dots,
    TRIM(BOTH '()' FROM '(123) Main St') AS without_parentheses;

-- Output: rimuove punti dall'indirizzo
```

```sql
-- Pulizia avanzata
SELECT 
    title,
    TRIM(LEADING 'THE ' FROM UPPER(title)) AS title_without_the
FROM film
WHERE UPPER(title) LIKE 'THE %'
LIMIT 5;

-- Rimuove "THE " dall'inizio dei titoli
```

---

## CONCAT

🔥 **Funzione molto utilizzata**

**Sintassi:** `CONCAT(str1, str2, ...)`

**Descrizione:** Concatena due o più stringhe. Se uno degli argomenti è NULL, restituisce NULL.

**Esempio Sakila:**
```sql
-- Nome completo clienti
SELECT 
    customer_id,
    CONCAT(first_name, ' ', last_name) AS full_name
FROM customer
LIMIT 5;
```

```sql
-- Indirizzo completo
SELECT 
    a.address_id,
    CONCAT(
        a.address, ', ',
        c.city, ' ',
        a.postal_code, ', ',
        co.country
    ) AS full_address
FROM address a
JOIN city c ON a.city_id = c.city_id
JOIN country co ON c.country_id = co.country_id
LIMIT 3;
```

```sql
-- Etichette descrittive
SELECT 
    film_id,
    CONCAT(
        title, ' (',
        release_year, ') - ',
        rating, ' - ',
        length, ' min'
    ) AS film_label
FROM film
LIMIT 3;

-- Output: "ACADEMY DINOSAUR (2006) - PG - 86 min"
```

---

## CONCAT_WS

🔥 **Funzione molto utilizzata**

**Sintassi:** `CONCAT_WS(separator, str1, str2, ...)`

**Descrizione:** Concatena stringhe con un separatore. Ignora i valori NULL.

**Esempio Sakila:**
```sql
-- Nome con separatore, gestisce middle name NULL
SELECT 
    actor_id,
    CONCAT_WS(' ', first_name, NULL, last_name) AS full_name
FROM actor
LIMIT 5;

-- NULL viene ignorato, risultato: "first_name last_name"
```

```sql
-- CSV di categorie per film
SELECT 
    f.film_id,
    f.title,
    CONCAT_WS(', ', 
        c1.name,
        c2.name,
        c3.name
    ) AS categories
FROM film f
LEFT JOIN film_category fc1 ON f.film_id = fc1.film_id
LEFT JOIN category c1 ON fc1.category_id = c1.category_id
LEFT JOIN film_category fc2 ON f.film_id = fc2.film_id AND fc2.category_id != fc1.category_id
LEFT JOIN category c2 ON fc2.category_id = c2.category_id
LEFT JOIN film_category fc3 ON f.film_id = fc3.film_id AND fc3.category_id NOT IN (fc1.category_id, fc2.category_id)
LEFT JOIN category c3 ON fc3.category_id = c3.category_id
LIMIT 5;
```

---

## SUBSTRING

🔥 **Funzione molto utilizzata**

**Sintassi:** `SUBSTRING(str, pos [, len])` o `SUBSTR(str, pos [, len])`

**Descrizione:** Estrae una sottostringa dalla posizione specificata.

**Esempio Sakila:**
```sql
-- Prime 3 lettere del titolo
SELECT 
    film_id,
    title,
    SUBSTRING(title, 1, 3) AS title_prefix
FROM film
LIMIT 5;
```

```sql
-- Estrazione anno da data
SELECT 
    rental_id,
    rental_date,
    SUBSTRING(rental_date, 1, 4) AS year,
    SUBSTRING(rental_date, 6, 2) AS month,
    SUBSTRING(rental_date, 9, 2) AS day
FROM rental
LIMIT 5;
```

```sql
-- Codici postali abbreviati
SELECT 
    address_id,
    address,
    postal_code,
    SUBSTRING(postal_code, 1, 3) AS postal_prefix
FROM address
WHERE postal_code IS NOT NULL
LIMIT 5;
```

---

## LEFT

🔥 **Funzione molto utilizzata**

**Sintassi:** `LEFT(str, len)`

**Descrizione:** Restituisce i primi `len` caratteri di una stringa.

**Esempio Sakila:**
```sql
-- Iniziali nomi
SELECT 
    actor_id,
    CONCAT(
        LEFT(first_name, 1), 
        '.', 
        LEFT(last_name, 1), 
        '.'
    ) AS initials
FROM actor
LIMIT 5;

-- Output: "P.G.", "N.W.", etc.
```

```sql
-- Abbreviazioni titoli lunghi
SELECT 
    film_id,
    title,
    CASE 
        WHEN CHAR_LENGTH(title) > 15 
        THEN CONCAT(LEFT(title, 12), '...')
        ELSE title
    END AS title_abbreviated
FROM film
WHERE CHAR_LENGTH(title) > 10
LIMIT 5;
```

---

## RIGHT

**Sintassi:** `RIGHT(str, len)`

**Descrizione:** Restituisce gli ultimi `len` caratteri di una stringa.

**Esempio Sakila:**
```sql
-- Ultime 4 cifre numero telefono (simulato)
SELECT 
    customer_id,
    CONCAT('***-***-', RIGHT(CONCAT('0000', customer_id), 4)) AS phone_masked
FROM customer
LIMIT 5;
```

```sql
-- Estensioni file (simulato)
SELECT 
    'document.pdf' AS filename,
    RIGHT('document.pdf', 3) AS extension;

-- Output: pdf
```

---

## REPLACE

🔥 **Funzione molto utilizzata**

**Sintassi:** `REPLACE(str, from_str, to_str)`

**Descrizione:** Sostituisce tutte le occorrenze di `from_str` con `to_str` nella stringa `str`.

**Esempio Sakila:**
```sql
-- Normalizzazione titoli per URL
SELECT 
    film_id,
    title,
    LOWER(
        REPLACE(
            REPLACE(
                REPLACE(title, ' ', '-'),
                ':', ''
            ),
            ',', ''
        )
    ) AS url_slug
FROM film
LIMIT 5;
```

```sql
-- Mascheramento email per privacy
SELECT 
    customer_id,
    email,
    CONCAT(
        LEFT(email, 2),
        REPEAT('*', CHAR_LENGTH(email) - LOCATE('@', email) - 1),
        RIGHT(email, CHAR_LENGTH(email) - LOCATE('@', email) + 1)
    ) AS email_masked
FROM customer
LIMIT 5;
```

```sql
-- Pulizia descrizioni
SELECT 
    film_id,
    REPLACE(
        REPLACE(
            REPLACE(description, '  ', ' '),  -- doppi spazi
            '\n', ' '
        ),
        '\t', ' '
    ) AS description_clean
FROM film
LIMIT 3;
```

---

## LOCATE

🔥 **Funzione molto utilizzata**

**Sintassi:** `LOCATE(substr, str [, pos])` o `POSITION(substr IN str)`

**Descrizione:** Restituisce la posizione della prima occorrenza di `substr` in `str`.

**Esempio Sakila:**
```sql
-- Trova posizione @ nelle email
SELECT 
    customer_id,
    email,
    LOCATE('@', email) AS at_position,
    SUBSTRING(email, 1, LOCATE('@', email) - 1) AS username,
    SUBSTRING(email, LOCATE('@', email) + 1) AS domain
FROM customer
LIMIT 5;
```

```sql
-- Ricerca parole nei titoli
SELECT 
    film_id,
    title,
    LOCATE('LOVE', UPPER(title)) AS love_position,
    CASE 
        WHEN LOCATE('LOVE', UPPER(title)) > 0 THEN 'Contiene LOVE'
        ELSE 'Non contiene LOVE'
    END AS has_love
FROM film
WHERE LOCATE('LOVE', UPPER(title)) > 0
LIMIT 5;
```

---

## SUBSTRING_INDEX

**Sintassi:** `SUBSTRING_INDEX(str, delim, count)`

**Descrizione:** Restituisce la sottostringa prima di `count` occorrenze del delimitatore.

**Esempio Sakila:**
```sql
-- Estrazione dominio email
SELECT 
    customer_id,
    email,
    SUBSTRING_INDEX(email, '@', -1) AS domain,
    SUBSTRING_INDEX(email, '@', 1) AS username
FROM customer
LIMIT 5;
```

```sql
-- Parsing indirizzi con delimitatori
SELECT 
    address_id,
    address,
    SUBSTRING_INDEX(address, ' ', 1) AS house_number,
    SUBSTRING_INDEX(address, ' ', -1) AS street_suffix
FROM address
LIMIT 5;
```

---

## GROUP_CONCAT

🔥 **Funzione molto utilizzata**

**Sintassi:** `GROUP_CONCAT([DISTINCT] expr [ORDER BY col] [SEPARATOR sep])`

**Descrizione:** Concatena valori di un gruppo in una singola stringa.

**Esempio Sakila:**
```sql
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

```sql
-- Categorie per film
SELECT 
    f.film_id,
    f.title,
    GROUP_CONCAT(
        DISTINCT c.name
        ORDER BY c.name
        SEPARATOR ' | '
    ) AS categories
FROM film f
JOIN film_category fc ON f.film_id = fc.film_id
JOIN category c ON fc.category_id = c.category_id
GROUP BY f.film_id, f.title
LIMIT 5;
```

```sql
-- Lista film per attore (con conteggio)
SELECT 
    a.actor_id,
    CONCAT(a.first_name, ' ', a.last_name) AS actor_name,
    COUNT(f.film_id) AS film_count,
    GROUP_CONCAT(
        f.title
        ORDER BY f.title
        SEPARATOR '; '
    ) AS films
FROM actor a
JOIN film_actor fa ON a.actor_id = fa.actor_id
JOIN film f ON fa.film_id = f.film_id
GROUP BY a.actor_id, a.first_name, a.last_name
HAVING COUNT(f.film_id) >= 35
LIMIT 3;
```

---

## REGEXP / RLIKE

**Sintassi:** `str REGEXP pattern` o `str RLIKE pattern`

**Descrizione:** Verifica se una stringa corrisponde a un'espressione regolare.

**Esempio Sakila:**
```sql
-- Trova titoli con pattern specifici
SELECT 
    film_id,
    title
FROM film
WHERE title REGEXP '^[A-D].*'  -- Inizia con A, B, C, o D
LIMIT 5;
```

```sql
-- Email con domini specifici
SELECT 
    customer_id,
    email
FROM customer
WHERE email REGEXP '.*\.(com|org|net)$'
LIMIT 5;
```

```sql
-- Titoli che contengono numeri
SELECT 
    film_id,
    title
FROM film
WHERE title REGEXP '[0-9]'
LIMIT 5;
```

---

## SOUNDEX

**Sintassi:** `SOUNDEX(str)`

**Descrizione:** Restituisce la rappresentazione Soundex di una stringa per ricerche fonetiche.

**Esempio Sakila:**
```sql
-- Ricerca fonetica nomi simili
SELECT 
    customer_id,
    first_name,
    SOUNDEX(first_name) AS first_name_soundex
FROM customer
WHERE SOUNDEX(first_name) = SOUNDEX('Mary')
LIMIT 5;

-- Trova Mary, Marie, Maria, etc.
```

```sql
-- Confronto cognomi simili
SELECT DISTINCT
    a1.last_name AS name1,
    a2.last_name AS name2
FROM actor a1
JOIN actor a2 ON SOUNDEX(a1.last_name) = SOUNDEX(a2.last_name)
WHERE a1.actor_id < a2.actor_id
LIMIT 5;
```

---

## LPAD

**Sintassi:** `LPAD(str, len, padstr)`

**Descrizione:** Padding a sinistra di una stringa fino alla lunghezza specificata.

**Esempio Sakila:**
```sql
-- Formattazione ID con zeri iniziali
SELECT 
    customer_id,
    LPAD(customer_id, 6, '0') AS customer_code
FROM customer
LIMIT 5;

-- Output: 000001, 000002, etc.
```

```sql
-- Formattazione prezzi
SELECT 
    film_id,
    rental_rate,
    CONCAT('$', LPAD(rental_rate, 6, '0')) AS price_formatted
FROM film
LIMIT 5;
```

---

## RPAD

**Sintassi:** `RPAD(str, len, padstr)`

**Descrizione:** Padding a destra di una stringa fino alla lunghezza specificata.

**Esempio Sakila:**
```sql
-- Allineamento testo in report
SELECT 
    RPAD(title, 30, '.') AS title_padded,
    CONCAT(length, ' min') AS duration
FROM film
LIMIT 5;

-- Output: "ACADEMY DINOSAUR.............86 min"
```

---

## REPEAT

**Sintassi:** `REPEAT(str, count)`

**Descrizione:** Ripete una stringa per il numero specificato di volte.

**Esempio Sakila:**
```sql
-- Creazione barre di progresso visuale
SELECT 
    film_id,
    title,
    length,
    CONCAT(
        REPEAT('█', FLOOR(length/10)),
        REPEAT('░', 20 - FLOOR(length/10))
    ) AS duration_bar
FROM film
LIMIT 5;
```

```sql
-- Mascheramento password (simulato)
SELECT 
    customer_id,
    REPEAT('*', 8) AS password_masked
FROM customer
LIMIT 3;
```

---

## REVERSE

**Sintassi:** `REVERSE(str)`

**Descrizione:** Inverte l'ordine dei caratteri in una stringa.

**Esempio Sakila:**
```sql
-- Palindromi nei titoli
SELECT 
    title,
    REVERSE(title) AS title_reversed,
    CASE 
        WHEN UPPER(title) = UPPER(REVERSE(title)) 
        THEN 'Palindromo'
        ELSE 'Non palindromo'
    END AS is_palindrome
FROM film
WHERE UPPER(title) = UPPER(REVERSE(title));
```

---

## ASCII / CHAR

**Sintassi:** `ASCII(str)` / `CHAR(N1, N2, ...)`

**Descrizione:** ASCII restituisce il valore ASCII del primo carattere. CHAR converte codici ASCII in caratteri.

**Esempio Sakila:**
```sql
-- Analisi caratteri
SELECT 
    title,
    ASCII(LEFT(title, 1)) AS first_char_ascii,
    CHAR(ASCII(LEFT(title, 1)) + 1) AS next_char
FROM film
LIMIT 5;
```

---

## HEX / UNHEX

**Sintassi:** `HEX(str)` / `UNHEX(str)`

**Descrizione:** Converte da/verso rappresentazione esadecimale.

**Esempio Sakila:**
```sql
-- Codifica titoli in hex
SELECT 
    film_id,
    title,
    HEX(title) AS title_hex,
    UNHEX(HEX(title)) AS title_restored
FROM film
LIMIT 3;
```

---

## Esempi Pratici Combinati

### Pulizia e Normalizzazione Dati
```sql
-- Normalizzazione completa nomi clienti
SELECT 
    customer_id,
    TRIM(CONCAT(
        INITCAP(TRIM(first_name)),
        ' ',
        INITCAP(TRIM(last_name))
    )) AS full_name_normalized
FROM customer
LIMIT 5;
```

### Generazione Report Formattati
```sql
-- Report clienti con formato avanzato
SELECT 
    LPAD(customer_id, 4, '0') AS ID,
    RPAD(CONCAT(first_name, ' ', last_name), 25, '.') AS Nome,
    SUBSTRING(email, 1, 20) AS Email,
    CASE 
        WHEN active = 1 THEN '✓ Attivo'
        ELSE '✗ Inattivo'
    END AS Stato
FROM customer
LIMIT 10;
```

### Analisi Testo Avanzata
```sql
-- Statistiche testuali sui film
SELECT 
    COUNT(*) AS total_films,
    AVG(CHAR_LENGTH(title)) AS avg_title_length,
    AVG(CHAR_LENGTH(description)) AS avg_desc_length,
    COUNT(CASE WHEN title REGEXP '[0-9]' THEN 1 END) AS films_with_numbers,
    COUNT(CASE WHEN UPPER(title) LIKE '%THE%' THEN 1 END) AS films_with_the
FROM film;
```

