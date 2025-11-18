<?php

namespace Database\Factories;

use App\Models\Professionista;
use App\Models\Utente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionistaFactory extends Factory
{
    protected $model = Professionista::class;

    public function definition(): array
    {
        return [
            'utente_id' => null, // Sarà assegnato manualmente
            'nome' => fake()->firstName(),
            'cognome' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->phoneNumber(),
            'specializzazione' => fake()->randomElement([
                'Pilates',
                'Yoga',
                'Personal Trainer',
                'Nutrizionista',
                'Fisioterapista'
            ]),
            'bio' => fake()->paragraph(),
            'foto_profilo' => null,
            'certificazioni' => json_encode([
                'Certificato ' . fake()->word(),
                'Diploma ' . fake()->word(),
            ]),
            'disponibilita' => json_encode([
                'lunedi' => ['09:00-13:00', '15:00-19:00'],
                'martedi' => ['09:00-13:00', '15:00-19:00'],
                'mercoledi' => ['09:00-13:00', '15:00-19:00'],
            ]),
            'attivo' => true,
            'visibile_sito' => true,
        ];
    }
}
