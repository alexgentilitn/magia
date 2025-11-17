# Sistema di Backup Database - MA.GIA DONNA

Sistema completo di backup automatico e manuale del database MySQL con interfaccia di gestione nel pannello Super Admin.

## 🚀 Funzionalità

### Backup Manuali
- ✅ Creazione backup on-demand tramite interfaccia web
- ✅ Download dei file di backup
- ✅ Eliminazione backup obsoleti
- ✅ Visualizzazione lista completa backup con dettagli
- ✅ Aggiunta descrizioni personalizzate ai backup
- ✅ Statistiche in tempo reale (numero backup, spazio utilizzato, ultimo backup)

### Backup Automatici
- ✅ Command Artisan per backup programmati
- ✅ Pulizia automatica backup vecchi (mantiene ultimi 30)
- ✅ Log completo di tutte le operazioni
- ✅ Metadati per ogni backup (data, autore, descrizione)

### Sicurezza
- ✅ Accesso limitato solo a Super Admin
- ✅ File backup salvati in `storage/app/backups` (fuori dalla root pubblica)
- ✅ Validazione nomi file per prevenire path traversal
- ✅ Gestione errori completa
- ✅ Retention policy (max 30 backup)

## 📋 Requisiti

### Server Requirements
- PHP 8.1+
- Laravel 10+
- MySQL 5.7+ o MariaDB 10.3+
- **mysqldump** installato sul server
- Permessi di scrittura su `storage/app/backups`

### Verificare mysqldump
```bash
which mysqldump
# Dovrebbe restituire: /usr/bin/mysqldump (o simile)
```

Se mysqldump non è installato:
```bash
# Ubuntu/Debian
sudo apt-get install mysql-client

# CentOS/RHEL
sudo yum install mysql

# macOS
brew install mysql-client
```

## 🛠️ Configurazione

### 1. Permessi Directory
```bash
chmod 755 storage/app/backups
```

### 2. Configurazione Database
Assicurati che le credenziali nel file `.env` siano corrette:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_database
DB_USERNAME=username
DB_PASSWORD=password
```

### 3. Backup Automatici (Opzionale)

#### Configurare Laravel Scheduler

Modifica `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Backup giornaliero alle 2:00 AM
    $schedule->command('backup:database')
        ->dailyAt('02:00')
        ->description('Backup automatico database');

    // Oppure backup ogni 6 ore
    // $schedule->command('backup:database')
    //     ->everySixHours();

    // Oppure backup settimanale (domenica alle 3:00 AM)
    // $schedule->command('backup:database')
    //     ->weekly()
    //     ->sundays()
    //     ->at('03:00');
}
```

#### Attivare il Cron Job sul server

Aggiungi al crontab:
```bash
crontab -e
```

Aggiungi questa riga:
```
* * * * * cd /percorso/al/progetto && php artisan schedule:run >> /dev/null 2>&1
```

#### Esempio frequenze comuni

```php
// Ogni ora
$schedule->command('backup:database')->hourly();

// Ogni giorno alle 3:00 AM
$schedule->command('backup:database')->dailyAt('03:00');

// Ogni lunedì alle 1:00 AM
$schedule->command('backup:database')->weeklyOn(1, '01:00');

// Primo giorno del mese alle 2:00 AM
$schedule->command('backup:database')->monthlyOn(1, '02:00');
```

## 📖 Utilizzo

### Backup Manuale via Web

1. Accedi al pannello **Super Admin**
2. Nella sezione "Backup Database":
   - **Crea Backup Ora**: Crea un nuovo backup con descrizione opzionale
   - **Visualizza Backup**: Mostra lista completa con opzioni download/elimina
   - **Pulisci Backup Vecchi**: Elimina backup oltre il limite di 30

### Backup Manuale via CLI

```bash
# Backup semplice
php artisan backup:database

# Backup con descrizione
php artisan backup:database --description="Prima del deploy v2.0"
```

### Testare il Backup

```bash
# Testa il comando
php artisan backup:database --description="Test backup"

# Verifica i file creati
ls -lh storage/app/backups/

# Controlla i log
tail -f storage/logs/laravel.log
```

## 📂 Struttura File

```
storage/
└── app/
    └── backups/
        ├── backup_2025-01-15_14-30-00.sql
        ├── backup_2025-01-16_02-00-00.sql
        ├── .metadata_backup_2025-01-15_14-30-00.sql.json
        ├── .metadata_backup_2025-01-16_02-00-00.sql.json
        └── .gitignore
```

### Metadata File (JSON)
```json
{
    "filename": "backup_2025-01-15_14-30-00.sql",
    "description": "Backup prima del deploy",
    "created_at": "2025-01-15 14:30:00",
    "created_by": "Admin"
}
```

## 🔧 Manutenzione

### Modificare il Numero Massimo di Backup

Modifica `app/Services/BackupService.php`:

```php
const MAX_BACKUPS = 30; // Cambia questo valore
```

### Spazio su Disco

Monitora lo spazio utilizzato dai backup:

```bash
# Dimensione totale backup
du -sh storage/app/backups

# Lista backup ordinati per dimensione
ls -lhS storage/app/backups/*.sql
```

### Ripristino da Backup

```bash
# Ripristina un backup specifico
mysql -u username -p database_name < storage/app/backups/backup_2025-01-15_14-30-00.sql

# Con credenziali da .env
mysql -h localhost -u $(grep DB_USERNAME .env | cut -d '=' -f2) -p$(grep DB_PASSWORD .env | cut -d '=' -f2) $(grep DB_DATABASE .env | cut -d '=' -f2) < storage/app/backups/backup_2025-01-15_14-30-00.sql
```

## 🐛 Troubleshooting

### Errore "mysqldump non disponibile"

```bash
# Installa mysql-client
sudo apt-get install mysql-client

# Verifica installazione
which mysqldump
```

### Errore permessi directory

```bash
# Dai permessi corretti
chmod -R 755 storage/app/backups
chown -R www-data:www-data storage/app/backups  # Ubuntu/Debian
```

### Backup troppo lenti

Per database molto grandi, considera:
- Backup incrementali
- Compressione gzip dei backup
- Backup di tabelle specifiche invece di tutto il DB

### Log Backup

Tutti i backup sono loggati in `storage/logs/laravel.log`:

```bash
# Visualizza log backup
grep "Backup database" storage/logs/laravel.log

# Errori backup
grep "Errore backup" storage/logs/laravel.log
```

## 📊 Best Practices

1. **Frequenza Backup**
   - Database piccolo (<100MB): Ogni 6 ore
   - Database medio (100MB-1GB): Ogni 12-24 ore
   - Database grande (>1GB): Giornaliero

2. **Retention Policy**
   - Mantenere almeno 7 giorni di backup
   - Backup settimanali per 1 mese
   - Backup mensili per 1 anno

3. **Verifica Backup**
   - Testa il ripristino periodicamente
   - Verifica l'integrità dei file SQL

4. **Backup Offsite**
   - Copia i backup su storage esterno (S3, FTP, etc.)
   - Non fare affidamento solo sui backup locali

## 🔐 Sicurezza

- ✅ I backup sono salvati fuori dalla directory pubblica
- ✅ I file `.sql` non sono accessibili via web
- ✅ Accesso limitato solo a utenti con ruolo `super_admin`
- ✅ Validazione input per prevenire path traversal
- ⚠️ Considera la cifratura per backup sensibili

## 📝 License

Questo sistema è parte del progetto MA.GIA DONNA ed è soggetto alla stessa licenza del progetto principale.

## 👤 Autore

Sviluppato per AG Studio Digital - MA.GIA DONNA
