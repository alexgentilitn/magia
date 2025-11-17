# Configurazione Server - MA.GIA DONNA

Guida alla configurazione corretta del web server per Laravel in sottocartella.

## 🚨 Problema Comune: URL con `/public`

Se vedi URL tipo:
```
https://www.agstudio.digital/magia/public/admin/...
```

Il server **NON è configurato correttamente**. Segui questa guida.

---

## ✅ Configurazione Apache

### 1. Trova il file di configurazione

```bash
# Di solito in uno di questi percorsi:
/etc/apache2/sites-available/000-default.conf
/etc/apache2/sites-available/magia.conf
```

### 2. Modifica il Virtual Host

```apache
<VirtualHost *:80>
    ServerName www.agstudio.digital

    # Root principale del sito
    DocumentRoot /var/www/html

    # Configurazione per Laravel in sottocartella /magia
    Alias /magia /var/www/html/magia/public

    <Directory /var/www/html/magia/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted

        # Abilita mod_rewrite
        RewriteEngine On

        # Reindirizza tutto a index.php
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>

    # Log
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### 3. Abilita mod_rewrite

```bash
sudo a2enmod rewrite
```

### 4. Verifica .htaccess in public/

File: `/var/www/html/magia/public/.htaccess`

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 5. Riavvia Apache

```bash
sudo systemctl restart apache2

# Verifica configurazione
sudo apache2ctl configtest
```

---

## ✅ Configurazione Nginx

### 1. File configurazione

File: `/etc/nginx/sites-available/default` o `/etc/nginx/sites-available/agstudio`

```nginx
server {
    listen 80;
    server_name www.agstudio.digital;

    # Root principale
    root /var/www/html;
    index index.html index.php;

    # Configurazione principale sito
    location / {
        try_files $uri $uri/ =404;
    }

    # Laravel in sottocartella /magia
    location /magia {
        alias /var/www/html/magia/public;
        try_files $uri $uri/ @magia;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }

    location @magia {
        rewrite /magia/(.*)$ /magia/index.php?/$1 last;
    }

    # Log
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
}
```

### 2. Testa e riavvia

```bash
# Test configurazione
sudo nginx -t

# Riavvia Nginx
sudo systemctl restart nginx
```

---

## 🔧 Configurazione Laravel

### File .env

```env
APP_URL=https://www.agstudio.digital/magia
# IMPORTANTE: NON mettere /public nell'URL!
```

### Permessi Directory

```bash
cd /var/www/html/magia

# Storage e cache scrivibili
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Backup directory
sudo chmod 755 storage/app/backups
sudo chown -R www-data:www-data storage/app/backups
```

---

## 🧪 Test Configurazione

### 1. Test Rotte

Apri console browser (F12) e prova un'operazione. L'URL deve essere:

```
✅ CORRETTO: https://www.agstudio.digital/magia/admin/super-admin/backup/create
❌ SBAGLIATO: https://www.agstudio.digital/magia/public/admin/super-admin/backup/create
```

### 2. Test File Statici

Prova ad accedere:
```
https://www.agstudio.digital/magia/js/admin/super-admin.js
```

Deve funzionare (non 404).

### 3. Test Laravel

```bash
php artisan route:list --name=backup
```

Verifica che le rotte esistano.

---

## 🐛 Troubleshooting

### Errore 403 Forbidden

```bash
# Controlla permessi
ls -la /var/www/html/magia/public

# Dovrebbero essere:
# drwxr-xr-x per directory
# -rw-r--r-- per file
```

### Errore 500 Internal Server Error

```bash
# Controlla log Apache
sudo tail -f /var/log/apache2/error.log

# O log Nginx
sudo tail -f /var/log/nginx/error.log

# E log Laravel
tail -f /var/www/html/magia/storage/logs/laravel.log
```

### Asset non caricati (CSS/JS)

```bash
# Rigenera asset
cd /var/www/html/magia
npm run build

# Verifica permessi
chmod -R 755 public/build
```

### mysqldump non trovato

```bash
# Installa mysql-client
sudo apt-get update
sudo apt-get install mysql-client

# Verifica
which mysqldump
# Deve restituire: /usr/bin/mysqldump
```

---

## 📝 Checklist Configurazione

- [ ] Virtual host punta a `magia/public`
- [ ] mod_rewrite abilitato (Apache)
- [ ] `.htaccess` presente in `public/`
- [ ] `APP_URL` corretto in `.env` (senza /public)
- [ ] Permessi storage: 775
- [ ] Permessi bootstrap/cache: 775
- [ ] Owner: www-data
- [ ] mysqldump installato
- [ ] Test rotte funzionanti (senza /public nell'URL)
- [ ] Asset caricati correttamente
- [ ] Backup directory creata e scrivibile

---

## 🔒 Sicurezza

### File da proteggere

Assicurati che questi file NON siano accessibili via web:

```apache
# In Apache config
<DirectoryMatch "/var/www/html/magia/(\.git|storage|bootstrap|database|tests)">
    Require all denied
</DirectoryMatch>

<FilesMatch "(\.env|composer\.json|composer\.lock|package\.json)">
    Require all denied
</FilesMatch>
```

### HTTPS

```bash
# Installa Certbot (Let's Encrypt)
sudo apt-get install certbot python3-certbot-apache

# Ottieni certificato
sudo certbot --apache -d www.agstudio.digital
```

---

## 📞 Supporto

Se dopo aver seguito questa guida il problema persiste:

1. Controlla i log:
   - Apache/Nginx error log
   - Laravel log (`storage/logs/laravel.log`)
   - Console browser (F12)

2. Verifica con:
   ```bash
   php artisan about
   php artisan route:list
   ```

3. Invia output di:
   ```bash
   apache2ctl -S  # Apache
   # oppure
   sudo nginx -T   # Nginx
   ```
