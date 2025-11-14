<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrazione: Creazione Tabella Utenti Base
 * 
 * Funzione: Crea la tabella 'utenti' per gestire l'autenticazione e i dati base
 * degli utenti del sistema (amministratori, professionisti, clienti)
 * 
 * Relazioni:
 * - Ha molti: clienti, professionisti, amministratori
 * - Collegata a: ruoli_permessi tramite ruolo_id
 */
return new class extends Migration
{
    /**
     * Esegue la migrazione (creazione tabella)
     * 
     * Funzione: Crea la tabella utenti con tutti i campi necessari
     * per autenticazione e gestione base utenti
     */
    public function up(): void
    {
        Schema::create('utenti', function (Blueprint $table) {
            // Chiave primaria
            $table->id();
            
            // Dati autenticazione
            $table->string('email')->unique()->comment('Email univoca per login');
            $table->string('password')->comment('Password criptata');
            $table->rememberToken()->comment('Token per "ricordami"');
            
            // Dati personali base
            $table->string('nome', 100)->comment('Nome utente');
            $table->string('cognome', 100)->comment('Cognome utente');
            $table->string('telefono', 20)->nullable()->comment('Telefono contatto');
            
            // Tipo utente e ruolo
            $table->enum('tipo_utente', ['amministratore', 'professionista', 'cliente'])
                  ->comment('Tipologia utente nel sistema');
            $table->foreignId('ruolo_id')->nullable()
                  ->constrained('ruoli')
                  ->nullOnDelete()
                  ->comment('Ruolo assegnato (per permessi)');
            
            // Stato account
            $table->boolean('attivo')->default(true)->comment('Account attivo/disattivato');
            $table->boolean('email_verificata')->default(false)->comment('Email confermata');
            $table->timestamp('email_verificata_il')->nullable()->comment('Data verifica email');
            
            // Tracciamento accessi
            $table->timestamp('ultimo_accesso')->nullable()->comment('Data ultimo login');
            $table->ipAddress('ultimo_ip')->nullable()->comment('IP ultimo accesso');
            
            // Timestamp automatici
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at per soft delete
            
            // Indici per ottimizzazione query
            $table->index('email');
            $table->index('tipo_utente');
            $table->index('attivo');
            $table->index(['email', 'attivo']); // Indice composto per login
        });
    }

    /**
     * Annulla la migrazione (eliminazione tabella)
     * 
     * Funzione: Elimina la tabella utenti in caso di rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('utenti');
    }
};
