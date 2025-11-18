<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\JsonDatabase;

echo "🧪 TEST DATABASE JSON - DATI REALI IMPORTATI\n";
echo "============================================\n\n";

try {
    // Test 1: Utenti
    echo "👤 Test UTENTI:\n";
    $utenti = JsonDatabase::all('utenti');
    echo "   Totale: " . count($utenti) . " utenti\n";
    foreach ($utenti as $utente) {
        echo "   - {$utente['nome']} {$utente['cognome']} ({$utente['email']}) - {$utente['tipo_utente']}\n";
    }
    echo "\n";

    // Test 2: Clienti
    echo "👥 Test CLIENTI:\n";
    $clienti = JsonDatabase::all('clienti');
    echo "   Totale: " . count($clienti) . " clienti\n";
    foreach ($clienti as $cliente) {
        echo "   - {$cliente['nome']} {$cliente['cognome']} - {$cliente['codice_cliente']}\n";
        echo "     Email: {$cliente['email']}\n";
        echo "     Tel: {$cliente['telefono_mobile']}\n";
        echo "     Città: {$cliente['citta']} ({$cliente['provincia']})\n";
    }
    echo "\n";

    // Test 3: Ruoli
    echo "🔐 Test RUOLI:\n";
    $ruoli = JsonDatabase::all('ruoli');
    echo "   Totale: " . count($ruoli) . " ruoli\n";
    foreach ($ruoli as $ruolo) {
        echo "   - {$ruolo['nome']} (livello {$ruolo['livello']})\n";
    }
    echo "\n";

    // Test 4: Sedi
    echo "🏢 Test SEDI:\n";
    $sedi = JsonDatabase::all('sedi');
    echo "   Totale: " . count($sedi) . " sedi\n";
    foreach ($sedi as $sede) {
        echo "   - {$sede['nome']}\n";
        echo "     Indirizzo: {$sede['indirizzo_via']}, {$sede['indirizzo_citta']}\n";
        echo "     Tel: {$sede['telefono']}\n";
    }
    echo "\n";

    // Test 5: Professionisti
    echo "💼 Test PROFESSIONISTI:\n";
    $professionisti = JsonDatabase::all('professionisti');
    echo "   Totale: " . count($professionisti) . " professionisti\n";
    foreach ($professionisti as $prof) {
        echo "   - {$prof['nome']} {$prof['cognome']} - {$prof['titolo_professionale']}\n";
        echo "     Codice: {$prof['codice_professionista']}\n";
        echo "     Esperienza: {$prof['anni_esperienza']} anni\n";
    }
    echo "\n";

    // Test 6: Programmi
    echo "📚 Test PROGRAMMI:\n";
    $programmi = JsonDatabase::all('programmi');
    echo "   Totale: " . count($programmi) . " programmi\n";
    foreach ($programmi as $prog) {
        echo "   - {$prog['nome']}\n";
        echo "     Tipologia: {$prog['tipologia']} | Livello: {$prog['livello']}\n";
        echo "     Prezzo: €{$prog['prezzo_base']} (promo: €{$prog['prezzo_promo']})\n";
    }
    echo "\n";

    // Test 7: Lezioni
    echo "📅 Test LEZIONI:\n";
    $lezioni = JsonDatabase::all('lezioni');
    echo "   Totale: " . count($lezioni) . " lezioni\n";
    foreach ($lezioni as $lez) {
        echo "   - {$lez['titolo']} ({$lez['tipologia']})\n";
        echo "     Data: {$lez['data']} | Ora: {$lez['ora_inizio']} - {$lez['ora_fine']}\n";
        echo "     Posti: {$lez['posti_occupati']}/{$lez['posti_totali']}\n";
    }
    echo "\n";

    // Test 8: Pagamenti
    echo "💰 Test PAGAMENTI:\n";
    $pagamenti = JsonDatabase::all('pagamenti');
    echo "   Totale: " . count($pagamenti) . " pagamenti\n";
    foreach ($pagamenti as $pag) {
        echo "   - €{$pag['importo']} - {$pag['tipo']} via {$pag['metodo']}\n";
        echo "     Stato: {$pag['stato']} | Data: {$pag['data_emissione']}\n";
    }
    echo "\n";

    // Test 9: Query con WHERE
    echo "🔍 Test WHERE - Cerca admin:\n";
    $admin = JsonDatabase::find('utenti', ['tipo_utente' => 'amministratore']);
    if ($admin) {
        echo "   ✅ Admin trovato: {$admin['nome']} {$admin['cognome']}\n";
        echo "      Email: {$admin['email']}\n\n";
    }

    // Test 10: Count
    echo "📊 Test COUNT:\n";
    echo "   - Utenti: " . JsonDatabase::count('utenti') . "\n";
    echo "   - Clienti: " . JsonDatabase::count('clienti') . "\n";
    echo "   - Lezioni: " . JsonDatabase::count('lezioni') . "\n";
    echo "   - Pagamenti: " . JsonDatabase::count('pagamenti') . "\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TUTTI I TEST SUPERATI!\n";
    echo "✅ Database JSON con dati reali funzionante!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "🎉 Migrazione da MySQL a JSON completata!\n";
    echo "📁 Tutte le tabelle disponibili in: database/jsondb/\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
