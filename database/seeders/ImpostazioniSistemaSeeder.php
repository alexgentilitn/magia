<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImpostazioniSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $impostazioni = [
            // TIPOLOGIE LEZIONE
            [
                'categoria' => 'tipologia_lezione',
                'chiave' => 'individuale',
                'etichetta' => 'Individuale',
                'descrizione' => 'Lezione one-to-one con un solo cliente',
                'colore' => '#e91e63',
                'icona' => 'fa-user',
                'ordinamento' => 1,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'tipologia_lezione',
                'chiave' => 'gruppo',
                'etichetta' => 'Gruppo',
                'descrizione' => 'Lezione con più partecipanti',
                'colore' => '#9c27b0',
                'icona' => 'fa-users',
                'ordinamento' => 2,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'tipologia_lezione',
                'chiave' => 'online',
                'etichetta' => 'Online',
                'descrizione' => 'Lezione svolta completamente online',
                'colore' => '#2196f3',
                'icona' => 'fa-video',
                'ordinamento' => 3,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'tipologia_lezione',
                'chiave' => 'ibrida',
                'etichetta' => 'Ibrida',
                'descrizione' => 'Lezione in presenza + online contemporaneamente',
                'colore' => '#ff9800',
                'icona' => 'fa-globe',
                'ordinamento' => 4,
                'attivo' => true,
                'di_sistema' => true,
            ],

            // STATI LEZIONE
            [
                'categoria' => 'stato_lezione',
                'chiave' => 'programmata',
                'etichetta' => 'Programmata',
                'descrizione' => 'Lezione programmata ma non ancora confermata',
                'colore' => '#ffa726',
                'icona' => 'fa-calendar',
                'ordinamento' => 1,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'stato_lezione',
                'chiave' => 'confermata',
                'etichetta' => 'Confermata',
                'descrizione' => 'Lezione confermata e pronta per lo svolgimento',
                'colore' => '#66bb6a',
                'icona' => 'fa-check-circle',
                'ordinamento' => 2,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'stato_lezione',
                'chiave' => 'in_corso',
                'etichetta' => 'In Corso',
                'descrizione' => 'Lezione attualmente in svolgimento',
                'colore' => '#42a5f5',
                'icona' => 'fa-play-circle',
                'ordinamento' => 3,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'stato_lezione',
                'chiave' => 'completata',
                'etichetta' => 'Completata',
                'descrizione' => 'Lezione terminata con successo',
                'colore' => '#26a69a',
                'icona' => 'fa-flag-checkered',
                'ordinamento' => 4,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'stato_lezione',
                'chiave' => 'cancellata',
                'etichetta' => 'Cancellata',
                'descrizione' => 'Lezione cancellata',
                'colore' => '#ef5350',
                'icona' => 'fa-times-circle',
                'ordinamento' => 5,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'stato_lezione',
                'chiave' => 'rinviata',
                'etichetta' => 'Rinviata',
                'descrizione' => 'Lezione rimandata ad altra data',
                'colore' => '#ff9800',
                'icona' => 'fa-clock',
                'ordinamento' => 6,
                'attivo' => true,
                'di_sistema' => true,
            ],

            // FREQUENZE RICORRENZA
            [
                'categoria' => 'frequenza_ricorrenza',
                'chiave' => 'giornaliera',
                'etichetta' => 'Giornaliera',
                'descrizione' => 'Ripete ogni giorno',
                'colore' => '#4caf50',
                'icona' => 'fa-calendar-day',
                'ordinamento' => 1,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'frequenza_ricorrenza',
                'chiave' => 'settimanale',
                'etichetta' => 'Settimanale',
                'descrizione' => 'Ripete ogni settimana',
                'colore' => '#2196f3',
                'icona' => 'fa-calendar-week',
                'ordinamento' => 2,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'frequenza_ricorrenza',
                'chiave' => 'bisettimanale',
                'etichetta' => 'Bisettimanale',
                'descrizione' => 'Ripete ogni due settimane',
                'colore' => '#9c27b0',
                'icona' => 'fa-calendar-alt',
                'ordinamento' => 3,
                'attivo' => true,
                'di_sistema' => true,
            ],
            [
                'categoria' => 'frequenza_ricorrenza',
                'chiave' => 'mensile',
                'etichetta' => 'Mensile',
                'descrizione' => 'Ripete ogni mese',
                'colore' => '#ff9800',
                'icona' => 'fa-calendar',
                'ordinamento' => 4,
                'attivo' => true,
                'di_sistema' => true,
            ],
        ];

        foreach ($impostazioni as $imp) {
            DB::table('impostazioni_sistema')->insert(array_merge($imp, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
