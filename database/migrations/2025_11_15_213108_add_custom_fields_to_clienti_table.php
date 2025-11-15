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
            // Dati Anagrafici Estesi
            $table->date('data_nascita')->nullable();
            $table->integer('eta')->nullable();
            $table->enum('sesso', ['F', 'M', 'Altro'])->nullable();

            // Obiettivi Personali
            $table->text('obiettivi_personali')->nullable();
            $table->enum('livello_attivita', ['sedentario', 'leggero', 'moderato', 'intenso', 'molto_intenso'])->nullable();

            // Dati Medici
            $table->text('note_mediche')->nullable();
            $table->text('allergie_intolleranze')->nullable();
            $table->text('patologie')->nullable();
            $table->text('farmaci_assunti')->nullable();
            $table->boolean('certificato_medico_presente')->default(false);

            // Alimentazione
            $table->text('preferenze_alimentari')->nullable();
            $table->enum('regime_alimentare', ['onnivoro', 'vegetariano', 'vegano', 'pescetariano', 'altro'])->nullable();
            $table->text('cibi_da_evitare')->nullable();

            // Parametri Corporei
            $table->decimal('peso', 5, 2)->nullable();  // es: 65.50 kg
            $table->decimal('altezza', 5, 2)->nullable();  // es: 165.00 cm
            $table->decimal('bmi', 4, 2)->nullable();  // calcolato automaticamente

            // Circonferenze (in cm)
            $table->decimal('circonferenza_vita', 5, 2)->nullable();
            $table->decimal('circonferenza_fianchi', 5, 2)->nullable();
            $table->decimal('circonferenza_petto', 5, 2)->nullable();
            $table->decimal('circonferenza_braccio_dx', 5, 2)->nullable();
            $table->decimal('circonferenza_braccio_sx', 5, 2)->nullable();
            $table->decimal('circonferenza_coscia_dx', 5, 2)->nullable();
            $table->decimal('circonferenza_coscia_sx', 5, 2)->nullable();

            // Timestamp misurazioni
            $table->timestamp('ultima_pesata')->nullable();
            $table->timestamp('ultima_misurazione')->nullable();

            // Note aggiuntive
            $table->text('note_professionista')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clienti', function (Blueprint $table) {
            $table->dropColumn([
                'data_nascita',
                'eta',
                'sesso',
                'obiettivi_personali',
                'livello_attivita',
                'note_mediche',
                'allergie_intolleranze',
                'patologie',
                'farmaci_assunti',
                'certificato_medico_presente',
                'preferenze_alimentari',
                'regime_alimentare',
                'cibi_da_evitare',
                'peso',
                'altezza',
                'bmi',
                'circonferenza_vita',
                'circonferenza_fianchi',
                'circonferenza_petto',
                'circonferenza_braccio_dx',
                'circonferenza_braccio_sx',
                'circonferenza_coscia_dx',
                'circonferenza_coscia_sx',
                'ultima_pesata',
                'ultima_misurazione',
                'note_professionista'
            ]);
        });
    }
};
