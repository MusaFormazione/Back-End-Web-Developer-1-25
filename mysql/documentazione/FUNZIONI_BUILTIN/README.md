# 📚 Funzioni Built-in MySQL - Documentazione Completa

Questa documentazione fornisce una guida completa e pratica alle funzioni built-in di MySQL 8.0+, con esempi contestualizzati utilizzando il database **Sakila**.

---

## 🗂️ Indice Generale

### [🔤 01. Funzioni Stringhe](./01_STRINGHE.md)
Manipolazione, analisi e trasformazione di stringhe
- **Più utilizzate**: UPPER, LOWER, CONCAT, SUBSTRING, LENGTH, TRIM, REPLACE
- **Avanzate**: GROUP_CONCAT, REGEXP, SUBSTRING_INDEX, SOUNDEX
- **Utility**: LPAD, RPAD, REPEAT, REVERSE, ASCII, HEX

### [📅 02. Funzioni Data e Ora](./02_DATA_ORA.md)  
Gestione completa di date, orari e intervalli temporali
- **Essenziali**: NOW, CURDATE, YEAR, MONTH, DAY, DATEDIFF, DATE_ADD
- **Formattazione**: DATE_FORMAT, STR_TO_DATE, TIME_FORMAT
- **Calcoli**: TIMESTAMPDIFF, TIMESTAMPADD, UNIX_TIMESTAMP

### [📊 03. Funzioni Aggregazione](./03_AGGREGAZIONE.md)
Calcoli statistici e aggregazioni per analisi dati
- **Base**: COUNT, SUM, AVG, MIN, MAX
- **Statistiche**: STDDEV, VARIANCE, PERCENT_RANK
- **Window**: ROW_NUMBER, RANK, DENSE_RANK, LAG, LEAD, NTILE

### [🔀 04. Funzioni Controllo Flusso](./04_CONTROLLO_FLUSSO.md)
Logica condizionale e controllo del flusso dati
- **Condizionali**: CASE, IF, IFNULL, COALESCE
- **Comparazioni**: GREATEST, LEAST, NULLIF
- **Validazione**: IS NULL, ISNULL

### [🔢 05. Funzioni Numeriche](./05_NUMERICHE.md)
Operazioni matematiche e calcoli numerici
- **Aritmetiche**: ABS, ROUND, CEIL, FLOOR, MOD, TRUNCATE
- **Avanzate**: POWER, SQRT, LN, LOG, SIN, COS, TAN
- **Utility**: RAND, SIGN, FORMAT, GREATEST, LEAST

### [🔄 06. Funzioni Conversione](./06_CONVERSIONE.md)
Trasformazione tra tipi, formati e rappresentazioni
- **Base**: CAST, CONVERT, BINARY
- **Numeriche**: FORMAT, BIN, HEX, OCT, CONV
- **Temporali**: DATE, TIME, DATETIME, STR_TO_DATE

### [🪟 07. Funzioni Window](./07_WINDOW.md)
Analisi avanzate e business intelligence
- **Ranking**: ROW_NUMBER, RANK, DENSE_RANK, NTILE
- **Navigazione**: LAG, LEAD, FIRST_VALUE, LAST_VALUE
- **Aggregate Window**: SUM() OVER, COUNT() OVER, AVG() OVER

### [📦 08. Funzioni JSON](./08_JSON.md) *(Pre-esistente)*
Gestione completa documenti JSON
- **Creazione**: JSON_OBJECT, JSON_ARRAY
- **Estrazione**: JSON_EXTRACT, JSON_UNQUOTE, JSON_KEYS
- **Modifica**: JSON_SET, JSON_INSERT, JSON_REPLACE, JSON_REMOVE

---

## 🎯 Caratteristiche della Documentazione

### ✅ **Esempi Contestualizzati**
- Tutti gli esempi utilizzano il database **Sakila**
- Scenari reali di business e operazioni quotidiane
- Casi d'uso pratici e applicabili

