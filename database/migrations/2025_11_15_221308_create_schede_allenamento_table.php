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
        Schema::create('schede_allenamento', function (Blueprint $table) {
            $table->id();

            // Relazioni
            $table->foreignId('cliente_id')->constrained('clienti')->cascadeOnDelete();
            $table->foreignId('professionista_id')->nullable()->constrained('utenti')->nullOnDelete();

            // Info scheda
            $table->string('nome_scheda');
            $table->text('descrizione')->nullable();
            $table->text('obiettivi')->nullable();

            // Periodo validità
            $table->date('data_inizio')->nullable();
            $table->date('data_fine')->nullable();
            $table->integer('durata_settimane')->nullable();

            // Note
            $table->text('note_generali')->nullable();
            $table->text('note_alimentazione')->nullable();
            $table->text('consigli_professionista')->nullable();

            // Stato
            $table->enum('stato', ['bozza', 'attiva', 'completata', 'archiviata'])->default('bozza');

            // Tracking invio email
            $table->boolean('inviata_email')->default(false);
            $table->timestamp('data_invio_email')->nullable();
            $table->string('pdf_path')->nullable(); // Path del PDF generato

            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index('cliente_id');
            $table->index('professionista_id');
            $table->index('stato');
            $table->index('data_inizio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schede_allenamento');
    }
};
