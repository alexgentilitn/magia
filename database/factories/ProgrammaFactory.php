<?php

namespace Database\Factories;

use App\Models\Programma;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgrammaFactory extends Factory
{
    protected $model = Programma::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->randomElement([
                'Balla & Snella',
                'Alimentazione Smart',
                'Pelle & Benessere',
                'Wellness Completo'
            ]),
            'descrizione' => fake()->paragraph(),
            'tipo' => fake()->randomElement(['balla_snella', 'alimentazione', 'pelle_benessere', 'wellness']),
            'durata_mesi' => fake()->randomElement([1, 3, 6, 12]),
            'prezzo' => fake()->randomFloat(2, 50, 500),
            'attivo' => true,
            'visibile_cliente' => true,
            'numero_lezioni' => fake()->numberBetween(8, 48),
            'benefici' => fake()->paragraph(),
        ];
    }

    public function inattivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'attivo' => false,
        ]);
    }
}
