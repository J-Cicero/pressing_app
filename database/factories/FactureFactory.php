<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\Pressing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facture>
 */
class FactureFactory extends Factory
{
    protected $model = Facture::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'num_ticket' => 'TCK-'.date('Ymd').'-'.fake()->unique()->numerify('####'),
            'pressing_id' => Pressing::factory(),
            'user_id' => User::factory(),
            'client_nom' => fake()->name(),
            'client_telephone' => fake()->phoneNumber(),
            'montant_total' => fake()->randomFloat(2, 2000, 50000),
            'statut' => 'depose',
            'date_retrait_prevue' => now()->addDays(2)->toDateString(),
            'paye_at' => null,
        ];
    }

    /**
     * Indicate that the facture is pret (ready).
     */
    public function pret(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'pret',
            'paye_at' => null,
        ]);
    }

    /**
     * Indicate that the facture is paye_retire.
     */
    public function payeRetire(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'paye_retire',
            'paye_at' => now(),
        ]);
    }
}
