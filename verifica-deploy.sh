#!/bin/bash

# ============================================
# SCRIPT VERIFICA DEPLOY - Controlla se i fix sono applicati
# ============================================

echo "============================================"
echo "  🔍 VERIFICA DEPLOY - Fix Import Pesate"
echo "============================================"
echo ""

# Colori
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verifica directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ ERRORE: Non sei nella directory del progetto Laravel!${NC}"
    echo "   Vai in: cd /home/agstudiodiital/public_html/magia"
    exit 1
fi

echo "📁 Directory: $(pwd)"
echo ""

# 1. Verifica PesateController
echo "🔍 [1/5] Verifica PesateController.php..."
if [ -f "app/Http/Controllers/Admin/PesateController.php" ]; then
    if grep -q "tipo_cliente.*=>.*'effettiva'" app/Http/Controllers/Admin/PesateController.php; then
        echo -e "   ${GREEN}✅ tipo_cliente fix presente${NC}"
    else
        echo -e "   ${RED}❌ tipo_cliente fix MANCANTE!${NC}"
        echo -e "   ${YELLOW}   Deploy necessario!${NC}"
    fi

    if grep -q "stato_cliente.*=>.*'attivo'" app/Http/Controllers/Admin/PesateController.php; then
        echo -e "   ${GREEN}✅ stato_cliente fix presente${NC}"
    else
        echo -e "   ${RED}❌ stato_cliente fix MANCANTE!${NC}"
        echo -e "   ${YELLOW}   Deploy necessario!${NC}"
    fi
else
    echo -e "   ${RED}❌ File NON trovato!${NC}"
fi
echo ""

# 2. Verifica GiornataProvaController
echo "🔍 [2/5] Verifica GiornataProvaController.php..."
if [ -f "app/Http/Controllers/GiornataProvaController.php" ]; then
    if grep -q "stato_cliente.*=>.*'attivo'" app/Http/Controllers/GiornataProvaController.php; then
        echo -e "   ${GREEN}✅ Fix presente${NC}"
    else
        echo -e "   ${RED}❌ Fix MANCANTE!${NC}"
    fi
else
    echo -e "   ${RED}❌ File NON trovato!${NC}"
fi
echo ""

# 3. Verifica migration codice_fiscale nullable
echo "🔍 [3/5] Verifica migration codice_fiscale..."
if php artisan migrate:status | grep -q "2025_11_19_093641_make_clienti_fields_nullable_for_import"; then
    echo -e "   ${GREEN}✅ Migration presente${NC}"

    if php artisan migrate:status | grep "2025_11_19_093641_make_clienti_fields_nullable_for_import" | grep -q "Ran"; then
        echo -e "   ${GREEN}✅ Migration eseguita${NC}"
    else
        echo -e "   ${YELLOW}⚠️  Migration NON eseguita!${NC}"
        echo -e "   ${YELLOW}   Esegui: php artisan migrate --force${NC}"
    fi
else
    echo -e "   ${RED}❌ Migration NON trovata!${NC}"
fi
echo ""

# 4. Verifica cache
echo "🔍 [4/5] Verifica cache..."
if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo -e "   ${YELLOW}⚠️  Cache route presente${NC}"
    echo -e "   ${YELLOW}   Pulisci con: php artisan route:clear${NC}"
else
    echo -e "   ${GREEN}✅ Cache route pulita${NC}"
fi

if [ -f "bootstrap/cache/config.php" ]; then
    echo -e "   ${YELLOW}⚠️  Cache config presente${NC}"
    echo -e "   ${YELLOW}   Pulisci con: php artisan config:clear${NC}"
else
    echo -e "   ${GREEN}✅ Cache config pulita${NC}"
fi
echo ""

# 5. Verifica log errori recenti
echo "🔍 [5/5] Verifica log errori recenti (ultimi 20)..."
if [ -f "storage/logs/laravel.log" ]; then
    ERRORI_RECENTI=$(tail -100 storage/logs/laravel.log | grep -c "stato_cliente.*attiva\|Data truncated")

    if [ "$ERRORI_RECENTI" -gt 0 ]; then
        echo -e "   ${RED}❌ Trovati $ERRORI_RECENTI errori recenti con 'attiva'${NC}"
        echo -e "   ${YELLOW}   Il server usa CODICE VECCHIO!${NC}"
        echo ""
        echo "   Ultimi errori:"
        tail -100 storage/logs/laravel.log | grep "stato_cliente.*attiva\|Data truncated" | tail -3
    else
        echo -e "   ${GREEN}✅ Nessun errore recente con 'attiva'${NC}"
    fi
else
    echo -e "   ${YELLOW}⚠️  File log non trovato${NC}"
fi
echo ""

echo "============================================"
echo "  📊 RIEPILOGO"
echo "============================================"
echo ""

# Conta i fix presenti
FIX_OK=0
FIX_MANCANTI=0

if [ -f "app/Http/Controllers/Admin/PesateController.php" ]; then
    grep -q "tipo_cliente.*=>.*'effettiva'" app/Http/Controllers/Admin/PesateController.php && ((FIX_OK++)) || ((FIX_MANCANTI++))
    grep -q "stato_cliente.*=>.*'attivo'" app/Http/Controllers/Admin/PesateController.php && ((FIX_OK++)) || ((FIX_MANCANTI++))
fi

if [ "$FIX_MANCANTI" -eq 0 ]; then
    echo -e "${GREEN}✅ TUTTI I FIX SONO PRESENTI${NC}"
    echo ""
    echo "Se l'import pesate continua a non funzionare:"
    echo "1. Pulisci cache: php artisan optimize:clear"
    echo "2. Controlla log: tail -f storage/logs/laravel.log"
    echo "3. Testa import di nuovo"
else
    echo -e "${RED}❌ MANCANO $FIX_MANCANTI FIX!${NC}"
    echo ""
    echo -e "${YELLOW}AZIONE RICHIESTA:${NC}"
    echo "1. Esegui: bash DEPLOY_URGENTE.sh"
    echo "2. Oppure: git pull origin claude/MAGIA_3-01LdNr3eLWNFtpfEAE2hHFJV"
fi
echo ""
