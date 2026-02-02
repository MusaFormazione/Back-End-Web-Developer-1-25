# Stored Programs, Stored Function e Trigger - MySQL

Esempi di stored programs che si applicano al database Sakila, con commenti esplicativi per comprendere il funzionamento.

---

## 1. Stored Procedure: Recupera i film disponibili in una categoria

Questa stored procedure accetta il nome di una categoria come input e restituisce l'elenco dei film disponibili in quella categoria.

```sql
DELIMITER $$

CREATE PROCEDURE GetFilmsByCategory(IN categoryName VARCHAR(50))
BEGIN
    -- Recupera i film disponibili in una categoria specifica
    SELECT f.title AS FilmTitle, f.description AS FilmDescription
    FROM film f
    JOIN film_category fc ON f.film_id = fc.film_id
    JOIN category c ON fc.category_id = c.category_id
    WHERE c.name = categoryName;
END$$

DELIMITER ;
```

### Come usarla:

```sql
CALL GetFilmsByCategory('Action');
```

Questo comando restituisce tutti i film della categoria "Action".

---

## 2. Stored Function: Calcola il numero di film in una categoria

Questa stored function accetta il nome di una categoria e restituisce il numero di film presenti in quella categoria.

```sql
DELIMITER $$

CREATE FUNCTION CountFilmsInCategory(categoryName VARCHAR(50))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE filmCount INT;
    -- Conta il numero di film in una categoria specifica
    SELECT COUNT(f.film_id) INTO filmCount
    FROM film f
    JOIN film_category fc ON f.film_id = fc.film_id
    JOIN category c ON fc.category_id = c.category_id
    WHERE c.name = categoryName;
    RETURN filmCount;
END$$

DELIMITER ;
```

### Come usarla:

```sql
SELECT CountFilmsInCategory('Comedy');
```

Questo comando restituisce il numero di film nella categoria "Comedy".

---

## 3. Trigger: Log delle modifiche al numero di copie di un film

I trigger sono utili per automatizzare azioni specifiche quando si verificano eventi (INSERT, UPDATE, DELETE) su una tabella.

### Creazione della tabella di log

Prima di creare il trigger, è necessario creare una tabella per registrare le modifiche.

```sql
CREATE TABLE inventory_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    inventory_id INT NOT NULL,
    old_quantity INT,
    new_quantity INT,
    change_date DATETIME NOT NULL
);
```

### Creazione del trigger

Il trigger viene attivato ogni volta che viene eseguito un aggiornamento sulla tabella inventory.

```sql
DELIMITER $$

CREATE TRIGGER after_inventory_update
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    -- Inserisce un record nella tabella di log per tenere traccia delle modifiche
    INSERT INTO inventory_log (inventory_id, old_quantity, new_quantity, change_date)
    VALUES (NEW.inventory_id, OLD.quantity, NEW.quantity, NOW());
END$$

DELIMITER ;
```

### Spiegazione:

- **AFTER UPDATE**: Il trigger viene eseguito dopo che un record nella tabella inventory è stato aggiornato.
- **FOR EACH ROW**: Il trigger agisce su ogni riga interessata dall'operazione di aggiornamento.
- **OLD.quantity**: Rappresenta il valore precedente della colonna quantity.
- **NEW.quantity**: Rappresenta il nuovo valore della colonna quantity.
- **NOW()**: Registra la data e l'ora della modifica.

### Esempio di utilizzo:

Supponiamo di aggiornare il numero di copie di un film nella tabella inventory:

```sql
UPDATE inventory
SET quantity = 10
WHERE inventory_id = 1;
```

Dopo l'aggiornamento, il trigger registra automaticamente l'evento nella tabella inventory_log. Puoi verificare il log con:

```sql
SELECT * FROM inventory_log;
```

---

## 4. Trigger: Inserimento automatico della data di registrazione

Un altro esempio semplice è un trigger che aggiunge automaticamente la data di registrazione quando viene inserito un nuovo cliente nella tabella customer.

### Creazione del trigger

```sql
DELIMITER $$

CREATE TRIGGER before_customer_insert
BEFORE INSERT ON customer
FOR EACH ROW
BEGIN
    -- Imposta automaticamente la data di registrazione se non fornita
    IF NEW.create_date IS NULL THEN
        SET NEW.create_date = NOW();
    END IF;
END$$

DELIMITER ;
```

### Spiegazione:

- **BEFORE INSERT**: Il trigger viene eseguito prima che un nuovo record venga inserito nella tabella customer.
- **NEW.create_date**: Rappresenta il valore della colonna create_date per il nuovo record.
- **NOW()**: Imposta la data e l'ora corrente.

### Esempio di utilizzo:

Inserisci un nuovo cliente senza specificare la data di registrazione:

```sql
INSERT INTO customer (store_id, first_name, last_name, email, address_id, active)
VALUES (1, 'John', 'Doe', 'john.doe@example.com', 5, 1);
```

Il trigger aggiunge automaticamente la data di registrazione al record.

---

## Riepilogo

Questi esempi mostrano come gli **stored programs** possano essere utilizzati per:

- **Stored Procedure**: Creare operazioni riutilizzabili per interrogare e manipolare i dati
- **Stored Function**: Calcolare valori specifici e restituire risultati
- **Trigger**: Automatizzare operazioni e garantire l'integrità dei dati nel database Sakila

Perfetti per una demo durante un corso!
