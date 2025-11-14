<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrazione: Tabella Clienti - Anagrafica Completa
 * 
 * Funzione: Crea la tabella 'clienti' con tutti i campi necessari per gestire
 * l'anagrafica completa delle clienti, inclusi dati personali, contatti,
 * parametri corporei iniziali e campi personalizzati per i programmi
 * 
 * Relazioni:
 * - Appartiene a: utenti (user_id)
 * - Ha molti: prenotazioni, pagamenti, parametri_corporei, documenti
 */
return new class extends Migration
{
    /**
     * Esegue la migrazione (creazione tabella)
     * 
     * Funzione: Crea la tabella clienti con anagrafica completa,
     * dati di contatto, parametri corporei e campi personalizzati
     */
    public function up(): void
    {
        Schema::create('clienti', function (Blueprint $table) {
            // Chiave primaria
            $table->id();
            
            // Relazione con tabella utenti
            $table->foreignId('utente_id')
                  ->constrained('utenti')
                  ->onDelete('cascade')
                  ->comment('Collegamento a tabella utenti');
            
            // === DATI ANAGRAFICI OBBLIGATORI ===
            
            $table->string('nome', 100)->comment('Nome cliente');
            $table->string('cognome', 100)->comment('Cognome cliente');
            $table->string('codice_fiscale', 16)->unique()->comment('Codice fiscale italiano');
            
            // Indirizzo completo
            $table->string('indirizzo', 200)->comment('Via e numero civico');
            $table->string('citta', 100)->comment('Città di residenza');
            $table->string('provincia', 2)->comment('Sigla provincia (es: TN)');
            $table->string('cap', 5)->comment('Codice Avviamento Postale');
            $table->string('nazione', 50)->default('Italia')->comment('Nazione di residenza');
            
            // Contatti
            $table->string('telefono_fisso', 20)->nullable()->comment('Telefono fisso');
            $table->string('telefono_mobile', 20)->comment('Cellulare (obbligatorio)');
            $table->string('email')->unique()->comment('Email principale');
            $table->string('pec', 255)->nullable()->comment('PEC (opzionale)');
            
            // === DATI PERSONALI ===
            
            $table->date('data_nascita')->comment('Data di nascita');
            $table->enum('sesso', ['F', 'M', 'Altro'])->default('F')->comment('Sesso biologico');
            $table->string('luogo_nascita', 100)->nullable()->comment('Città di nascita');
            
            // === PARAMETRI CORPOREI INIZIALI ===
            
            $table->decimal('peso_iniziale', 5, 2)->nullable()->comment('Peso iniziale in kg');
            $table->decimal('altezza', 5, 2)->nullable()->comment('Altezza in cm');
            $table->decimal('circonferenza_vita', 5, 2)->nullable()->comment('Circonferenza vita in cm');
            $table->decimal('circonferenza_fianchi', 5, 2)->nullable()->comment('Circonferenza fianchi in cm');
            $table->decimal('circonferenza_braccia', 5, 2)->nullable()->comment('Circonferenza braccia in cm');
            $table->decimal('circonferenza_cosce', 5, 2)->nullable()->comment('Circonferenza cosce in cm');
            
            // === DATI IMPEDENZIOMETRIA (forniti da Giorgia) ===
            
            $table->decimal('massa_grassa', 5, 2)->nullable()->comment('Percentuale massa grassa');
            $table->decimal('massa_magra', 5, 2)->nullable()->comment('Percentuale massa magra');
            $table->decimal('acqua_corporea', 5, 2)->nullable()->comment('Percentuale acqua corporea');
            $table->decimal('metabolismo_basale', 6, 2)->nullable()->comment('Kcal metabolismo basale');
            
            // === INFORMAZIONI SALUTE E OBIETTIVI ===
            
            $table->text('obiettivi_personali')->nullable()->comment('Obiettivi della cliente (dimagrimento, tonificazione, ecc)');
            $table->text('note_mediche')->nullable()->comment('Patologie, allergie, intolleranze');
            $table->text('preferenze_alimentari')->nullable()->comment('Preferenze e restrizioni alimentari');
            $table->text('farmaci_assunti')->nullable()->comment('Farmaci in uso regolare');
            $table->boolean('certificato_medico')->default(false)->comment('Ha consegnato certificato medico');
            $table->date('certificato_medico_scadenza')->nullable()->comment('Scadenza certificato medico');
            
            // === PROGRAMMI E ISCRIZIONI ===
            
            $table->string('programma_attuale', 100)->nullable()->comment('Programma in corso (Balla&Snella, MA.GIA, ecc)');
            $table->date('data_iscrizione')->nullable()->comment('Data prima iscrizione');
            $table->date('inizio_programma')->nullable()->comment('Data inizio programma attuale');
            $table->date('fine_programma')->nullable()->comment('Data fine programma (se prevista)');
            $table->enum('stato_programma', ['attivo', 'sospeso', 'completato', 'cancellato'])->default('attivo')->comment('Stato programma attuale');
            
            // === CAMPI PERSONALIZZATI PER PROGRAMMI ===
            
            $table->json('dati_personalizzati')->nullable()->comment('Campi extra personalizzabili per programmi specifici (JSON)');
            $table->string('gruppo_whatsapp', 255)->nullable()->comment('Link gruppo WhatsApp associato');
            $table->foreignId('sede_preferita_id')->nullable()
                  ->comment('Sede preferita dalla cliente');
            
            // === CONSENSI E PRIVACY ===
            
            $table->boolean('consenso_privacy')->default(false)->comment('Accettazione privacy policy');
            $table->timestamp('consenso_privacy_data')->nullable()->comment('Data accettazione privacy');
            $table->boolean('consenso_marketing')->default(false)->comment('Consenso comunicazioni marketing');
            $table->boolean('consenso_foto')->default(false)->comment('Consenso uso foto/video');
            
            // === DATI AMMINISTRATIVI ===
            
            $table->text('note_interne')->nullable()->comment('Note riservate admin (non visibili a cliente)');
            $table->enum('stato_cliente', ['attivo', 'sospeso', 'inattivo', 'cancellato'])->default('attivo')->comment('Stato account cliente');
            $table->string('codice_cliente', 20)->unique()->nullable()->comment('Codice univoco cliente (generato automaticamente)');
            
            // === REFERRAL ===
            
            $table->string('codice_referral', 20)->unique()->nullable()->comment('Codice personale per invitare amiche');
            $table->foreignId('invitato_da_cliente_id')->nullable()
                  ->constrained('clienti')
                  ->nullOnDelete()
                  ->comment('ID cliente che l\'ha invitata (referral)');
            
            // Timestamp automatici
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at per soft delete
            
            // === INDICI PER OTTIMIZZAZIONE QUERY ===
            
            $table->index('utente_id');
            $table->index('codice_fiscale');
            $table->index('email');
            $table->index('telefono_mobile');
            $table->index('stato_cliente');
            $table->index('programma_attuale');
            $table->index('data_iscrizione');
            $table->index('codice_cliente');
            $table->index('codice_referral');
            $table->index(['cognome', 'nome']); // Indice composto per ricerche alfabetiche
        });
    }

    /**
     * Annulla la migrazione (eliminazione tabella)
     * 
     * Funzione: Elimina la tabella clienti in caso di rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('clienti');
    }
};
