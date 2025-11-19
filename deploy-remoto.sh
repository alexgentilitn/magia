#!/bin/bash

# ============================================
# SCRIPT DEPLOY REMOTO - Esegue deploy via SSH
# ============================================
# Uso: Fornisci credenziali SSH e lo eseguo io

SSH_HOST="$1"  # es: server.aruba.it
SSH_USER="$2"  # es: agstudiodiital
SSH_PATH="$3"  # es: /home/agstudiodiital/public_html/magia

if [ -z "$SSH_HOST" ] || [ -z "$SSH_USER" ] || [ -z "$SSH_PATH" ]; then
    echo "❌ Uso: bash deploy-remoto.sh HOST USER PATH"
    echo "   Esempio: bash deploy-remoto.sh server.aruba.it utente /home/.../magia"
    exit 1
fi

echo "🚀 Eseguo deploy remoto su $SSH_HOST..."
echo ""

# Esegui script deploy sul server remoto via SSH
ssh "$SSH_USER@$SSH_HOST" << 'ENDSSH'
cd "$SSH_PATH"

# Download script
wget -q https://raw.githubusercontent.com/alexgentilitn/magia/claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV/DEPLOY_URGENTE.sh -O DEPLOY_URGENTE.sh

# Esegui
chmod +x DEPLOY_URGENTE.sh
bash DEPLOY_URGENTE.sh
ENDSSH

echo ""
echo "✅ Deploy completato!"
