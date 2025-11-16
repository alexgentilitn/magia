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
        Schema::create('consensi_privacy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clienti')->onDelete('cascade');
            $table->enum('tipo_consenso', [
                'privacy_policy',
                'trattamento_dati',
                'marketing',
                'profilazione',
                'cookie_tecnici',
                'cookie_analytics',
                'cookie_marketing',
                'terze_parti'
            ])->comment('Tipo di consenso richiesto');
            $table->boolean('consenso_dato')->default(false)->comment('Consenso accordato o negato');
            $table->timestamp('data_consenso')->nullable()->comment('Data e ora consenso');
            $table->timestamp('data_revoca')->nullable()->comment('Data e ora revoca consenso');
            $table->string('ip_address', 45)->nullable()->comment('IP da cui è stato dato il consenso');
            $table->text('user_agent')->nullable()->comment('User agent browser');
            $table->enum('stato', ['attivo', 'revocato', 'modificato', 'scaduto'])->default('attivo');
            $table->text('note')->nullable()->comment('Note aggiuntive o motivazione');
            $table->string('versione_policy', 20)->nullable()->comment('Versione privacy policy accettata');
            $table->timestamps();
            $table->softDeletes();

            // Indici per performance
            $table->index('cliente_id');
            $table->index('tipo_consenso');
            $table->index(['cliente_id', 'tipo_consenso', 'stato']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consensi_privacy');
    }
};
