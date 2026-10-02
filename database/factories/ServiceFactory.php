<?php

namespace Database\Factories;

use App\Models\Pressing;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pressing_id' => Pressing::factory(),
            'designation' => fake()->randomElement(['Nettoyage Costume', 'Lavage Chemise', 'Repassage Pantalon', 'Nettoyage Robe', 'Lavage Couette']),
            'prix_unitaire' => fake()->randomElement([1000.00, 1500.00, 2000.00, 2500.00, 5000.00]),
        ];
    }
}
