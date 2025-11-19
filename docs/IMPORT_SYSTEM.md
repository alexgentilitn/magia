# Sistema Import Universale - Pesate

Sistema intelligente per importare file da diverse bilance con formati diversi.

## 🎯 Caratteristiche

### Formati Supportati
- ✅ **Excel**: `.xlsx`, `.xls`
- ✅ **CSV**: `.csv` con rilevamento automatico separatore
- ✅ **TXT**: `.txt` file di testo delimitati

### Separatori CSV Rilevati Automaticamente
- Virgola: `,`
- Punto e virgola: `;`
- Tab: `\t`
- Pipe: `|`

### Encoding Supportati
- UTF-8
- ISO-8859-1
- Windows-1252
- ASCII

## 🔧 Funzionalità Intelligenti

### 1. Rilevamento Formato Data
Se Excel ha formattato una colonna numerica come Data (es. BMI 19.09 → 19:09), il sistema:
- Rileva il formato `h\.mm`
- Converte in DateTime
- Estrae ore:minuti (19:09)
- Ricostruisce valore originale: `19.09` ✅

**Esempio:**
```
Numero seriale Excel: 45974.79791666667
DateTime: 2025-11-13 19:09:00
Valore ricostruito: 19.09
```

### 2. Pulizia Valori Universale
Gestisce automaticamente tutti i formati numerici:

#### Percentuali
- `27.5%` → `27.5`
- `0.275` → `27.5`
- `27,5%` → `27.5` (formato europeo)

#### Valute
- `€15.50` → `15.5`
- `$20.00` → `20.0`
- `£10.99` → `10.99`

#### Formati Numerici
- Europeo: `1.234,56` → `1234.56`
- Americano: `1,234.56` → `1234.56`
- Con unità: `57.5 kg` → `57.5`
- Con spazi: `1 280` → `1280`

### 3. Matching Intelligente Colonne
Il sistema riconosce automaticamente le colonne anche con nomi diversi:

| Campo DB | Varianti Riconosciute |
|----------|----------------------|
| **bmi** | bmi, BMI, body mass, indice massa, b.m.i |
| **peso_corporeo_senza_grassi** | peso corporeo senza grassi, peso senza grassi, FFM, fat free mass, massa magra, lean body mass |
| **muscolo_scheletrico** | muscolo scheletrico, skeletal muscle, musc schel, SMM |
| **grasso_corporeo** | grasso corporeo, body fat, BF%, massa grassa, fat% |
| **massa_muscolare** | massa muscolare, muscle mass, massa musc |
| **bmr** | BMR, metabolismo basale, basal metabolic, kcal |

**Sistema di scoring:**
- Match esatto: 1000 punti
- Match contenuto: 500 punti
- Match parole separate: 100 punti

### 4. Validazione Range
Valori fuori range vengono automaticamente scartati:

| Campo | Range Valido |
|-------|-------------|
| BMI | 10 - 100 |
| Peso | 20 - 300 kg |
| Grasso Viscerale | 0 - 100 |
| BMR | 500 - 5000 kcal |
| Età Metabolica | 10 - 120 anni |

## 📁 Struttura Import

### File Excel/CSV Standard
```
| Cognome | Nome | Peso | BMI | Grasso corporeo % | ... |
|---------|------|------|-----|-------------------|-----|
| Rossi   | Mario| 75.5 |25.3 | 18.5%             | ... |
| Verdi   | Luca | 68.2 |22.1 | 15.2%             | ... |
```

### File con Formato Europeo
```
| Cognome | Nome | Peso  | BMI   | Grasso corporeo % |
|---------|------|-------|-------|-------------------|
| Rossi   | Mario| 75,5  |25,3   | 18,5%             |
| Verdi   | Luca | 68,2  |22,1   | 15,2%             |
```

### File CSV con Separatore Punto e Virgola
```csv
Cognome;Nome;Peso;BMI;Grasso corporeo %
Rossi;Mario;75,5;25,3;18,5%
Verdi;Luca;68,2;22,1;15,2%
```

## 🚀 Utilizzo

### 1. Importa File
```
Admin → Pesate → Importa da Excel/CSV
```

### 2. Carica File
- Seleziona file (Excel, CSV o TXT)
- Scegli sede
- Clicca "Carica e Mappa Colonne"

### 3. Verifica Mapping
- Sistema rileva automaticamente le colonne
- Colonne con badge verde "✓ Auto" sono auto-rilevate
- Verifica l'anteprima di ogni campo
- Modifica mapping se necessario

### 4. Anteprima e Conferma
- Controlla anteprima dati
- Clienti nuovi sono evidenziati
- Conferma import

## 🛠️ Funzioni Tecniche

### `rilevaSeparatoreCSV($filePath)`
Rileva automaticamente il separatore più frequente nelle prime 5 righe.

### `rilevaEncoding($filePath)`
Rileva encoding del file analizzando i primi 4KB.

### `pulisciValoreUniversale($valore)`
Pulisce valori da:
- Simboli valuta (€, $, £, ¥, ₹)
- Percentuali (%)
- Unità di misura (kg, g, cm, m, kcal)
- Spazi e separatori migliaia
- Gestisce formato europeo vs americano

### `leggiValoreCellaUniversale($cell)`
Legge valore da cella Excel:
1. Rileva formato Data → estrae ore:minuti
2. Applica pulizia universale
3. Ritorna valore numerico pulito

### `normalizzaPercentuale($valore)`
Converte percentuali in formato standard:
- `0.275` → `27.5`
- `27.5%` → `27.5`
- `27.5` → `27.5`

## 📊 Log e Diagnostica

Tutti gli import sono loggati in `storage/logs/laravel.log`:

```
Import Pesate - Separatore CSV rilevato
Import Pesate - Cella con formato Data - valore ricostruito
Import Pesate - BMI fuori range rilevato
```

### Strumenti Diagnostici

**Test Lettura BMI:**
```
https://tuosito.it/diagnose-bmi-precision.php
```

**Test Precisione Dati:**
```
https://tuosito.it/test-precision.php
```

## 🔮 Estensioni Future

### Supporto Planned
- [ ] File JSON da API bilance smart
- [ ] XML da export medicali
- [ ] Sincronizzazione automatica cloud bilance
- [ ] Import batch multipli file
- [ ] Template personalizzati per bilancia

### Integrazione API
```php
// Esempio integrazione futura API bilancia
POST /api/import/bilancia
{
  "bilancia_id": "tanita-bc-545n",
  "data": [...],
  "formato": "json"
}
```

## ⚠️ Note Importanti

1. **File Excel corrotti:** Se il BMI mostra sempre NULL, verifica formato colonna in Excel (deve essere Numero, non Data)

2. **Encoding problemi:** Se caratteri accentati non vengono letti, salva file come UTF-8

3. **Separatore errato:** Sistema rileva automaticamente, ma se fallisce usa `,` come default

4. **Mapping manuale:** Sempre verificare anteprima prima di confermare import

## 📞 Supporto

Per problemi o domande:
- Controlla log: `storage/logs/laravel.log`
- Usa strumenti diagnostici
- Verifica formato file Excel originale
