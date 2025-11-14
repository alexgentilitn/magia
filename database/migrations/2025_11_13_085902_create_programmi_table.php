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
        Schema::create('programmi', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descrizione')->nullable();
            $table->string('descrizione_breve', 500)->nullable();

            // Classificazione
            $table->enum('tipologia', ['fitness', 'danza', 'alimentazione', 'wellness', 'personalizzato', 'altro'])->default('fitness');
            $table->json('discipline')->nullable();
            $table->enum('livello', ['principiante', 'intermedio', 'avanzato', 'tutti'])->default('tutti');

            // Durata e frequenza
            $table->integer('durata_giorni')->nullable();
            $table->integer('durata_mesi')->nullable();
            $table->integer('lezioni_totali')->nullable();
            $table->integer('lezioni_settimana')->nullable();
            $table->integer('durata_singola_lezione')->nullable(); // in minuti

            // Prezzi
            $table->decimal('prezzo_base', 10, 2);
            $table->decimal('prezzo_promo', 10, 2)->nullable();
            $table->date('promo_valida_da')->nullable();
            $table->date('promo_valida_a')->nullable();
            $table->boolean('prezzo_su_richiesta')->default(false);

            // Posti
            $table->integer('posti_disponibili')->nullable();
            $table->integer('posti_occupati')->default(0);
            $table->integer('min_partecipanti')->nullable();
            $table->integer('max_partecipanti')->nullable();

            // Relazioni
            $table->foreignId('sede_id')->nullable()->constrained('sedi')->nullOnDelete();
            $table->foreignId('professionista_id')->nullable()->constrained('utenti')->nullOnDelete();

            // Date disponibilità
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->boolean('sempre_disponibile')->default(false);

            // Info aggiuntive
            $table->json('obiettivi')->nullable();
            $table->json('prerequisiti')->nullable();
            $table->json('materiali_inclusi')->nullable();

            // Media
            $table->string('immagine_copertina')->nullable();
            $table->json('galleria_immagini')->nullable();

            // Flags
            $table->boolean('attivo')->default(true);
            $table->boolean('visibile_pubblico')->default(false);
            $table->boolean('in_evidenza')->default(false);
            $table->integer('ordine')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('tipologia');
            $table->index('livello');
            $table->index('attivo');
            $table->index('visibile_pubblico');
            $table->index('in_evidenza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmi');
    }
};
