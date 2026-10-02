<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LigneFacture>
 */
class LigneFactureFactory extends Factory
{
    protected $model = LigneFacture::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantite = fake()->numberBetween(1, 5);
        $prix = fake()->randomElement([1000.00, 1500.00, 2000.00, 2500.00, 5000.00]);

        return [
            'facture_id' => Facture::factory(),
            'service_id' => Service::factory(),
            'quantite' => $quantite,
            'prix_applique' => $prix,
        ];
    }
}
