#!/bin/bash

# ============================================
# DEPLOY FTP MANUALE - Upload file via FTP
# ============================================

echo "🚀 Deploy FTP Manuale"
echo "===================="
echo ""

# Carica credenziali da .env.ftp
if [ ! -f ".env.ftp" ]; then
    echo "❌ File .env.ftp non trovato!"
    echo ""
    echo "Crea il file .env.ftp con le credenziali:"
    echo "  cp .env.ftp.example .env.ftp"
    echo "  nano .env.ftp"
    echo ""
    exit 1
fi

source .env.ftp

echo "📋 Configurazione:"
echo "   Host: $FTP_HOST"
echo "   User: $FTP_USER"
echo "   Path: $FTP_PATH"
echo ""

# Verifica che lftp sia installato
if ! command -v lftp &> /dev/null; then
    echo "❌ lftp non installato!"
    echo "   Installa con: sudo apt-get install lftp"
    exit 1
fi

echo "📤 Inizio upload file..."
echo ""

# Upload con lftp
lftp -u "$FTP_USER,$FTP_PASSWORD" "$FTP_HOST" <<EOF
set ftp:ssl-allow no
cd $FTP_PATH

# Upload file modificati
mirror --reverse --delete --verbose \
  --exclude .git/ \
  --exclude .github/ \
  --exclude node_modules/ \
  --exclude .env.example \
  --exclude README.md \
  --exclude .env.ftp \
  --exclude .env.ftp.example \
  ./ ./

bye
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Deploy completato con successo!"
    echo ""
    echo "⚠️  IMPORTANTE: Esegui sul server:"
    echo "   cd $FTP_PATH"
    echo "   php artisan route:clear"
    echo "   php artisan config:clear"
    echo "   php artisan cache:clear"
    echo "   php artisan migrate --force"
    echo "   php artisan optimize"
else
    echo ""
    echo "❌ Errore durante il deploy!"
    exit 1
fi
