#!/bin/bash

# ============================================
# SCRIPT DEPLOY - Aggiorna server produzione
# ============================================
# Funzione: Pulisce cache e applica aggiornamenti codice
# Uso: bash deploy-update.sh

echo "======================================"
echo "  DEPLOY UPDATE - Magia Application"
echo "======================================"
echo ""

# 1. Pulizia cache route
echo "📦 [1/6] Pulizia cache route..."
php artisan route:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache route pulita"
else
    echo "   ❌ Errore pulizia cache route"
    exit 1
fi
echo ""

# 2. Pulizia cache config
echo "⚙️  [2/6] Pulizia cache configurazione..."
php artisan config:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache config pulita"
else
    echo "   ❌ Errore pulizia cache config"
    exit 1
fi
echo ""

# 3. Pulizia cache view
echo "👁️  [3/6] Pulizia cache view..."
php artisan view:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache view pulita"
else
    echo "   ❌ Errore pulizia cache view"
    exit 1
fi
echo ""

# 4. Pulizia cache applicazione
echo "🧹 [4/6] Pulizia cache applicazione..."
php artisan cache:clear
if [ $? -eq 0 ]; then
    echo "   ✅ Cache applicazione pulita"
else
    echo "   ❌ Errore pulizia cache applicazione"
    exit 1
fi
echo ""

# 5. Esegui migration (se necessario)
echo "🗄️  [5/6] Verifica migration database..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "   ✅ Migration completate"
else
    echo "   ⚠️  Attenzione: problema con le migration"
fi
echo ""

# 6. Ottimizza applicazione (cache route, config, view)
echo "⚡ [6/6] Ottimizzazione applicazione..."
php artisan optimize
if [ $? -eq 0 ]; then
    echo "   ✅ Ottimizzazione completata"
else
    echo "   ⚠️  Attenzione: problema con l'ottimizzazione"
fi
echo ""

echo "======================================"
echo "  ✅ DEPLOY COMPLETATO CON SUCCESSO!"
echo "======================================"
echo ""
echo "Verifica che l'applicazione funzioni correttamente:"
echo "- Prova l'import delle pesate"
echo "- Controlla i log: tail -f storage/logs/laravel.log"
echo ""
