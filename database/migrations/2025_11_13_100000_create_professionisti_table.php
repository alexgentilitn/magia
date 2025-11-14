<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tabella Professionisti - Dettagli Completi
 *
 * Funzione: Crea la tabella 'professionisti' con tutti i campi per gestire
 * professionisti, istruttori, personal trainer e nutrizionisti
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionisti', function (Blueprint $table) {
            $table->id();

            // Relazione con utenti
            $table->foreignId('utente_id')
                  ->constrained('utenti')
                  ->onDelete('cascade')
                  ->comment('Collegamento a tabella utenti');

            // === DATI ANAGRAFICI ===
            $table->string('nome', 100)->comment('Nome professionista');
            $table->string('cognome', 100)->comment('Cognome professionista');
            $table->string('codice_fiscale', 16)->unique()->nullable();
            $table->date('data_nascita')->nullable();
            $table->enum('sesso', ['F', 'M', 'Altro'])->nullable();

            // === DATI PROFESSIONALI ===
            $table->string('titolo_professionale', 100)->nullable()->comment('Es: Personal Trainer, Nutrizionista');
            $table->json('specializzazioni')->nullable()->comment('Discipline insegnate (fitness, danza, yoga, ecc)');
            $table->text('bio')->nullable()->comment('Descrizione professionale');
            $table->integer('anni_esperienza')->nullable()->comment('Anni di esperienza');
            $table->string('partita_iva', 20)->nullable()->comment('P.IVA se libero professionista');

            // === CERTIFICAZIONI E QUALIFICHE ===
            $table->json('certificazioni')->nullable()->comment('Array certificazioni [{nome, ente, data, scadenza}]');
            $table->json('qualifiche')->nullable()->comment('Titoli di studio e qualifiche professionali');

            // === CONTATTI ===
            $table->string('telefono_mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('indirizzo', 200)->nullable();
            $table->string('citta', 100)->nullable();
            $table->string('provincia', 2)->nullable();
            $table->string('cap', 5)->nullable();

            // === TARIFFE ===
            $table->decimal('tariffa_oraria', 8, 2)->nullable()->comment('Tariffa oraria in euro');
            $table->decimal('tariffa_lezione_gruppo', 8, 2)->nullable()->comment('Tariffa lezione di gruppo');
            $table->decimal('tariffa_lezione_privata', 8, 2)->nullable()->comment('Tariffa lezione privata');

            // === DISPONIBILITÀ ===
            $table->json('disponibilita_settimanale')->nullable()->comment('Orari disponibili per giorno');
            $table->date('disponibile_da')->nullable()->comment('Data inizio disponibilità');
            $table->date('disponibile_fino')->nullable()->comment('Data fine contratto/disponibilità');

            // === MEDIA ===
            $table->string('foto_profilo')->nullable()->comment('Path foto profilo');
            $table->json('galleria_foto')->nullable()->comment('Galleria foto professionista');
            $table->string('video_presentazione')->nullable()->comment('URL video presentazione');

            // === SOCIAL E WEB ===
            $table->string('sito_web')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('tiktok')->nullable();

            // === LINGUE ===
            $table->json('lingue_parlate')->nullable()->comment('Array lingue parlate');

            // === DATI AMMINISTRATIVI ===
            $table->enum('tipo_contratto', ['dipendente', 'collaboratore', 'freelance', 'altro'])->nullable();
            $table->date('data_assunzione')->nullable();
            $table->text('note_interne')->nullable()->comment('Note riservate admin');
            $table->boolean('visibile_pubblico')->default(false)->comment('Visibile sul sito pubblico');
            $table->enum('stato', ['attivo', 'sospeso', 'inattivo'])->default('attivo');
            $table->string('codice_professionista', 20)->unique()->nullable();

            // === PERFORMANCE ===
            $table->decimal('valutazione_media', 3, 2)->nullable()->comment('Media recensioni (0-5)');
            $table->integer('numero_recensioni')->default(0);
            $table->integer('lezioni_completate')->default(0);
            $table->integer('clienti_seguiti')->default(0);

            // Timestamp
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('utente_id');
            $table->index('stato');
            $table->index('visibile_pubblico');
            $table->index(['cognome', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionisti');
    }
};
