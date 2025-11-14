<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migrazione: Sistema Ruoli e Permessi (RBAC)
 * 
 * Funzione: Crea le tabelle per gestire ruoli e permessi degli utenti
 * Sistema Role-Based Access Control per controllare accessi alle funzionalità
 * 
 * Tabelle create:
 * - ruoli: definisce i ruoli (Super Admin, Moderatore, Collaboratore)
 * - permessi: definisce i singoli permessi disponibili
 * - ruolo_permesso: tabella pivot per relazione many-to-many
 */
return new class extends Migration
{
    /**
     * Esegue la migrazione (creazione tabelle)
     * 
     * Funzione: Crea tabelle ruoli, permessi e la loro relazione
     * Include anche i dati iniziali dei ruoli base
     */
    public function up(): void
    {
        // Tabella RUOLI
        Schema::create('ruoli', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50)->unique()->comment('Nome ruolo (es: Super Admin)');
            $table->string('slug', 50)->unique()->comment('Identificativo univoco (es: super-admin)');
            $table->text('descrizione')->nullable()->comment('Descrizione del ruolo');
            $table->integer('livello')->default(1)->comment('Livello gerarchico (1=più alto)');
            $table->boolean('attivo')->default(true)->comment('Ruolo attivo/disattivato');
            $table->timestamps();
            
            // Indici
            $table->index('slug');
            $table->index('attivo');
        });

        // Tabella PERMESSI
        Schema::create('permessi', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique()->comment('Nome permesso (es: Gestione Clienti)');
            $table->string('slug', 100)->unique()->comment('Identificativo univoco (es: gestione-clienti)');
            $table->string('categoria', 50)->comment('Categoria permesso (es: clienti, programmi)');
            $table->text('descrizione')->nullable()->comment('Descrizione del permesso');
            $table->boolean('attivo')->default(true)->comment('Permesso attivo/disattivato');
            $table->timestamps();
            
            // Indici
            $table->index('slug');
            $table->index('categoria');
            $table->index('attivo');
        });

        // Tabella PIVOT: Ruolo-Permesso (relazione many-to-many)
        Schema::create('ruolo_permesso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruolo_id')
                  ->constrained('ruoli')
                  ->onDelete('cascade')
                  ->comment('ID ruolo');
            $table->foreignId('permesso_id')
                  ->constrained('permessi')
                  ->onDelete('cascade')
                  ->comment('ID permesso');
            $table->timestamps();
            
            // Impedisce duplicati
            $table->unique(['ruolo_id', 'permesso_id']);
            
            // Indici per ottimizzazione query
            $table->index('ruolo_id');
            $table->index('permesso_id');
        });

        // Inserimento RUOLI BASE
        $this->inserisciRuoliBase();
        
        // Inserimento PERMESSI BASE
        $this->inserisciPermessiBase();
    }

    /**
     * Inserisce i ruoli base del sistema
     * 
     * Funzione: Popola la tabella ruoli con i 3 ruoli fondamentali:
     * - Super Admin (livello 1): accesso totale
     * - Moderatore (livello 2): gestione clienti e contenuti
     * - Collaboratore (livello 3): accesso limitato
     */
    private function inserisciRuoliBase(): void
    {
        DB::table('ruoli')->insert([
            [
                'nome' => 'Super Admin',
                'slug' => 'super-admin',
                'descrizione' => 'Accesso completo a tutte le funzionalità del sistema',
                'livello' => 1,
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Moderatore',
                'slug' => 'moderatore',
                'descrizione' => 'Gestione clienti, programmi, corsi e contenuti',
                'livello' => 2,
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Collaboratore',
                'slug' => 'collaboratore',
                'descrizione' => 'Accesso limitato a visualizzazione e supporto clienti',
                'livello' => 3,
                'attivo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Inserisce i permessi base del sistema
     * 
     * Funzione: Popola la tabella permessi con le autorizzazioni fondamentali
     * organizzate per categoria (dashboard, clienti, programmi, ecc.)
     */
    private function inserisciPermessiBase(): void
    {
        DB::table('permessi')->insert([
            // Categoria: Dashboard
            ['nome' => 'Visualizza Dashboard', 'slug' => 'visualizza-dashboard', 'categoria' => 'dashboard', 'descrizione' => 'Accesso alla dashboard amministratore', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Categoria: Clienti
            ['nome' => 'Visualizza Clienti', 'slug' => 'visualizza-clienti', 'categoria' => 'clienti', 'descrizione' => 'Visualizzare elenco clienti', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Crea Clienti', 'slug' => 'crea-clienti', 'categoria' => 'clienti', 'descrizione' => 'Creare nuovi clienti', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Modifica Clienti', 'slug' => 'modifica-clienti', 'categoria' => 'clienti', 'descrizione' => 'Modificare dati clienti esistenti', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Elimina Clienti', 'slug' => 'elimina-clienti', 'categoria' => 'clienti', 'descrizione' => 'Eliminare clienti dal sistema', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Categoria: Programmi
            ['nome' => 'Gestione Programmi', 'slug' => 'gestione-programmi', 'categoria' => 'programmi', 'descrizione' => 'Creare e modificare programmi', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Categoria: Corsi
            ['nome' => 'Gestione Corsi', 'slug' => 'gestione-corsi', 'categoria' => 'corsi', 'descrizione' => 'Creare e modificare corsi/lezioni', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Categoria: Pagamenti
            ['nome' => 'Visualizza Pagamenti', 'slug' => 'visualizza-pagamenti', 'categoria' => 'pagamenti', 'descrizione' => 'Visualizzare storico pagamenti', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Gestione Pagamenti', 'slug' => 'gestione-pagamenti', 'categoria' => 'pagamenti', 'descrizione' => 'Registrare e modificare pagamenti', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Categoria: Sedi
            ['nome' => 'Gestione Sedi', 'slug' => 'gestione-sedi', 'categoria' => 'sedi', 'descrizione' => 'Gestire le 5 sedi operative', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            
            // Categoria: Sistema
            ['nome' => 'Gestione Utenti', 'slug' => 'gestione-utenti', 'categoria' => 'sistema', 'descrizione' => 'Creare e modificare utenti amministratori', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Gestione Ruoli', 'slug' => 'gestione-ruoli', 'categoria' => 'sistema', 'descrizione' => 'Modificare ruoli e permessi', 'attivo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Annulla la migrazione (eliminazione tabelle)
     * 
     * Funzione: Elimina le tabelle in ordine inverso per rispettare
     * le foreign key constraints
     */
    public function down(): void
    {
        Schema::dropIfExists('ruolo_permesso');
        Schema::dropIfExists('permessi');
        Schema::dropIfExists('ruoli');
    }
};
