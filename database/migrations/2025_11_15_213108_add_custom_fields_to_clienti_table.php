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
            if (!Schema::hasColumn('clienti', 'data_nascita')) {
                $table->date('data_nascita')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'eta')) {
                $table->integer('eta')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'sesso')) {
                $table->enum('sesso', ['F', 'M', 'Altro'])->nullable();
            }

            // Obiettivi Personali
            if (!Schema::hasColumn('clienti', 'obiettivi_personali')) {
                $table->text('obiettivi_personali')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'livello_attivita')) {
                $table->enum('livello_attivita', ['sedentario', 'leggero', 'moderato', 'intenso', 'molto_intenso'])->nullable();
            }

            // Dati Medici
            if (!Schema::hasColumn('clienti', 'note_mediche')) {
                $table->text('note_mediche')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'allergie_intolleranze')) {
                $table->text('allergie_intolleranze')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'patologie')) {
                $table->text('patologie')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'farmaci_assunti')) {
                $table->text('farmaci_assunti')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'certificato_medico_presente')) {
                $table->boolean('certificato_medico_presente')->default(false);
            }

            // Alimentazione
            if (!Schema::hasColumn('clienti', 'preferenze_alimentari')) {
                $table->text('preferenze_alimentari')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'regime_alimentare')) {
                $table->enum('regime_alimentare', ['onnivoro', 'vegetariano', 'vegano', 'pescetariano', 'altro'])->nullable();
            }
            if (!Schema::hasColumn('clienti', 'cibi_da_evitare')) {
                $table->text('cibi_da_evitare')->nullable();
            }

            // Parametri Corporei
            if (!Schema::hasColumn('clienti', 'peso')) {
                $table->decimal('peso', 5, 2)->nullable();  // es: 65.50 kg
            }
            if (!Schema::hasColumn('clienti', 'altezza')) {
                $table->decimal('altezza', 5, 2)->nullable();  // es: 165.00 cm
            }
            if (!Schema::hasColumn('clienti', 'bmi')) {
                $table->decimal('bmi', 4, 2)->nullable();  // calcolato automaticamente
            }

            // Circonferenze (in cm)
            if (!Schema::hasColumn('clienti', 'circonferenza_vita')) {
                $table->decimal('circonferenza_vita', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'circonferenza_fianchi')) {
                $table->decimal('circonferenza_fianchi', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'circonferenza_petto')) {
                $table->decimal('circonferenza_petto', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'circonferenza_braccio_dx')) {
                $table->decimal('circonferenza_braccio_dx', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'circonferenza_braccio_sx')) {
                $table->decimal('circonferenza_braccio_sx', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'circonferenza_coscia_dx')) {
                $table->decimal('circonferenza_coscia_dx', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('clienti', 'circonferenza_coscia_sx')) {
                $table->decimal('circonferenza_coscia_sx', 5, 2)->nullable();
            }

            // Timestamp misurazioni
            if (!Schema::hasColumn('clienti', 'ultima_pesata')) {
                $table->timestamp('ultima_pesata')->nullable();
            }
            if (!Schema::hasColumn('clienti', 'ultima_misurazione')) {
                $table->timestamp('ultima_misurazione')->nullable();
            }

            // Note aggiuntive
            if (!Schema::hasColumn('clienti', 'note_professionista')) {
                $table->text('note_professionista')->nullable();
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