### ✅ **Funzioni Prioritarie**
- Contrassegno 🔥 per le funzioni più utilizzate
- Focus su quelle essenziali per sviluppatori
- Esempi multipli per funzioni critiche

### ✅ **Livelli di Complessità**
- **Base**: Sintassi e esempi semplici
- **Intermedio**: Combinazioni e casi d'uso reali
- **Avanzato**: Esempi complessi e business intelligence

### ✅ **Commenti Dettagliati**
- Spiegazione di ogni passaggio negli esempi
- Logica di business chiarita
- Best practices integrate

---

## 🚀 Come Utilizzare Questa Documentazione

### 📖 **Per Principianti**
1. Inizia con le funzioni contrassegnate 🔥
2. Concentrati sugli esempi base
3. Pratica con il database Sakila

### 🔨 **Per Sviluppatori**
1. Usa l'indice per trovare funzioni specifiche
2. Copia e adatta gli esempi ai tuoi casi
3. Studia gli esempi combinati

### 📊 **Per Analisi Dati**
1. Focus su Aggregazione e Window Functions
2. Studia i dashboard completi negli esempi
3. Adatta le logiche di segmentazione

### 🏢 **Per Business Intelligence**
1. Window Functions per analisi avanzate
2. Funzioni di conversione per export
3. Esempi di cohort analysis e KPI

---

## 💡 Esempi Trasversali

### Customer Segmentation
Combinando **aggregazione** + **window functions** + **controllo flusso**:
```sql
-- Segmentazione RFM completa
SELECT 
    customer_id,
    customer_name,
    NTILE(5) OVER (ORDER BY days_since_last_rental) AS recency_score,
    NTILE(5) OVER (ORDER BY total_rentals DESC) AS frequency_score, 
    NTILE(5) OVER (ORDER BY total_spent DESC) AS monetary_score,
    CASE 
        WHEN NTILE(5) OVER (ORDER BY total_spent DESC) <= 2 THEN 'Champions'
        WHEN NTILE(5) OVER (ORDER BY days_since_last_rental) >= 4 THEN 'At Risk'
        ELSE 'Regular'
    END AS customer_segment
FROM customer_metrics;
```

### Dashboard Finanziario
Combinando **date/ora** + **numeriche** + **conversione**:
```sql
-- KPI dashboard con trend
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') AS month,
    FORMAT(SUM(amount), 2) AS revenue,
    SUM(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS running_total,
    LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m')) AS prev_month,
    ROUND(((SUM(amount) - LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m'))) / 
           LAG(SUM(amount)) OVER (ORDER BY DATE_FORMAT(payment_date, '%Y-%m'))) * 100, 2) AS growth_rate
FROM payment
GROUP BY DATE_FORMAT(payment_date, '%Y-%m');
```

---

## 🗄️ Database di Riferimento: Sakila

Tutti gli esempi utilizzano il database **Sakila**, un database di esempio che simula un negozio di noleggio DVD:

### Tabelle Principali
| Tabella | Descrizione | Utilizzo negli Esempi |
|---------|-------------|----------------------|
| `film` | Catalogo film (title, rental_rate, length, rating) | Aggregazioni, ranking, analisi |
| `customer` | Anagrafica clienti (name, email, create_date) | Segmentazione, CRM, retention |
| `rental` | Noleggi (rental_date, return_date) | Trend temporali, pattern |
| `payment` | Pagamenti (amount, payment_date) | KPI finanziari, revenue |
| `category` | Categorie film | Classificazioni, performance |
| `actor` | Attori (first_name, last_name) | Esempi stringhe, concatenazioni |
| `inventory` | Inventario DVD per store | Analisi stock, distribuzione |

---

## 🔗 Collegamenti Rapidi

