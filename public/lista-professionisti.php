<?php
/**
 * Script: Lista Professionisti Disponibili
 *
 * Funzione: Mostra tutti i professionisti nel database
 * per trovare gli ID corretti da usare
 */

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Lista Professionisti</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
h1 { color: #333; }
table { width: 100%; border-collapse: collapse; background: #fff; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
th { background: #007bff; color: white; font-weight: bold; }
tr:nth-child(even) { background: #f9f9f9; }
tr:hover { background: #e9ecef; }
.success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
.error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
.info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
.btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
.btn:hover { background: #218838; }
.badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; }
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: #000; }
.badge-danger { background: #dc3545; color: white; }
</style>";
echo "</head><body>";

echo "<h1>📋 Lista Professionisti Disponibili</h1>";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Utente;
use App\Models\Professionista;

try {
    echo "<div class='info'>";
    echo "<h2>Professionisti nel Sistema</h2>";
    echo "<p>Questa lista mostra tutti gli utenti di tipo 'professionista' e i loro dati associati.</p>";
    echo "</div>";

    // Query per ottenere tutti i professionisti
    $professionisti = Utente::with(['ruolo', 'professionista'])
        ->where('tipo_utente', 'professionista')
        ->orderBy('id', 'desc')
        ->get();

    if ($professionisti->count() > 0) {
        echo "<div class='success'>";
        echo "<p><strong>✅ Trovati {$professionisti->count()} professionisti</strong></p>";
        echo "</div>";

        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID Utente</th>";
        echo "<th>Nome</th>";
        echo "<th>Email</th>";
        echo "<th>Ruolo</th>";
        echo "<th>Stato</th>";
        echo "<th>Professionista ID</th>";
        echo "<th>Azioni</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($professionisti as $p) {
            $ruoloNome = $p->ruolo ? $p->ruolo->nome : '<span class="badge badge-danger">Nessun Ruolo</span>';
            $attivo = $p->attivo ? '<span class="badge badge-success">Attivo</span>' : '<span class="badge badge-danger">Non Attivo</span>';
            $professionistaId = $p->professionista ? $p->professionista->id : '<span class="badge badge-warning">N/A</span>';

            echo "<tr>";
            echo "<td><strong>{$p->id}</strong></td>";
            echo "<td>{$p->nome} {$p->cognome}</td>";
            echo "<td>{$p->email}</td>";
            echo "<td>{$ruoloNome}</td>";
            echo "<td>{$attivo}</td>";
            echo "<td>{$professionistaId}</td>";
            echo "<td>";
            echo "<a href='/magia/public/admin/professionisti/{$p->id}/permessi' class='btn' target='_blank'>🔐 Vai ai Permessi</a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

        // Dettagli aggiuntivi per professionisti senza ruolo
        $senzaRuolo = $professionisti->filter(function($p) {
            return $p->ruolo_id === null;
        });

        if ($senzaRuolo->count() > 0) {
            echo "<div class='error'>";
            echo "<h3>⚠️ Professionisti Senza Ruolo</h3>";
            echo "<p><strong>{$senzaRuolo->count()} professionisti</strong> non hanno un ruolo assegnato:</p>";
            echo "<ul>";
            foreach ($senzaRuolo as $p) {
                echo "<li>ID {$p->id}: {$p->nome} {$p->cognome} ({$p->email})</li>";
            }
            echo "</ul>";
            echo "<p>Questi utenti non vedranno i permessi correttamente. È necessario assegnare loro il ruolo 'Collaboratore'.</p>";
            echo "</div>";
        }

    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Nessun Professionista Trovato</h3>";
        echo "<p>Non ci sono utenti con tipo_utente = 'professionista' nel database.</p>";
        echo "</div>";

        // Verifica tutti gli utenti
        $tuttiUtenti = Utente::all();
        echo "<div class='info'>";
        echo "<h3>Tutti gli Utenti nel Database ({$tuttiUtenti->count()})</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th></tr>";
        foreach ($tuttiUtenti as $u) {
            echo "<tr><td>{$u->id}</td><td>{$u->nome} {$u->cognome}</td><td>{$u->email}</td><td>{$u->tipo_utente}</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    }

    // Verifica tabella professionisti
    echo "<div class='info'>";
    echo "<h2>📊 Tabella 'professionisti'</h2>";
    $recordProfessionisti = Professionista::with('utente')->orderBy('id', 'desc')->get();

    if ($recordProfessionisti->count() > 0) {
        echo "<p>Record nella tabella professionisti: <strong>{$recordProfessionisti->count()}</strong></p>";
        echo "<table>";
        echo "<tr><th>ID Prof.</th><th>Utente ID</th><th>Nome Utente</th><th>Codice</th><th>Stato</th></tr>";
        foreach ($recordProfessionisti as $prof) {
            $nomeUtente = $prof->utente ? "{$prof->utente->nome} {$prof->utente->cognome}" : '<span class="badge badge-danger">Utente Non Trovato</span>';
            echo "<tr>";
            echo "<td>{$prof->id}</td>";
            echo "<td>{$prof->utente_id}</td>";
            echo "<td>{$nomeUtente}</td>";
            echo "<td>{$prof->codice_professionista}</td>";
            echo "<td>{$prof->stato}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Nessun record nella tabella professionisti</p>";
    }
    echo "</div>";

    // Verifica ruoli disponibili
    echo "<div class='info'>";
    echo "<h2>🏷️ Ruoli Disponibili</h2>";
    $ruoli = \App\Models\Ruolo::all();

    if ($ruoli->count() > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nome</th><th>Slug</th><th>Livello</th><th>Attivo</th><th>Utenti</th></tr>";
        foreach ($ruoli as $r) {
            $utentiCount = $r->utenti()->count();
            $attivoStatus = $r->attivo ? '<span class="badge badge-success">Sì</span>' : '<span class="badge badge-danger">No</span>';
            echo "<tr>";
            echo "<td>{$r->id}</td>";
            echo "<td>{$r->nome}</td>";
            echo "<td>{$r->slug}</td>";
            echo "<td>{$r->livello}</td>";
            echo "<td>{$attivoStatus}</td>";
            echo "<td>{$utentiCount}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Nessun ruolo trovato nel database</p>";
    }
    echo "</div>";

} catch (\Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Errore</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
