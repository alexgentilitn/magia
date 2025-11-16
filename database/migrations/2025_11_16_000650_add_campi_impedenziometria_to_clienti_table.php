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
            // Dati Impedenziometria completi
            if (!Schema::hasColumn('clienti', 'massa_grassa_percentuale')) {
                $table->decimal('massa_grassa_percentuale', 5, 2)->nullable()->after('massa_magra')->comment('% massa grassa');
            }
            if (!Schema::hasColumn('clienti', 'massa_muscolare_kg')) {
                $table->decimal('massa_muscolare_kg', 5, 2)->nullable()->comment('Kg massa muscolare');
            }
            if (!Schema::hasColumn('clienti', 'massa_ossea_kg')) {
                $table->decimal('massa_ossea_kg', 5, 2)->nullable()->comment('Kg massa ossea');
            }
            if (!Schema::hasColumn('clienti', 'grasso_viscerale')) {
                $table->decimal('grasso_viscerale', 5, 2)->nullable()->comment('Livello grasso viscerale');
            }
            if (!Schema::hasColumn('clienti', 'proteine_percentuale')) {
                $table->decimal('proteine_percentuale', 5, 2)->nullable()->comment('% proteine corporee');
            }
            if (!Schema::hasColumn('clienti', 'acqua_totale_percentuale')) {
                $table->decimal('acqua_totale_percentuale', 5, 2)->nullable()->comment('% acqua corporea totale');
            }
            if (!Schema::hasColumn('clienti', 'acqua_extra_percentuale')) {
                $table->decimal('acqua_extra_percentuale', 5, 2)->nullable()->comment('% acqua extracellulare');
            }
            if (!Schema::hasColumn('clienti', 'acqua_intra_percentuale')) {
                $table->decimal('acqua_intra_percentuale', 5, 2)->nullable()->comment('% acqua intracellulare');
            }

            // Obiettivi cliente
            if (!Schema::hasColumn('clienti', 'obiettivi_primari')) {
                $table->text('obiettivi_primari')->nullable()->comment('Obiettivi principali (es: perdere peso, tonificare)');
            }
            if (!Schema::hasColumn('clienti', 'obiettivi_secondari')) {
                $table->text('obiettivi_secondari')->nullable()->comment('Obiettivi secondari');
            }
            if (!Schema::hasColumn('clienti', 'peso_obiettivo')) {
                $table->decimal('peso_obiettivo', 5, 2)->nullable()->comment('Peso target');
            }
            if (!Schema::hasColumn('clienti', 'data_obiettivo')) {
                $table->date('data_obiettivo')->nullable()->comment('Data prevista raggiungimento obiettivo');
            }

            // NOTA: allergie e intolleranze sono già presenti nella migration precedente come allergie_intolleranze
            // Non le aggiungiamo qui per evitare duplicati

            // Stile di vita
            // NOTA: livello_attivita è già presente nella migration precedente, non lo aggiungiamo
            if (!Schema::hasColumn('clienti', 'ore_sonno')) {
                $table->integer('ore_sonno')->nullable()->comment('Ore medie di sonno per notte');
            }
            if (!Schema::hasColumn('clienti', 'qualita_sonno')) {
                $table->enum('qualita_sonno', ['pessima', 'scarsa', 'sufficiente', 'buona', 'ottima'])->nullable();
            }
            if (!Schema::hasColumn('clienti', 'fumatore')) {
                $table->boolean('fumatore')->default(false);
            }
            if (!Schema::hasColumn('clienti', 'consumo_acqua_litri')) {
                $table->decimal('consumo_acqua_litri', 3, 1)->nullable()->comment('Litri acqua consumati al giorno');
            }

            // Dati medici aggiuntivi
            if (!Schema::hasColumn('clienti', 'patologie_croniche')) {
                $table->text('patologie_croniche')->nullable()->comment('Patologie croniche o condizioni mediche');
            }
            if (!Schema::hasColumn('clienti', 'interventi_chirurgici')) {
                $table->text('interventi_chirurgici')->nullable()->comment('Storico interventi chirurgici');
            }
            if (!Schema::hasColumn('clienti', 'gravidanza_in_corso')) {
                $table->boolean('gravidanza_in_corso')->default(false);
            }
            if (!Schema::hasColumn('clienti', 'ultima_gravidanza')) {
                $table->date('ultima_gravidanza')->nullable();
            }

            // Mestruazioni (specifico per donne)
            if (!Schema::hasColumn('clienti', 'ciclo_regolare')) {
                $table->boolean('ciclo_regolare')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'durata_ciclo_giorni')) {
                $table->integer('durata_ciclo_giorni')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'ultima_mestruazione')) {
                $table->date('ultima_mestruazione')->nullable();
            }

            // Tracking evoluzione
            if (!Schema::hasColumn('clienti', 'ultima_impedenziometria')) {
                $table->timestamp('ultima_impedenziometria')->nullable()->comment('Data ultima misurazione impedenziometria');
            }
            if (!Schema::hasColumn('clienti', 'storico_peso')) {
                $table->json('storico_peso')->nullable()->comment('Array storico variazioni peso');
            }
            if (!Schema::hasColumn('clienti', 'storico_misure')) {
                $table->json('storico_misure')->nullable()->comment('Array storico misurazioni');
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
                'massa_grassa_percentuale',
                'massa_muscolare_kg',
                'massa_ossea_kg',
                'grasso_viscerale',
                'proteine_percentuale',
                'acqua_totale_percentuale',
                'acqua_extra_percentuale',
                'acqua_intra_percentuale',
                'obiettivi_primari',
                'obiettivi_secondari',
                'peso_obiettivo',
                'data_obiettivo',
                'allergie',
                'intolleranze',
                'livello_attivita',
                'ore_sonno',
                'qualita_sonno',
                'fumatore',
                'consumo_acqua_litri',
                'patologie_croniche',
                'interventi_chirurgici',
                'gravidanza_in_corso',
                'ultima_gravidanza',
                'ciclo_regolare',
                'durata_ciclo_giorni',
                'ultima_mestruazione',
                'ultima_impedenziometria',
                'storico_peso',
                'storico_misure',
            ]);
        });
    }
};
