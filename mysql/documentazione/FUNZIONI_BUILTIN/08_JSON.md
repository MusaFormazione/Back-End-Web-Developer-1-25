# 📦 Funzioni JSON

MySQL 8.0+ offre un supporto nativo completo per documenti JSON, permettendo di archiviare, interrogare e manipolare dati JSON direttamente nel database.

---

## 📋 Indice

### Creazione JSON
- [JSON_OBJECT](#json_object)
- [JSON_ARRAY](#json_array)
- [JSON_QUOTE](#json_quote)

### Estrazione e Ricerca
- [JSON_EXTRACT / ->](#json_extract)
- [JSON_UNQUOTE / ->>](#json_unquote)
- [JSON_KEYS](#json_keys)
- [JSON_SEARCH](#json_search)
- [JSON_CONTAINS](#json_contains)
- [JSON_CONTAINS_PATH](#json_contains_path)
- [JSON_VALUE](#json_value)

### Modifica JSON
- [JSON_SET](#json_set)
- [JSON_INSERT](#json_insert)
- [JSON_REPLACE](#json_replace)
- [JSON_REMOVE](#json_remove)
- [JSON_ARRAY_APPEND](#json_array_append)
- [JSON_ARRAY_INSERT](#json_array_insert)
- [JSON_MERGE_PRESERVE](#json_merge_preserve)
- [JSON_MERGE_PATCH](#json_merge_patch)

### Informazioni JSON
- [JSON_TYPE](#json_type)
- [JSON_VALID](#json_valid)
- [JSON_LENGTH](#json_length)
- [JSON_DEPTH](#json_depth)

### Utility
- [JSON_PRETTY](#json_pretty)
- [JSON_TABLE](#json_table)
- [JSON_STORAGE_SIZE](#json_storage_size)

---

## JSON_OBJECT

🔥 **Funzione molto utilizzata**

**Sintassi:** `JSON_OBJECT(key1, value1, key2, value2, ...)`

**Descrizione:** Crea un oggetto JSON da coppie chiave-valore.

**Esempio Sakila:**
```sql
-- Creiamo un oggetto JSON per ogni film
SELECT 
    film_id,
    JSON_OBJECT(
        'id', film_id,
        'title', title,
        'year', release_year,
        'rating', rating,
        'duration', length
    ) AS film_json
FROM film
LIMIT 3;

-- Output esempio:
-- | film_id | film_json                                                           |
-- |---------|---------------------------------------------------------------------|
-- | 1       | {"id": 1, "title": "ACADEMY DINOSAUR", "year": 2006, "rating": "PG", "duration": 86} |
```

```sql
-- Oggetto JSON nidificato per cliente con indirizzo
SELECT 
    c.customer_id,
    JSON_OBJECT(
        'customer', JSON_OBJECT(
            'id', c.customer_id,
            'name', CONCAT(c.first_name, ' ', c.last_name),
            'email', c.email
        ),
        'address', JSON_OBJECT(
            'street', a.address,
            'city', ci.city,
            'country', co.country
        )
    ) AS customer_json
FROM customer c
JOIN address a ON c.address_id = a.address_id
JOIN city ci ON a.city_id = ci.city_id
JOIN country co ON ci.country_id = co.country_id
LIMIT 2;
```

```sql
-- JSON per API response
SELECT JSON_OBJECT(
    'success', true,
    'data', JSON_OBJECT(
        'films_count', (SELECT COUNT(*) FROM film),
        'customers_count', (SELECT COUNT(*) FROM customer)
    ),
    'timestamp', NOW()
) AS api_response;
```

---

## JSON_ARRAY

🔥 **Funzione molto utilizzata**

**Sintassi:** `JSON_ARRAY(value1, value2, ...)`

**Descrizione:** Crea un array JSON dai valori forniti.

**Esempio Sakila:**
```sql
-- Creiamo un array semplice
SELECT JSON_ARRAY('Action', 'Comedy', 'Drama', 'Horror') AS generi;

-- Output: ["Action", "Comedy", "Drama", "Horror"]
```

```sql
-- Array di oggetti per i film
SELECT 
    JSON_ARRAY(
        JSON_OBJECT('id', 1, 'title', 'Film A'),
        JSON_OBJECT('id', 2, 'title', 'Film B'),
        JSON_OBJECT('id', 3, 'title', 'Film C')
    ) AS films_array;
```

```sql
-- Array con valori misti
SELECT JSON_ARRAY(
    'stringa',
    123,
    45.67,
    true,
    false,
    null,
    JSON_OBJECT('nested', 'object'),
    JSON_ARRAY(1, 2, 3)
) AS mixed_array;
```

---

## JSON_QUOTE

**Sintassi:** `JSON_QUOTE(string)`

**Descrizione:** Racchiude una stringa tra virgolette e fa l'escape dei caratteri speciali per renderla una stringa JSON valida.

**Esempio Sakila:**
```sql
-- Quote di una stringa semplice
SELECT 
    title,
    JSON_QUOTE(title) AS quoted_title
FROM film
LIMIT 3;

-- Output:
-- | title            | quoted_title       |
-- |------------------|--------------------|
-- | ACADEMY DINOSAUR | "ACADEMY DINOSAUR" |
```

```sql
-- Gestione caratteri speciali
SELECT 
    JSON_QUOTE('Linea 1\nLinea 2') AS con_newline,
    JSON_QUOTE('Tab\there') AS con_tab,
    JSON_QUOTE('Quote "here"') AS con_quote;

-- I caratteri speciali vengono escaped: \n, \t, \"
```

---

## JSON_EXTRACT

🔥 **Funzione molto utilizzata**

**Sintassi:** `JSON_EXTRACT(json_doc, path1, path2, ...)` o `json_doc->path`

**Descrizione:** Estrae uno o più valori da un documento JSON usando i path specificati.

**Path Syntax:**
- `$` - Root del documento
- `$.key` - Accesso a una chiave
- `$[n]` - Accesso a un elemento di array (0-indexed)
- `$.key1.key2` - Path nidificato
- `$[*]` - Tutti gli elementi di un array
- `$.key[*].subkey` - Subkey di tutti gli elementi

**Esempio Sakila:**
```sql
-- Creiamo una colonna JSON di esempio
SELECT 
    film_id,
    JSON_OBJECT(
        'title', title,
        'details', JSON_OBJECT(
            'length', length,
            'rating', rating
        ),
        'prices', JSON_ARRAY(rental_rate, replacement_cost)
    ) AS film_data,
    -- Estrazioni
    JSON_EXTRACT(
        JSON_OBJECT(
            'title', title,
            'details', JSON_OBJECT('length', length, 'rating', rating),
            'prices', JSON_ARRAY(rental_rate, replacement_cost)
        ),
        '$.title'
    ) AS extracted_title,
    JSON_EXTRACT(
        JSON_OBJECT(
            'title', title,
            'details', JSON_OBJECT('length', length, 'rating', rating),
            'prices', JSON_ARRAY(rental_rate, replacement_cost)
        ),
        '$.details.rating'
    ) AS extracted_rating
FROM film
LIMIT 3;
```

```sql
-- Sintassi abbreviata con ->
SET @json = '{"name": "Film A", "year": 2024, "tags": ["action", "drama"]}';

SELECT 
    @json->'$.name' AS nome,
    @json->'$.year' AS anno,
    @json->'$.tags[0]' AS primo_tag,
    @json->'$.tags[1]' AS secondo_tag;

-- Output: "Film A", 2024, "action", "drama"
-- Nota: i valori stringa sono ancora quotati!
```

```sql
-- Estrazione multipla
SET @json = '{"a": 1, "b": 2, "c": 3}';

SELECT JSON_EXTRACT(@json, '$.a', '$.b', '$.c') AS tutti_valori;
-- Output: [1, 2, 3]
```

---

## JSON_UNQUOTE

🔥 **Funzione molto utilizzata**

**Sintassi:** `JSON_UNQUOTE(json_val)` o `json_doc->>path`

**Descrizione:** Rimuove le virgolette da una stringa JSON, restituendo il valore grezzo.

**Esempio Sakila:**
```sql
SET @json = '{"name": "ACADEMY DINOSAUR", "rating": "PG"}';

SELECT 
    @json->'$.name' AS con_quote,           -- "ACADEMY DINOSAUR"
    JSON_UNQUOTE(@json->'$.name') AS senza_quote,  -- ACADEMY DINOSAUR
    @json->>'$.name' AS shorthand;          -- ACADEMY DINOSAUR (operatore ->>)
```

```sql
-- Uso pratico: confronto con valori
SET @json = '{"rating": "PG"}';

-- ❌ Questo non funziona (confronta con virgolette)
SELECT @json->'$.rating' = 'PG';  -- 0 (false, confronta "PG" con PG)

-- ✅ Questo funziona
SELECT JSON_UNQUOTE(@json->'$.rating') = 'PG';  -- 1 (true)
SELECT @json->>'$.rating' = 'PG';  -- 1 (true)
```

```sql
-- Estrai e usa in query
SELECT 
    film_id,
    title,
    JSON_OBJECT('title', title, 'length', length) AS json_data
FROM film
WHERE title = JSON_UNQUOTE(
    JSON_EXTRACT(
        JSON_OBJECT('title', title),
        '$.title'
    )
)
LIMIT 5;
```

---

## JSON_KEYS

**Sintassi:** `JSON_KEYS(json_doc, path)`

**Descrizione:** Restituisce le chiavi di un oggetto JSON come array JSON.

**Esempio Sakila:**
```sql
-- Ottieni le chiavi di un oggetto
SET @json = '{"title": "Film A", "year": 2024, "rating": "PG", "length": 120}';

SELECT JSON_KEYS(@json) AS chiavi;
-- Output: ["length", "rating", "title", "year"]
```

```sql
-- Chiavi di un oggetto nidificato
SET @json = '{"film": {"title": "Test", "details": {"duration": 120}}}';

SELECT 
    JSON_KEYS(@json) AS chiavi_root,
    JSON_KEYS(@json, '$.film') AS chiavi_film,
    JSON_KEYS(@json, '$.film.details') AS chiavi_details;

-- Output:
-- chiavi_root: ["film"]
-- chiavi_film: ["details", "title"]
-- chiavi_details: ["duration"]
```

---

## JSON_SEARCH

**Sintassi:** `JSON_SEARCH(json_doc, one_or_all, search_str, escape_char, path)`

**Descrizione:** Cerca una stringa in un documento JSON e restituisce il path dove è stata trovata.

**Esempio Sakila:**
```sql
SET @json = '{
    "films": [
        {"title": "ACADEMY DINOSAUR", "rating": "PG"},
        {"title": "ACE GOLDFINGER", "rating": "G"},
        {"title": "AFFAIR PREJUDICE", "rating": "G"}
    ]
}';

-- Trova la prima occorrenza di "G"
SELECT JSON_SEARCH(@json, 'one', 'G') AS primo_match;
-- Output: "$.films[1].rating"

-- Trova tutte le occorrenze di "G"
SELECT JSON_SEARCH(@json, 'all', 'G') AS tutti_match;
-- Output: ["$.films[1].rating", "$.films[2].rating"]
```

```sql
-- Ricerca con wildcard (% e _)
SET @json = '{"names": ["John", "Jane", "Jack", "Jill"]}';

SELECT JSON_SEARCH(@json, 'all', 'J%') AS iniziano_con_j;
-- Output: ["$.names[0]", "$.names[1]", "$.names[2]", "$.names[3]"]

SELECT JSON_SEARCH(@json, 'all', 'J___') AS quattro_lettere;
-- Output: ["$.names[0]", "$.names[1]", "$.names[2]", "$.names[3]"]
```

---

## JSON_CONTAINS

🔥 **Funzione molto utilizzata**

**Sintassi:** `JSON_CONTAINS(json_doc, val, path)`

**Descrizione:** Verifica se un documento JSON contiene un valore specifico. Restituisce 1 (true) o 0 (false).

**Esempio Sakila:**
```sql
SET @json = '{"ratings": ["G", "PG", "PG-13", "R"]}';

-- Verifica se l'array contiene "PG"
SELECT JSON_CONTAINS(@json, '"PG"', '$.ratings') AS contiene_pg;
-- Output: 1

-- Verifica un valore non presente
SELECT JSON_CONTAINS(@json, '"NC-17"', '$.ratings') AS contiene_nc17;
-- Output: 0
```

```sql
-- Verifica se un oggetto contiene una sottostruttura
SET @json = '{"name": "Film A", "year": 2024, "genres": ["action", "drama"]}';

SELECT 
    JSON_CONTAINS(@json, '{"year": 2024}') AS contiene_year,
    JSON_CONTAINS(@json, '{"genres": ["action"]}') AS contiene_action,
    JSON_CONTAINS(@json, '{"genres": ["comedy"]}') AS contiene_comedy;

-- Output: 1, 1, 0
```

```sql
-- Uso in WHERE clause
-- Supponendo una tabella con colonna JSON
/*
SELECT * FROM products
WHERE JSON_CONTAINS(attributes, '"wireless"', '$.features');
*/
```

---

## JSON_CONTAINS_PATH

**Sintassi:** `JSON_CONTAINS_PATH(json_doc, one_or_all, path1, path2, ...)`

**Descrizione:** Verifica se un documento JSON contiene dati nei path specificati.

**Esempio Sakila:**
```sql
SET @json = '{"name": "Film", "details": {"year": 2024, "director": null}}';

-- Verifica se esiste almeno un path
SELECT JSON_CONTAINS_PATH(@json, 'one', '$.name', '$.missing') AS almeno_uno;
-- Output: 1 ($.name esiste)

-- Verifica se esistono tutti i path
SELECT JSON_CONTAINS_PATH(@json, 'all', '$.name', '$.missing') AS tutti;
-- Output: 0 ($.missing non esiste)

-- Nota: path esiste anche se il valore è null
SELECT JSON_CONTAINS_PATH(@json, 'one', '$.details.director') AS director_exists;
-- Output: 1 (esiste, anche se null)
```

---

## JSON_VALUE

🆕 **MySQL 8.0.21+**

**Sintassi:** `JSON_VALUE(json_doc, path [RETURNING type] [on_empty] [on_error])`

**Descrizione:** Estrae un valore scalare da un documento JSON. Più potente di JSON_EXTRACT perché può specificare il tipo di ritorno.

**Esempio Sakila:**
```sql
SET @json = '{"title": "Film A", "year": 2024, "rating": 8.5}';

SELECT 
    JSON_VALUE(@json, '$.title') AS title,
    JSON_VALUE(@json, '$.year' RETURNING SIGNED) AS year_int,
    JSON_VALUE(@json, '$.rating' RETURNING DECIMAL(3,1)) AS rating_decimal;
```

```sql
-- Gestione errori e valori mancanti
SET @json = '{"name": "Test"}';

SELECT 
    JSON_VALUE(@json, '$.name') AS nome,
    JSON_VALUE(@json, '$.missing' DEFAULT 'N/A' ON EMPTY) AS con_default,
    JSON_VALUE(@json, '$.missing' NULL ON EMPTY) AS con_null;
```

---

## JSON_SET

🔥 **Funzione molto utilizzata**

**Sintassi:** `JSON_SET(json_doc, path, val, path, val, ...)`

**Descrizione:** Inserisce o aggiorna valori in un documento JSON. Se il path esiste, aggiorna; se non esiste, inserisce.

**Esempio Sakila:**
```sql
SET @json = '{"title": "Film A", "year": 2020}';

-- Aggiorna un valore esistente e aggiungi uno nuovo
SELECT JSON_SET(@json, 
    '$.year', 2024,           -- Aggiorna (esiste)
    '$.rating', 'PG'          -- Inserisce (non esiste)
) AS updated_json;

-- Output: {"title": "Film A", "year": 2024, "rating": "PG"}
```

```sql
-- Aggiorna valori nidificati
SET @json = '{"film": {"title": "Test", "details": {}}}';

SELECT JSON_SET(@json,
    '$.film.title', 'New Title',
    '$.film.details.length', 120,
    '$.film.details.rating', 'PG'
) AS updated;
```

---

## JSON_INSERT

**Sintassi:** `JSON_INSERT(json_doc, path, val, path, val, ...)`

**Descrizione:** Inserisce valori SOLO se il path non esiste già. Non sovrascrive valori esistenti.

**Esempio Sakila:**
```sql
SET @json = '{"title": "Film A", "year": 2020}';

-- INSERT non sovrascrive valori esistenti
SELECT JSON_INSERT(@json,
    '$.year', 2024,       -- NON aggiorna (esiste già)
    '$.rating', 'PG'      -- Inserisce (non esiste)
) AS result;

-- Output: {"title": "Film A", "year": 2020, "rating": "PG"}
-- Nota: year rimane 2020!
```

---

## JSON_REPLACE

**Sintassi:** `JSON_REPLACE(json_doc, path, val, path, val, ...)`

**Descrizione:** Aggiorna valori SOLO se il path esiste già. Non inserisce nuovi valori.

**Esempio Sakila:**
```sql
SET @json = '{"title": "Film A", "year": 2020}';

-- REPLACE non inserisce nuovi valori
SELECT JSON_REPLACE(@json,
    '$.year', 2024,       -- Aggiorna (esiste)
    '$.rating', 'PG'      -- NON inserisce (non esiste)
) AS result;

-- Output: {"title": "Film A", "year": 2024}
-- Nota: rating non viene aggiunto!
```

```sql
-- Confronto SET vs INSERT vs REPLACE
SET @json = '{"a": 1}';

SELECT 
    JSON_SET(@json, '$.a', 10, '$.b', 20) AS json_set,         -- {"a": 10, "b": 20}
    JSON_INSERT(@json, '$.a', 10, '$.b', 20) AS json_insert,   -- {"a": 1, "b": 20}
    JSON_REPLACE(@json, '$.a', 10, '$.b', 20) AS json_replace; -- {"a": 10}
```

---

## JSON_REMOVE

**Sintassi:** `JSON_REMOVE(json_doc, path1, path2, ...)`

**Descrizione:** Rimuove elementi da un documento JSON.

**Esempio Sakila:**
```sql
SET @json = '{"title": "Film A", "year": 2024, "temp_data": "remove me"}';

SELECT JSON_REMOVE(@json, '$.temp_data') AS cleaned;
-- Output: {"title": "Film A", "year": 2024}
```

```sql
-- Rimuovi multipli elementi
SET @json = '{"a": 1, "b": 2, "c": 3, "d": 4}';

SELECT JSON_REMOVE(@json, '$.a', '$.c') AS result;
-- Output: {"b": 2, "d": 4}
```

```sql
-- Rimuovi elementi da array
SET @json = '{"tags": ["one", "two", "three", "four"]}';

SELECT JSON_REMOVE(@json, '$.tags[1]') AS result;
-- Output: {"tags": ["one", "three", "four"]}
-- Nota: gli indici si ricalcolano dopo la rimozione
```

---

## JSON_ARRAY_APPEND

**Sintassi:** `JSON_ARRAY_APPEND(json_doc, path, val, path, val, ...)`

**Descrizione:** Aggiunge valori alla fine di un array JSON.

**Esempio Sakila:**
```sql
SET @json = '{"genres": ["action", "drama"]}';

SELECT JSON_ARRAY_APPEND(@json, '$.genres', 'comedy') AS updated;
-- Output: {"genres": ["action", "drama", "comedy"]}
```

```sql
-- Append multipli
SET @json = '{"a": [1], "b": [10]}';

SELECT JSON_ARRAY_APPEND(@json,
    '$.a', 2,
    '$.a', 3,
    '$.b', 20
) AS result;
-- Output: {"a": [1, 2, 3], "b": [10, 20]}
```

---

## JSON_ARRAY_INSERT

**Sintassi:** `JSON_ARRAY_INSERT(json_doc, path, val, path, val, ...)`

**Descrizione:** Inserisce valori in una posizione specifica di un array JSON.

**Esempio Sakila:**
```sql
SET @json = '{"numbers": [1, 2, 4, 5]}';

-- Inserisci 3 nella posizione 2 (0-indexed)
SELECT JSON_ARRAY_INSERT(@json, '$.numbers[2]', 3) AS result;
-- Output: {"numbers": [1, 2, 3, 4, 5]}
```

```sql
-- Inserisci all'inizio
SET @json = '{"list": ["b", "c"]}';

SELECT JSON_ARRAY_INSERT(@json, '$.list[0]', 'a') AS result;
-- Output: {"list": ["a", "b", "c"]}
```

---

## JSON_MERGE_PRESERVE

**Sintassi:** `JSON_MERGE_PRESERVE(json_doc1, json_doc2, ...)`

**Descrizione:** Combina più documenti JSON. Gli array vengono concatenati, gli oggetti vengono fusi preservando tutti i valori.

**Esempio Sakila:**
```sql
-- Merge di array
SELECT JSON_MERGE_PRESERVE(
    '["a", "b"]',
    '["c", "d"]'
) AS merged_array;
-- Output: ["a", "b", "c", "d"]
```

```sql
-- Merge di oggetti
SELECT JSON_MERGE_PRESERVE(
    '{"a": 1, "b": 2}',
    '{"b": 3, "c": 4}'
) AS merged_object;
-- Output: {"a": 1, "b": [2, 3], "c": 4}
-- Nota: "b" diventa array perché presente in entrambi!
```

```sql
-- Merge multiplo
SELECT JSON_MERGE_PRESERVE(
    '{"name": "Film"}',
    '{"year": 2024}',
    '{"rating": "PG"}'
) AS merged;
-- Output: {"name": "Film", "year": 2024, "rating": "PG"}
```

---

## JSON_MERGE_PATCH

**Sintassi:** `JSON_MERGE_PATCH(json_doc1, json_doc2, ...)`

**Descrizione:** Combina documenti JSON con semantica "patch" (RFC 7396). I valori duplicati vengono sovrascritti, non preservati.

**Esempio Sakila:**
```sql
-- Patch di oggetti (sovrascrive)
SELECT JSON_MERGE_PATCH(
    '{"a": 1, "b": 2}',
    '{"b": 3, "c": 4}'
) AS patched;
-- Output: {"a": 1, "b": 3, "c": 4}
-- Nota: "b" è sovrascritto a 3, non diventa array!
```

```sql
-- Confronto PRESERVE vs PATCH
SELECT 
    JSON_MERGE_PRESERVE('{"x": 1}', '{"x": 2}') AS preserve,  -- {"x": [1, 2]}
    JSON_MERGE_PATCH('{"x": 1}', '{"x": 2}') AS patch;        -- {"x": 2}
```

```sql
-- Rimuovi chiavi con null in PATCH
SELECT JSON_MERGE_PATCH(
    '{"a": 1, "b": 2, "c": 3}',
    '{"b": null}'
) AS result;
-- Output: {"a": 1, "c": 3}
-- "b" viene rimosso!
```

---

## JSON_TYPE

**Sintassi:** `JSON_TYPE(json_val)`

**Descrizione:** Restituisce il tipo di un valore JSON come stringa.

**Esempio Sakila:**
```sql
SELECT 
    JSON_TYPE('{"a": 1}') AS tipo_object,       -- OBJECT
    JSON_TYPE('[1, 2, 3]') AS tipo_array,       -- ARRAY
    JSON_TYPE('"hello"') AS tipo_string,        -- STRING
    JSON_TYPE('123') AS tipo_integer,           -- INTEGER
    JSON_TYPE('12.5') AS tipo_double,           -- DOUBLE
    JSON_TYPE('true') AS tipo_boolean,          -- BOOLEAN
    JSON_TYPE('null') AS tipo_null;             -- NULL
```

```sql
-- Validazione del tipo
SET @json = '{"data": [1, 2, 3]}';

SELECT 
    CASE JSON_TYPE(@json->'$.data')
        WHEN 'ARRAY' THEN 'È un array'
        WHEN 'OBJECT' THEN 'È un oggetto'
        ELSE 'Altro tipo'
    END AS tipo_data;
```

---

## JSON_VALID

**Sintassi:** `JSON_VALID(val)`

**Descrizione:** Verifica se una stringa è un JSON valido. Restituisce 1 (valido) o 0 (non valido).

**Esempio Sakila:**
```sql
SELECT 
    JSON_VALID('{"name": "test"}') AS valido,           -- 1
    JSON_VALID('not json') AS non_valido,               -- 0
    JSON_VALID('{"incomplete":') AS incompleto,         -- 0
    JSON_VALID(NULL) AS null_value;                     -- NULL
```

```sql
-- Validazione prima dell'inserimento
SET @input = '{"title": "Film A"}';

SELECT 
    IF(JSON_VALID(@input), 
        'JSON valido, procedi con insert',
        'JSON non valido, errore!'
    ) AS validation_result;
```

---

## JSON_LENGTH

**Sintassi:** `JSON_LENGTH(json_doc, path)`

**Descrizione:** Restituisce la lunghezza di un documento JSON. Per array: numero di elementi. Per oggetti: numero di chiavi.

**Esempio Sakila:**
```sql
SET @json = '{
    "title": "Film",
    "genres": ["action", "drama", "comedy"],
    "cast": [
        {"name": "Actor 1"},
        {"name": "Actor 2"}
    ]
}';

SELECT 
    JSON_LENGTH(@json) AS chiavi_root,           -- 3 (title, genres, cast)
    JSON_LENGTH(@json, '$.genres') AS num_genres, -- 3
    JSON_LENGTH(@json, '$.cast') AS num_cast;     -- 2
```

```sql
-- Filtra in base alla lunghezza
/*
SELECT * FROM products
WHERE JSON_LENGTH(attributes, '$.tags') > 5;
*/
```

---

## JSON_DEPTH

**Sintassi:** `JSON_DEPTH(json_doc)`

**Descrizione:** Restituisce la profondità massima di un documento JSON. Un valore scalare ha profondità 1.

**Esempio Sakila:**
```sql
SELECT 
    JSON_DEPTH('{}') AS empty_object,           -- 1
    JSON_DEPTH('[]') AS empty_array,            -- 1
    JSON_DEPTH('"hello"') AS scalar,            -- 1
    JSON_DEPTH('[1, 2, 3]') AS flat_array,      -- 2
    JSON_DEPTH('{"a": {"b": {"c": 1}}}') AS nested; -- 4
```

---

## JSON_PRETTY

**Sintassi:** `JSON_PRETTY(json_val)`

**Descrizione:** Formatta un documento JSON con indentazione per renderlo leggibile.

**Esempio Sakila:**
```sql
SET @json = '{"name":"Film","year":2024,"genres":["action","drama"]}';

SELECT JSON_PRETTY(@json) AS formatted;

-- Output:
-- {
--   "name": "Film",
--   "year": 2024,
--   "genres": [
--     "action",
--     "drama"
--   ]
-- }
```

---

## JSON_TABLE

🆕 **MySQL 8.0+**

**Sintassi:** `JSON_TABLE(json_doc, path COLUMNS (column_list))`

**Descrizione:** Converte dati JSON in una tabella relazionale. Potentissimo per query su dati JSON.

**Esempio Sakila:**
```sql
-- Converti array JSON in righe
SET @json = '[
    {"id": 1, "name": "Action", "count": 64},
    {"id": 2, "name": "Comedy", "count": 58},
    {"id": 3, "name": "Drama", "count": 62}
]';

SELECT * FROM JSON_TABLE(
    @json,
    '$[*]' COLUMNS (
        id INT PATH '$.id',
        name VARCHAR(50) PATH '$.name',
        count INT PATH '$.count'
    )
) AS categories;

-- Output:
-- | id | name   | count |
-- |----|--------|-------|
-- | 1  | Action | 64    |
-- | 2  | Comedy | 58    |
-- | 3  | Drama  | 62    |
```

```sql
-- JSON nidificato con NESTED PATH
SET @json = '{
    "store": "Sakila",
    "films": [
        {"title": "Film A", "actors": ["Actor 1", "Actor 2"]},
        {"title": "Film B", "actors": ["Actor 3"]}
    ]
}';

SELECT * FROM JSON_TABLE(
    @json,
    '$.films[*]' COLUMNS (
        title VARCHAR(100) PATH '$.title',
        NESTED PATH '$.actors[*]' COLUMNS (
            actor VARCHAR(50) PATH '$'
        )
    )
) AS film_actors;

-- Output:
-- | title  | actor   |
-- |--------|---------|
-- | Film A | Actor 1 |
-- | Film A | Actor 2 |
-- | Film B | Actor 3 |
```

```sql
-- Con gestione errori e valori mancanti
SET @json = '[{"a": 1}, {"a": 2, "b": "text"}, {"a": 3}]';

SELECT * FROM JSON_TABLE(
    @json,
    '$[*]' COLUMNS (
        a INT PATH '$.a',
        b VARCHAR(50) PATH '$.b' DEFAULT '"N/A"' ON EMPTY
    )
) AS data;
```

---

## JSON_STORAGE_SIZE

**Sintassi:** `JSON_STORAGE_SIZE(json_doc)`

**Descrizione:** Restituisce il numero di byte utilizzati per archiviare il documento JSON.

**Esempio Sakila:**
```sql
SELECT 
    JSON_STORAGE_SIZE('{}') AS empty_object,
    JSON_STORAGE_SIZE('{"a": 1}') AS simple_object,
    JSON_STORAGE_SIZE('{"title": "ACADEMY DINOSAUR", "year": 2006}') AS film_object;
```

---

## 💡 Tips & Best Practices

### 1. Indici su JSON
```sql
-- Crea indici funzionali per query frequenti su JSON
/*
ALTER TABLE films
ADD INDEX idx_year ((CAST(data->>'$.year' AS UNSIGNED)));

SELECT * FROM films
WHERE CAST(data->>'$.year' AS UNSIGNED) = 2024;
*/
```

### 2. Preferisci colonne normali per dati strutturati
```sql
-- JSON è ottimo per dati flessibili/schemaless
-- Ma per dati strutturati, le colonne normali sono più efficienti
```

### 3. Usa ->> per stringhe
```sql
-- ✅ ->> rimuove le virgolette automaticamente
SELECT doc->>'$.name' FROM table;

-- ❌ -> mantiene le virgolette
SELECT doc->'$.name' FROM table;  -- Risultato: "John" con virgolette
```

### 4. JSON_TABLE per query complesse
```sql
-- Per query complesse su JSON, JSON_TABLE è più leggibile
-- e spesso più performante di multiple JSON_EXTRACT
```

### 5. Validazione
```sql
-- Valida sempre il JSON prima dell'inserimento
INSERT INTO table (json_col) 
SELECT @json WHERE JSON_VALID(@json);
```

---

*Documentazione Funzioni JSON MySQL 8.0+ - Sakila Database*

