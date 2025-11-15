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
        Schema::table('clienti', function (Blueprint $table) {
            // Consensi Privacy GDPR
            $table->boolean('privacy_accettata')->default(false);
            $table->timestamp('privacy_accettata_at')->nullable();

            $table->boolean('termini_accettati')->default(false);
            $table->timestamp('termini_accettati_at')->nullable();

            $table->boolean('marketing_accettato')->default(false)->nullable();
            $table->timestamp('marketing_accettato_at')->nullable();

            $table->boolean('consenso_dati_sensibili')->default(false);
            $table->timestamp('consenso_dati_sensibili_at')->nullable();

            // Tracciabilità
            $table->string('ip_registrazione', 45)->nullable();
            $table->text('note_consensi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            $table->dropColumn([
                'privacy_accettata',
                'privacy_accettata_at',
                'termini_accettati',
                'termini_accettati_at',
                'marketing_accettato',
                'marketing_accettato_at',
                'consenso_dati_sensibili',
                'consenso_dati_sensibili_at',
                'ip_registrazione',
                'note_consensi'
            ]);
        });
    }
};
