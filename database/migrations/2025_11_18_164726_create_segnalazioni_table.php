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
        Schema::create('segnalazioni', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utente_id')->comment('ID dell\'utente che ha creato la segnalazione');
            $table->string('titolo', 255)->comment('Titolo della segnalazione');
            $table->text('descrizione')->comment('Descrizione dettagliata del problema o suggerimento');
            $table->enum('tipo', ['bug', 'suggerimento', 'altro'])->default('bug')->comment('Tipo di segnalazione');
            $table->enum('priorita', ['bassa', 'media', 'alta'])->default('media')->comment('Priorità della segnalazione');
            $table->enum('stato', ['aperta', 'in_lavorazione', 'risolta'])->default('aperta')->comment('Stato attuale');
            $table->text('risposta')->nullable()->comment('Risposta/soluzione del Super Admin');
            $table->string('email_notifica', 255)->nullable()->comment('Email per ricevere notifiche di cambio stato');
            $table->unsignedBigInteger('risolto_da')->nullable()->comment('ID utente che ha risolto');
            $table->timestamp('risolto_il')->nullable()->comment('Data e ora risoluzione');
            $table->timestamps();

            // Indexes
            $table->index('utente_id', 'idx_segnalazioni_utente');
            $table->index('stato', 'idx_segnalazioni_stato');
            $table->index('tipo', 'idx_segnalazioni_tipo');
            $table->index('created_at', 'idx_segnalazioni_created');

            // Foreign keys
            $table->foreign('utente_id', 'fk_segnalazioni_utente')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('risolto_da', 'fk_segnalazioni_risolto_da')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segnalazioni');
    }
};
