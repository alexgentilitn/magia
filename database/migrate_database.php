<?php

/**
 * Script: Migrazione Intelligente Database
 * Funzione: Analizza il database esistente e aggiunge tabelle/campi mancanti
 *
 * UTILIZZO:
 * php database/migrate_database.php
 *
 * Lo script:
 * 1. Verifica quali tabelle esistono
 * 2. Crea le tabelle mancanti
 * 3. Aggiunge i campi mancanti alle tabelle esistenti
 * 4. Non elimina dati esistenti
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   MIGRAZIONE INTELLIGENTE DATABASE - MA.GIA DONNA         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$tabelle_create = 0;
$campi_aggiunti = 0;
$errori = 0;

try {
    // ========================================
    // TABELLA: documenti
    // ========================================
    echo "📋 Verifico tabella 'documenti'...\n";
    if (!Schema::hasTable('documenti')) {
        echo "   ➜ Creazione tabella 'documenti'...\n";
        Schema::create('documenti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('caricato_da_utente_id')->nullable();
            $table->string('tipo_documento', 50);
            $table->string('nome_file');
            $table->string('nome_originale');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->integer('dimensione')->unsigned();
            $table->text('descrizione')->nullable();
            $table->date('data_scadenza')->nullable();
            $table->string('stato', 30)->default('valido');
            $table->text('note')->nullable();
            $table->boolean('visibile_cliente')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('cliente_id');
            $table->index('tipo_documento');
            $table->index('data_scadenza');
        });
        $tabelle_create++;
        echo "   ✅ Tabella 'documenti' creata\n\n";
    } else {
        echo "   ✓ Tabella 'documenti' già esistente\n\n";
    }

    // ========================================
    // TABELLA: template_email
    // ========================================
    echo "📋 Verifico tabella 'template_email'...\n";
    if (!Schema::hasTable('template_email')) {
        echo "   ➜ Creazione tabella 'template_email'...\n";
        Schema::create('template_email', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50)->unique();
            $table->string('nome');
            $table->string('oggetto');
            $table->text('corpo_html');
            $table->text('corpo_text')->nullable();
            $table->json('variabili_disponibili')->nullable();
            $table->string('mittente_nome')->nullable();
            $table->string('mittente_email')->nullable();
            $table->boolean('attivo')->default(true);
            $table->text('note')->nullable();
            $table->boolean('invia_automaticamente')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipo');
            $table->index('attivo');
        });
        $tabelle_create++;
        echo "   ✅ Tabella 'template_email' creata\n\n";
    } else {
        echo "   ✓ Tabella 'template_email' già esistente\n\n";
    }

    // ========================================
    // TABELLA: ricette
    // ========================================
    echo "📋 Verifico tabella 'ricette'...\n";
    if (!Schema::hasTable('ricette')) {
        echo "   ➜ Creazione tabella 'ricette'...\n";
        Schema::create('ricette', function (Blueprint $table) {
            $table->id();
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->text('ingredienti');
            $table->text('procedimento');
            $table->integer('tempo_preparazione')->nullable()->comment('minuti');
            $table->enum('difficolta', ['facile', 'media', 'difficile'])->default('facile');
            $table->integer('calorie')->nullable();
            $table->string('categoria', 50)->nullable();
            $table->string('immagine')->nullable();
            $table->text('note')->nullable();
            $table->integer('ordine_pubblicazione')->default(0);
            $table->date('data_pubblicazione')->nullable();
            $table->boolean('pubblicata')->default(false);
            $table->json('visibile_programmi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('pubblicata');
            $table->index('categoria');
            $table->index('ordine_pubblicazione');
            $table->index('data_pubblicazione');
        });
        $tabelle_create++;
        echo "   ✅ Tabella 'ricette' creata\n\n";
    } else {
        echo "   ✓ Tabella 'ricette' già esistente\n\n";
    }

    // ========================================
    // TABELLA: programma_ricetta (pivot)
    // ========================================
    echo "📋 Verifico tabella 'programma_ricetta'...\n";
    if (!Schema::hasTable('programma_ricetta')) {
        echo "   ➜ Creazione tabella 'programma_ricetta'...\n";
        Schema::create('programma_ricetta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programma_id');
            $table->unsignedBigInteger('ricetta_id');
            $table->timestamps();

            $table->index('programma_id');
            $table->index('ricetta_id');
        });
        $tabelle_create++;
        echo "   ✅ Tabella 'programma_ricetta' creata\n\n";
    } else {
        echo "   ✓ Tabella 'programma_ricetta' già esistente\n\n";
    }

    // ========================================
    // TABELLA: referrals
    // ========================================
    echo "📋 Verifico tabella 'referrals'...\n";
    if (!Schema::hasTable('referrals')) {
        echo "   ➜ Creazione tabella 'referrals'...\n";
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id'); // Chi invita
            $table->unsignedBigInteger('cliente_invitato_id')->nullable(); // Chi è stato invitato
            $table->string('codice_referral', 20);
            $table->enum('stato', ['in_attesa', 'iscritto', 'attivo', 'sconto_applicato', 'annullato'])->default('in_attesa');
            $table->datetime('data_invito')->nullable();
            $table->datetime('data_iscrizione')->nullable();
            $table->datetime('data_attivazione')->nullable();
            $table->enum('sconto_tipo', ['percentuale', 'fisso', 'lezione_gratis', 'upgrade'])->nullable();
            $table->decimal('sconto_valore', 8, 2)->nullable();
            $table->boolean('sconto_applicato')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('cliente_id');
            $table->index('cliente_invitato_id');
            $table->index('codice_referral');
            $table->index('stato');
        });
        $tabelle_create++;
        echo "   ✅ Tabella 'referrals' creata\n\n";
    } else {
        echo "   ✓ Tabella 'referrals' già esistente\n\n";
    }

    // ========================================
    // TABELLA: analytics
    // ========================================
    echo "📋 Verifico tabella 'analytics'...\n";
    if (!Schema::hasTable('analytics')) {
        echo "   ➜ Creazione tabella 'analytics'...\n";
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utente_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('tipo_evento', 50);
            $table->string('pagina')->nullable();
            $table->string('azione')->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('dispositivo', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->integer('tempo_permanenza')->nullable()->comment('secondi');
            $table->text('referrer')->nullable();
            $table->json('dati_extra')->nullable();
            $table->timestamps();

            $table->index('tipo_evento');
            $table->index('utente_id');
            $table->index('cliente_id');
            $table->index('created_at');
            $table->index('dispositivo');
        });
        $tabelle_create++;
        echo "   ✅ Tabella 'analytics' creata\n\n";
    } else {
        echo "   ✓ Tabella 'analytics' già esistente\n\n";
    }

    // ========================================
    // CAMPI AGGIUNTIVI TABELLA: clienti
    // ========================================
    echo "📋 Verifico campi tabella 'clienti'...\n";

    $campi_clienti_necessari = [
        'peso_iniziale' => ['type' => 'decimal', 'precision' => [8, 2]],
        'altezza' => ['type' => 'decimal', 'precision' => [5, 2]],
        'circonferenza_vita' => ['type' => 'decimal', 'precision' => [5, 2]],
        'circonferenza_fianchi' => ['type' => 'decimal', 'precision' => [5, 2]],
        'circonferenza_braccia' => ['type' => 'decimal', 'precision' => [5, 2]],
        'circonferenza_cosce' => ['type' => 'decimal', 'precision' => [5, 2]],
        'massa_grassa' => ['type' => 'decimal', 'precision' => [5, 2]],
        'massa_magra' => ['type' => 'decimal', 'precision' => [5, 2]],
        'acqua_corporea' => ['type' => 'decimal', 'precision' => [5, 2]],
        'metabolismo_basale' => ['type' => 'decimal', 'precision' => [6, 2]],
        'obiettivi_personali' => ['type' => 'text'],
        'note_mediche' => ['type' => 'text'],
        'preferenze_alimentari' => ['type' => 'text'],
        'farmaci_assunti' => ['type' => 'text'],
        'codice_referral' => ['type' => 'string', 'length' => 20],
        'invitato_da_cliente_id' => ['type' => 'unsignedBigInteger'],
    ];

    if (Schema::hasTable('clienti')) {
        foreach ($campi_clienti_necessari as $campo => $config) {
            if (!Schema::hasColumn('clienti', $campo)) {
                echo "   ➜ Aggiunta campo '$campo' a 'clienti'...\n";
                Schema::table('clienti', function (Blueprint $table) use ($campo, $config) {
                    switch ($config['type']) {
                        case 'decimal':
                            $table->decimal($campo, $config['precision'][0], $config['precision'][1])->nullable();
                            break;
                        case 'text':
                            $table->text($campo)->nullable();
                            break;
                        case 'string':
                            $table->string($campo, $config['length'] ?? 255)->nullable();
                            break;
                        case 'unsignedBigInteger':
                            $table->unsignedBigInteger($campo)->nullable();
                            break;
                    }
                });
                $campi_aggiunti++;
            }
        }
        echo "   ✓ Tutti i campi necessari presenti in 'clienti'\n\n";
    }

    // ========================================
    // RIEPILOGO
    // ========================================
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║                    MIGRAZIONE COMPLETATA                  ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "📊 Riepilogo:\n";
    echo "   ✅ Tabelle create: $tabelle_create\n";
    echo "   ✅ Campi aggiunti: $campi_aggiunti\n";
    echo "   ❌ Errori: $errori\n\n";

    if ($tabelle_create === 0 && $campi_aggiunti === 0) {
        echo "✓ Il database è già aggiornato. Nessuna modifica necessaria.\n\n";
    } else {
        echo "✅ Database aggiornato con successo!\n\n";
    }

    echo "🚀 Puoi ora utilizzare tutte le nuove funzionalità:\n";
    echo "   • Sistema upload documenti\n";
    echo "   • Template email personalizzabili\n";
    echo "   • Ricette con distribuzione automatica\n";
    echo "   • Programma referral \"Porta un Amico\"\n";
    echo "   • Analytics comportamentali utenti\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERRORE durante la migrazione:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    $errori++;
    exit(1);
}

exit(0);
