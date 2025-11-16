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
            if (!Schema::hasColumn('clienti', 'privacy_accettata')) {
                $table->boolean('privacy_accettata')->default(false);
            }
            if (!Schema::hasColumn('clienti', 'privacy_accettata_at')) {
                $table->timestamp('privacy_accettata_at')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'termini_accettati')) {
                $table->boolean('termini_accettati')->default(false);
            }
            if (!Schema::hasColumn('clienti', 'termini_accettati_at')) {
                $table->timestamp('termini_accettati_at')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'marketing_accettato')) {
                $table->boolean('marketing_accettato')->default(false)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'marketing_accettato_at')) {
                $table->timestamp('marketing_accettato_at')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'consenso_dati_sensibili')) {
                $table->boolean('consenso_dati_sensibili')->default(false);
            }
            if (!Schema::hasColumn('clienti', 'consenso_dati_sensibili_at')) {
                $table->timestamp('consenso_dati_sensibili_at')->nullable();
            }

            // Tracciabilità
            if (!Schema::hasColumn('clienti', 'ip_registrazione')) {
                $table->string('ip_registrazione', 45)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'note_consensi')) {
                $table->text('note_consensi')->nullable();
            }
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
