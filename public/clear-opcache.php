<?php

echo "CLEAR SERVER CACHE\n";
echo "==================\n\n";

// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
} else {
    echo "⚠️  OPcache non disponibile\n";
}

// Clear APCu
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu cleared\n";
} else {
    echo "⚠️  APCu non disponibile\n";
}

// Clear realpath cache
clearstatcache(true);
echo "✅ Stat cache cleared\n\n";

echo "Fatto! Riprova ad accedere all'applicazione.\n";
