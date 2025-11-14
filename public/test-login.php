<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "TEST AUTENTICAZIONE E POST-LOGIN\n";
echo "=================================\n\n";

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Utente;
use Illuminate\Support\Facades\Auth;

try {
    // Test 1: Carica un utente
    echo "Test 1: Carica utente dal JSON\n";
    $utenti = Utente::all();
    echo "   ✅ Utenti trovati: {$utenti->count()}\n\n";

    if ($utenti->count() == 0) {
        echo "   ❌ Nessun utente nel database!\n";
        exit;
    }

    // Test 2: Carica primo utente
    echo "Test 2: Carica primo utente\n";
    $utente = $utenti->first();
    echo "   ID: {$utente->id}\n";
    echo "   Email: {$utente->email}\n";
    echo "   Nome: {$utente->nome} {$utente->cognome}\n";
    echo "   ✅ Utente caricato\n\n";

    // Test 3: Verifica relazione ruolo
    echo "Test 3: Verifica relazione ruolo\n";
    try {
        $ruolo = $utente->ruolo();
        if ($ruolo) {
            echo "   ✅ Ruolo caricato: " . ($ruolo->nome ?? 'N/A') . "\n\n";
        } else {
            echo "   ⚠️  Nessun ruolo associato\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Errore caricamento ruolo: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    }

    // Test 4: Verifica metodi utente
    echo "Test 4: Metodi utente\n";
    try {
        $nomeCompleto = $utente->getNomeCompletoAttribute();
        echo "   ✅ getNomeCompleto: {$nomeCompleto}\n";

        $isAdmin = $utente->isSuperAdmin();
        echo "   ✅ isSuperAdmin: " . ($isAdmin ? 'SI' : 'NO') . "\n";

        $attivo = $utente->attivo;
        echo "   ✅ attivo: " . ($attivo ? 'SI' : 'NO') . "\n\n";
    } catch (\Exception $e) {
        echo "   ❌ Errore metodi: " . $e->getMessage() . "\n\n";
    }

    // Test 5: Simula autenticazione
    echo "Test 5: Simula autenticazione\n";
    try {
        Auth::login($utente);
        $logged = Auth::check();
        echo "   ✅ Auth::login eseguito\n";
        echo "   ✅ Auth::check: " . ($logged ? 'SI' : 'NO') . "\n";

        if ($logged) {
            $user = Auth::user();
            echo "   ✅ Auth::user: {$user->email}\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Errore autenticazione: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "   Trace:\n";
        echo substr($e->getTraceAsString(), 0, 1500) . "\n\n";
    }

    // Test 6: Simula richiesta dashboard (route post-login)
    echo "Test 6: Simula richiesta dashboard\n";
    try {
        $request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');

        // Simula utente autenticato
        $request->setUserResolver(function () use ($utente) {
            return $utente;
        });

        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);

        echo "   Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() == 200) {
            echo "   ✅ Dashboard funziona!\n";
        } else if ($response->getStatusCode() == 302) {
            echo "   ⚠️  Redirect a: " . $response->headers->get('Location') . "\n";
        } else if ($response->getStatusCode() == 500) {
            echo "   ❌ Errore 500 sulla dashboard!\n";
            echo "   Content: " . substr($response->getContent(), 0, 500) . "\n";
        }

        $kernel->terminate($request, $response);

    } catch (\Exception $e) {
        echo "   ❌ ERRORE DASHBOARD: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "   Trace:\n";
        echo substr($e->getTraceAsString(), 0, 1500) . "\n";
    }

} catch (\Exception $e) {
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE CRITICO:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
