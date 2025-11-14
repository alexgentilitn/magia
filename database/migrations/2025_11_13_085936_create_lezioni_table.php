<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lezioni', function (Blueprint $table) {
            $table->id();
            $table->string('titolo');
            $table->text('descrizione')->nullable();

            // Tipologia e classificazione
            $table->enum('tipologia', ['individuale', 'gruppo', 'online', 'ibrida'])->default('gruppo');
            $table->string('disciplina')->nullable();
            $table->enum('livello', ['principiante', 'intermedio', 'avanzato', 'tutti'])->default('tutti');

            // Data e orari
            $table->date('data');
            $table->time('ora_inizio');
            $table->time('ora_fine');
            $table->integer('durata_minuti')->nullable();

            // Posti
            $table->integer('posti_totali');
            $table->integer('posti_occupati')->default(0);
            $table->integer('posti_lista_attesa')->default(0);

            // Relazioni
            $table->foreignId('programma_id')->nullable()->constrained('programmi')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedi')->nullOnDelete();
            $table->foreignId('professionista_id')->nullable()->constrained('utenti')->nullOnDelete();

            // Stato e gestione
            $table->enum('stato', ['programmata', 'confermata', 'in_corso', 'completata', 'cancellata', 'rinviata'])->default('programmata');
            $table->text('note_interne')->nullable();
            $table->text('note_pubbliche')->nullable();

            // Ricorrenza
            $table->boolean('ricorrente')->default(false);
            $table->foreignId('lezione_padre_id')->nullable()->constrained('lezioni')->nullOnDelete();
            $table->enum('frequenza_ricorrenza', ['giornaliera', 'settimanale', 'bisettimanale', 'mensile'])->nullable();
            $table->date('fine_ricorrenza')->nullable();

            // Prenotazioni
            $table->boolean('prenotazione_obbligatoria')->default(true);
            $table->integer('max_cancellazioni_ore')->nullable(); // ore prima della lezione
            $table->boolean('lista_attesa_attiva')->default(false);

            // Materiale e requisiti
            $table->json('materiale_necessario')->nullable();
            $table->string('link_online')->nullable();
            $table->string('password_online')->nullable();

            // Flags
            $table->boolean('visibile_calendario')->default(true);
            $table->boolean('invia_reminder')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('data');
            $table->index('stato');
            $table->index(['data', 'ora_inizio']);
            $table->index('tipologia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lezioni');
    }
};
