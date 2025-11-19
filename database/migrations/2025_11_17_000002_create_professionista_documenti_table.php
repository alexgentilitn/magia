<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tabella Documenti Professionista
 *
 * Funzione: Gestisce documenti e allegati del professionista
 * (certificati, attestati, CV, carta identità, ecc.)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionista_documenti', function (Blueprint $table) {
            $table->id();

            // Relazione con professionisti
            $table->foreignId('professionista_id')
                  ->constrained('professionisti')
                  ->onDelete('cascade')
                  ->comment('Collegamento al professionista');

            // Informazioni documento
            $table->string('nome_file')->comment('Nome originale del file');
            $table->string('path')->comment('Path del file nello storage');
            $table->string('tipo_documento')->nullable()->comment('Tipo: certificato, cv, carta_identita, altro');
            $table->string('mime_type')->nullable()->comment('Tipo MIME del file');
            $table->integer('dimensione')->nullable()->comment('Dimensione file in bytes');
            $table->text('descrizione')->nullable()->comment('Descrizione del documento');

            // Scadenza (per certificati/documenti con validità)
            $table->date('data_scadenza')->nullable()->comment('Data scadenza documento');
            $table->boolean('scaduto')->default(false)->comment('Flag se il documento è scaduto');

            // Visibilità e stato
            $table->boolean('verificato')->default(false)->comment('Documento verificato dall\'admin');
            $table->timestamp('verificato_at')->nullable()->comment('Data verifica documento');
            $table->foreignId('verificato_da')->nullable()
                  ->constrained('utenti')
                  ->nullOnDelete()
                  ->comment('Admin che ha verificato');

            $table->timestamps();

            // Indici
            $table->index('professionista_id');
            $table->index('tipo_documento');
            $table->index('data_scadenza');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionista_documenti');
    }
};
