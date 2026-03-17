# LOAD DATA - Esempio Pratico

## Introduzione
Il comando `LOAD DATA` in MySQL permette di caricare dati da un file (come CSV) direttamente in una tabella del database in modo efficiente.

---

## 1. Creazione della Tabella

Prima di caricare i dati, è necessario avere una tabella di destinazione:

```sql
CREATE TABLE utenti (
    id INT NOT NULL,
    nome VARCHAR(50),
    email VARCHAR(100),
    PRIMARY KEY (id)
);
```

---

## 2. Che cos'è un file CSV?

**CSV** sta per **Comma-Separated Values** (Valori Separati da Virgola).

### Caratteristiche principali:
- 📄 **Formato di testo semplice** per memorizzare dati tabulari
- 🔗 **Ogni riga** rappresenta un record (come una riga di una tabella)
- ➡️ **Ogni campo** è separato da una virgola (o altro delimitatore)
- 📝 **Facilmente leggibile** sia da umani che da programmi

### Esempio visivo:
**Tabella normale:**
```
| ID | Nome  | Email             |
|----|-------|-------------------|
| 1  | Marco | marco@example.com |
| 2  | Luca  | luca@example.com  |
```

**Stesso contenuto in formato CSV:**
```
1,Marco,marco@example.com
2,Luca,luca@example.com
```

### Vantaggi del formato CSV:
- ✅ **Universale** - supportato da tutti i database e fogli di calcolo
- ✅ **Leggero** - occupa poco spazio
- ✅ **Semplice** - facile da creare e modificare
- ✅ **Portabile** - funziona su qualsiasi sistema operativo

---

## 3. Preparazione del File CSV

Creare un file CSV chiamato `utenti.csv` con il seguente contenuto:

```text
1,Marco,marco@example.com
2,Luca,luca@example.com
3,Sara,sara@example.com
```

**Struttura del file:**
- Ogni riga rappresenta un record
- I campi sono separati da virgole
- Nessuna intestazione (header)

---

## 4. Comando LOAD DATA

Sintassi base per caricare i dati:

```sql


LOAD DATA INFILE '/percorso/del/file/utenti.csv'
INTO TABLE utenti
FIELDS TERMINATED BY ',' 
LINES TERMINATED BY '\n'
(id, nome, email);
```

---

## 5. Spiegazione dei Parametri

| Parametro | Descrizione |
|-----------|-------------|
| `INFILE` | Specifica il percorso completo del file CSV |
| `INTO TABLE` | Indica la tabella di destinazione |
| `FIELDS TERMINATED BY ','` | Definisce il separatore dei campi (virgola) |
| `LINES TERMINATED BY '\n'` | Definisce il terminatore di riga |
| `(id, nome, email)` | Ordine delle colonne corrispondente al file |

---

## 6. Note Importanti

### Permessi
- ⚠️ Il server MySQL deve avere permessi di lettura sul file
- Il file deve essere accessibile dal server MySQL

### Percorsi File
- **Linux/Mac:** `/home/user/data/utenti.csv`
- **Windows:** `C:\\percorso\\del\\file\\utenti.csv`

### Sicurezza
- Usare `LOAD DATA LOCAL INFILE` se il file è sul client
- Verificare le impostazioni di sicurezza del server

---

## 7. Esempi Avanzati

### Con Skip delle Righe di Intestazione
```sql
LOAD DATA INFILE '/percorso/utenti.csv'
INTO TABLE utenti
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(id, nome, email);
```

### Con Gestione di Campi Opzionali
```sql
LOAD DATA INFILE '/percorso/utenti.csv'
INTO TABLE utenti
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(id, nome, email);
```

---

## 8. Verifica del Caricamento

Dopo aver eseguito il comando, verificare i dati:

```sql
SELECT * FROM utenti;
```

**Output atteso:**
```
+----+-------+-------------------+
| id | nome  | email             |
+----+-------+-------------------+
|  1 | Marco | marco@example.com |
|  2 | Luca  | luca@example.com  |
|  3 | Sara  | sara@example.com  |
+----+-------+-------------------+
```

---

## Conclusioni

Il comando `LOAD DATA` è uno strumento potente per:
- ✅ Importazione rapida di grandi quantità di dati
- ✅ Migrazione di dati da altri sistemi
- ✅ Caricamento batch di file CSV/TXT

**Vantaggi:** Molto più veloce degli INSERT multipli per grandi dataset.
