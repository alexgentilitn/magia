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
            $table->decimal('massa_grassa_percentuale', 5, 2)->nullable()->after('massa_magra')->comment('% massa grassa');
            $table->decimal('massa_muscolare_kg', 5, 2)->nullable()->comment('Kg massa muscolare');
            $table->decimal('massa_ossea_kg', 5, 2)->nullable()->comment('Kg massa ossea');
            $table->decimal('grasso_viscerale', 5, 2)->nullable()->comment('Livello grasso viscerale');
            $table->decimal('proteine_percentuale', 5, 2)->nullable()->comment('% proteine corporee');
            $table->decimal('acqua_totale_percentuale', 5, 2)->nullable()->comment('% acqua corporea totale');
            $table->decimal('acqua_extra_percentuale', 5, 2)->nullable()->comment('% acqua extracellulare');
            $table->decimal('acqua_intra_percentuale', 5, 2)->nullable()->comment('% acqua intracellulare');

            // Obiettivi cliente
            $table->text('obiettivi_primari')->nullable()->comment('Obiettivi principali (es: perdere peso, tonificare)');
            $table->text('obiettivi_secondari')->nullable()->comment('Obiettivi secondari');
            $table->decimal('peso_obiettivo', 5, 2)->nullable()->comment('Peso target');
            $table->date('data_obiettivo')->nullable()->comment('Data prevista raggiungimento obiettivo');

            // Allergie e intolleranze
            $table->text('allergie')->nullable()->comment('Allergie alimentari o di altro tipo');
            $table->text('intolleranze')->nullable()->comment('Intolleranze alimentari');

            // Stile di vita
            $table->enum('livello_attivita', ['sedentario', 'leggero', 'moderato', 'attivo', 'molto_attivo'])->nullable()->comment('Livello attività fisica generale');
            $table->integer('ore_sonno')->nullable()->comment('Ore medie di sonno per notte');
            $table->enum('qualita_sonno', ['pessima', 'scarsa', 'sufficiente', 'buona', 'ottima'])->nullable();
            $table->boolean('fumatore')->default(false);
            $table->decimal('consumo_acqua_litri', 3, 1)->nullable()->comment('Litri acqua consumati al giorno');

            // Dati medici aggiuntivi
            $table->text('patologie_croniche')->nullable()->comment('Patologie croniche o condizioni mediche');
            $table->text('interventi_chirurgici')->nullable()->comment('Storico interventi chirurgici');
            $table->boolean('gravidanza_in_corso')->default(false);
            $table->date('ultima_gravidanza')->nullable();

            // Mestruazioni (specifico per donne)
            $table->boolean('ciclo_regolare')->nullable();
            $table->integer('durata_ciclo_giorni')->nullable();
            $table->date('ultima_mestruazione')->nullable();

            // Tracking evoluzione
            $table->timestamp('ultima_impedenziometria')->nullable()->comment('Data ultima misurazione impedenziometria');
            $table->json('storico_peso')->nullable()->comment('Array storico variazioni peso');
            $table->json('storico_misure')->nullable()->comment('Array storico misurazioni');
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
