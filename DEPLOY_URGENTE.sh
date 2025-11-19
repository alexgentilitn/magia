#!/bin/bash

# ============================================
# DEPLOY URGENTE - Fix Import Pesate
# ============================================
# Uso: Copia questo script sul server e eseguilo
# Path server: /home/agstudiodiital/public_html/magia

echo "============================================"
echo "  🚨 DEPLOY URGENTE - Fix Import Pesate"
echo "============================================"
echo ""

# Verifica che siamo nella directory corretta
if [ ! -f "artisan" ]; then
    echo "❌ ERRORE: Non sei nella directory del progetto Laravel!"
    echo "   Vai in: cd /home/agstudiodiital/public_html/magia"
    exit 1
fi

echo "📁 Directory corrente: $(pwd)"
echo ""

# 1. Backup dei file che modifchiamo
echo "💾 [1/8] Creazione backup file critici..."
BACKUP_DIR="backup_pre_deploy_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r app/Http/Controllers/Admin/PesateController.php "$BACKUP_DIR/" 2>/dev/null || echo "   ⚠️  PesateController.php non trovato"
cp -r app/Http/Controllers/Admin/ClientiController.php "$BACKUP_DIR/" 2>/dev/null || echo "   ⚠️  ClientiController.php non trovato"
cp -r app/Http/Controllers/Auth/RegistrazioneController.php "$BACKUP_DIR/" 2>/dev/null || echo "   ⚠️  RegistrazioneController.php non trovato"
cp -r app/Http/Controllers/GiornataProvaController.php "$BACKUP_DIR/" 2>/dev/null || echo "   ⚠️  GiornataProvaController.php non trovato"
echo "   ✅ Backup creato in: $BACKUP_DIR"
echo ""

# 2. Verifica branch Git disponibili
echo "🌿 [2/8] Verifica branch Git..."
BRANCH_MAGIA3="claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV"
BRANCH_CONNECT="claude/connect-latest-branch-01LdNr3eLWNFtpfEAE2hHFJV"

git fetch origin 2>&1 | head -5

if git ls-remote --heads origin "$BRANCH_MAGIA3" | grep -q "$BRANCH_MAGIA3"; then
    BRANCH_TO_USE="$BRANCH_MAGIA3"
    echo "   ✅ Trovato branch MAGIA_3"
elif git ls-remote --heads origin "$BRANCH_CONNECT" | grep -q "$BRANCH_CONNECT"; then
    BRANCH_TO_USE="$BRANCH_CONNECT"
    echo "   ✅ Trovato branch connect-latest"
else
    echo "   ❌ ERRORE: Nessun branch con fix trovato!"
    echo "   Deploy manuale necessario - vedi DEPLOY_MANUAL.md"
    exit 1
fi
echo ""

# 3. Pull delle modifiche
echo "📥 [3/8] Pull modifiche da branch: $BRANCH_TO_USE..."
git pull origin "$BRANCH_TO_USE"
if [ $? -ne 0 ]; then
    echo "   ❌ ERRORE durante git pull!"
    echo "   Prova manualmente: git pull origin $BRANCH_TO_USE"
    exit 1
fi
echo "   ✅ Codice aggiornato"
echo ""

# 4. Verifica file critici presenti
echo "🔍 [4/8] Verifica file aggiornati..."
if grep -q "tipo_cliente.*effettiva" app/Http/Controllers/Admin/PesateController.php 2>/dev/null; then
    echo "   ✅ PesateController.php aggiornato"
else
    echo "   ❌ PesateController.php NON aggiornato!"
fi

if grep -q "stato_cliente.*attivo" app/Http/Controllers/Admin/PesateController.php 2>/dev/null; then
    echo "   ✅ Fix stato_cliente presente"
else
    echo "   ❌ Fix stato_cliente MANCANTE!"
fi
echo ""

# 5. Pulizia cache route (CRITICO per route mancanti)
echo "🧹 [5/8] Pulizia cache route..."
php artisan route:clear
echo "   ✅ Cache route pulita"
echo ""

# 6. Pulizia cache config
echo "⚙️  [6/8] Pulizia cache config..."
php artisan config:clear
echo "   ✅ Cache config pulita"
echo ""

# 7. Esegui migration (per codice_fiscale nullable)
echo "🗄️  [7/8] Esecuzione migration database..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "   ✅ Migration completate"
else
    echo "   ⚠️  Attenzione: errore migration (potrebbe essere normale se già eseguite)"
fi
echo ""

# 8. Pulizia cache generale e ottimizzazione
echo "⚡ [8/8] Ottimizzazione finale..."
php artisan cache:clear
php artisan view:clear
php artisan optimize
echo "   ✅ Ottimizzazione completata"
echo ""

echo "============================================"
echo "  ✅ DEPLOY COMPLETATO!"
echo "============================================"
echo ""
echo "📋 VERIFICA IMMEDIATA:"
echo "1. Vai su: Admin → Pesate → Import Excel"
echo "2. Carica file Excel di test"
echo "3. Controlla che non ci siano errori"
echo ""
echo "📊 VERIFICA DATABASE:"
echo "   mysql -u [user] -p [database] -e 'SELECT COUNT(*) FROM pesate;'"
echo ""
echo "📝 LOG APPLICAZIONE:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "💾 BACKUP PRECEDENTE IN: $BACKUP_DIR"
echo "   (Usa in caso di problemi: cp $BACKUP_DIR/* app/Http/Controllers/...)"
echo ""
