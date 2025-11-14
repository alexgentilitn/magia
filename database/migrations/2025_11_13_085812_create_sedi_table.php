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
        Schema::create('sedi', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descrizione')->nullable();

            // Indirizzo
            $table->string('indirizzo_via')->nullable();
            $table->string('indirizzo_citta')->nullable();
            $table->string('indirizzo_provincia', 2)->nullable();
            $table->string('indirizzo_cap', 5)->nullable();
            $table->decimal('latitudine', 10, 8)->nullable();
            $table->decimal('longitudine', 11, 8)->nullable();

            // Contatti
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();

            // Info struttura
            $table->json('orari_apertura')->nullable();
            $table->text('note_accesso')->nullable();
            $table->json('tipologie_corsi')->nullable();
            $table->integer('capienza_massima')->nullable();

            // Media
            $table->string('immagine_copertina')->nullable();
            $table->json('galleria_immagini')->nullable();

            // Flags
            $table->boolean('attiva')->default(true);
            $table->boolean('visibile_pubblico')->default(false);
            $table->boolean('sede_principale')->default(false);
            $table->integer('ordine')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('attiva');
            $table->index('visibile_pubblico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedi');
    }
};