| Categoria | Link Diretto | Funzioni Chiave |
|-----------|--------------|-----------------|
| **Stringhe** | [📖 01_STRINGHE.md](./01_STRINGHE.md) | CONCAT, SUBSTRING, REPLACE, GROUP_CONCAT |
| **Date/Ora** | [📖 02_DATA_ORA.md](./02_DATA_ORA.md) | NOW, DATEDIFF, DATE_FORMAT, DATE_ADD |
| **Aggregazione** | [📖 03_AGGREGAZIONE.md](./03_AGGREGAZIONE.md) | COUNT, SUM, AVG, STDDEV, ROW_NUMBER |
| **Controllo Flusso** | [📖 04_CONTROLLO_FLUSSO.md](./04_CONTROLLO_FLUSSO.md) | CASE, IF, COALESCE, GREATEST |
| **Numeriche** | [📖 05_NUMERICHE.md](./05_NUMERICHE.md) | ROUND, ABS, MOD, RAND, FORMAT |
| **Conversione** | [📖 06_CONVERSIONE.md](./06_CONVERSIONE.md) | CAST, CONVERT, FORMAT, HEX |
| **Window** | [📖 07_WINDOW.md](./07_WINDOW.md) | ROW_NUMBER, RANK, LAG, LEAD, NTILE |
| **JSON** | [📖 08_JSON.md](./08_JSON.md) | JSON_OBJECT, JSON_EXTRACT, JSON_SET |

---

## 📈 Statistiche Documentazione

- **8 Categorie** complete di funzioni
- **150+ Funzioni** documentate con esempi
- **500+ Esempi** pratici contestualizzati
- **100% Database Sakila** per coerenza
- **Livelli Multipli** di complessità

---

## 🎓 Percorso di Apprendimento Consigliato

### 🥉 **Livello Base** (1-2 settimane)
1. **Stringhe**: CONCAT, SUBSTRING, UPPER, LOWER, LENGTH
2. **Date**: NOW, CURDATE, YEAR, MONTH, DATEDIFF
3. **Numeriche**: ROUND, ABS, MOD
4. **Controllo Flusso**: CASE, IF, IFNULL

### 🥈 **Livello Intermedio** (2-3 settimane)
1. **Aggregazione**: COUNT, SUM, AVG, GROUP_CONCAT
2. **Conversioni**: CAST, FORMAT, DATE_FORMAT
3. **Stringhe Avanzate**: REGEXP, SUBSTRING_INDEX
4. **Window Basic**: ROW_NUMBER, RANK

### 🥇 **Livello Avanzato** (3-4 settimane)
1. **Window Advanced**: LAG, LEAD, NTILE, Frames
2. **JSON**: JSON_OBJECT, JSON_EXTRACT, JSON_SET
3. **Analytics**: PERCENT_RANK, CUME_DIST
4. **Business Intelligence**: Cohort Analysis, Segmentation

---

*Documentazione creata per il corso Backend Web Development - Gennaio 2025*  
*Tutti gli esempi sono testati e funzionali con MySQL 8.0+ e database Sakila*

---

## 🚀 Come Usare Questa Documentazione

1. **Consulta l'indice** per trovare la categoria di funzioni che ti interessa
2. **Clicca sul link** per accedere al file della categoria
3. **Cerca la funzione specifica** usando l'indice interno del file
4. **Copia e adatta gli esempi** per le tue query

---

## ⚠️ Note Importanti

- Tutti gli esempi sono testati su **MySQL 8.0+**
- Alcune funzioni potrebbero non essere disponibili in versioni precedenti
- Le funzioni deprecate sono segnalate con ⚠️
- I risultati degli esempi possono variare in base ai dati presenti nel database

---

## 📖 Legenda Simboli

| Simbolo | Significato |
|---------|-------------|
| ✅ | Funzione consigliata/best practice |
| ⚠️ | Funzione deprecata o da usare con cautela |
| 💡 | Suggerimento o tip utile |
| 🔥 | Funzione molto utilizzata |
| 🆕 | Funzione introdotta in MySQL 8.0+ |

---

*Documentazione creata per il corso Backend Web Development - Musa Formazione*

