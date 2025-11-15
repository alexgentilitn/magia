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
        Schema::create('scheda_esercizi', function (Blueprint $table) {
            $table->id();

            // Relazione con scheda
            $table->foreignId('scheda_allenamento_id')->constrained('schede_allenamento')->cascadeOnDelete();

            // Organizzazione
            $table->enum('giorno_settimana', ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica', 'Riposo']);
            $table->integer('ordine')->default(0); // Per ordinare gli esercizi nel giorno

            // Info esercizio
            $table->string('nome_esercizio');
            $table->text('descrizione')->nullable();
            $table->text('istruzioni_esecuzione')->nullable();

            // Parametri allenamento
            $table->integer('serie')->nullable();
            $table->string('ripetizioni')->nullable(); // String perché può essere "12-15", "30 secondi", "AMRAP", etc.
            $table->integer('recupero_secondi')->nullable(); // Tempo recupero tra le serie
            $table->string('peso_suggerito')->nullable(); // "10kg", "15% 1RM", "Corpo libero", etc.
            $table->integer('durata_minuti')->nullable(); // Per esercizi cardio
            $table->string('intensita')->nullable(); // "Bassa", "Media", "Alta"

            // Note aggiuntive
            $table->text('note_esecuzione')->nullable();
            $table->text('varianti')->nullable();

            // Media (opzionale)
            $table->string('video_url')->nullable();
            $table->string('immagine_url')->nullable();

            // Categorie
            $table->enum('categoria', ['forza', 'cardio', 'stretching', 'mobilità', 'altro'])->default('forza');
            $table->json('muscoli_target')->nullable(); // Array di muscoli coinvolti

            $table->timestamps();

            // Indici
            $table->index('scheda_allenamento_id');
            $table->index('giorno_settimana');
            $table->index('ordine');
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheda_esercizi');
    }
};
