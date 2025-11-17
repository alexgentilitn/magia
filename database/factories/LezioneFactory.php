<?php

namespace Database\Factories;

use App\Models\Lezione;
use App\Models\Professionista;
use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per il modello Lezione
 */
class LezioneFactory extends Factory
{
    protected $model = Lezione::class;

    public function definition(): array
    {
        $dataOra = fake()->dateTimeBetween('now', '+30 days');
        $durata = fake()->randomElement([45, 60, 90]);

        return [
            'titolo' => fake()->randomElement([
                'Pilates Base',
                'Yoga Flow',
                'Balla & Snella',
                'Tonificazione',
                'Stretching',
                'Ginnastica Dolce'
            ]),
            'descrizione' => fake()->paragraph(),
            'tipo' => fake()->randomElement(['pilates', 'yoga', 'ballo', 'tonificazione', 'altro']),
            'data_ora' => $dataOra,
            'durata_minuti' => $durata,
            'capienza' => fake()->numberBetween(8, 20),
            'posti_prenotati' => 0,
            'professionista_id' => null, // Sarà assegnato manualmente
            'sede_id' => null, // Sarà assegnato manualmente
            'stato' => 'programmata',
            'visibile_calendario' => true,
            'note' => fake()->boolean(20) ? fake()->sentence() : null,
        ];
    }

    /**
     * Lezione quasi al completo
     */
    public function quasiCompleta(): static
    {
        return $this->state(function (array $attributes) {
            $capienza = $attributes['capienza'] ?? 15;
            return [
                'posti_prenotati' => $capienza - 2,
            ];
        });
    }

    /**
     * Lezione completa (posti esauriti)
     */
    public function completa(): static
    {
        return $this->state(function (array $attributes) {
            $capienza = $attributes['capienza'] ?? 15;
            return [
                'posti_prenotati' => $capienza,
            ];
        });
    }

    /**
     * Lezione annullata
     */
    public function annullata(): static
    {
        return $this->state(fn (array $attributes) => [
            'stato' => 'annullata',
        ]);
    }

    /**
     * Lezione completata
     */
    public function completata(): static
    {
        return $this->state(fn (array $attributes) => [
            'stato' => 'completata',
            'data_ora' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
