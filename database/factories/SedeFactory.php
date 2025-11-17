<?php

namespace Database\Factories;

use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

class SedeFactory extends Factory
{
    protected $model = Sede::class;

    public function definition(): array
    {
        return [
            'nome' => 'MA.GIA ' . fake()->city(),
            'indirizzo' => fake()->streetAddress(),
            'citta' => fake()->city(),
            'provincia' => fake()->stateAbbr(),
            'cap' => fake()->postcode(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'latitudine' => fake()->latitude(35, 47),
            'longitudine' => fake()->longitude(6, 19),
            'capienza' => fake()->numberBetween(15, 30),
            'attiva' => true,
            'principale' => false,
            'orari_apertura' => json_encode([
                'lunedi' => ['09:00-13:00', '15:00-20:00'],
                'martedi' => ['09:00-13:00', '15:00-20:00'],
                'mercoledi' => ['09:00-13:00', '15:00-20:00'],
                'giovedi' => ['09:00-13:00', '15:00-20:00'],
                'venerdi' => ['09:00-13:00', '15:00-20:00'],
            ]),
        ];
    }

    public function principale(): static
    {
        return $this->state(fn (array $attributes) => [
            'principale' => true,
        ]);
    }
}
