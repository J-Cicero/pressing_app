<?php

namespace Database\Factories;

use App\Models\Pressing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pressing>
 */
class PressingFactory extends Factory
{
    protected $model = Pressing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->company(),
            'ville' => fake()->city(),
            'quartier' => fake()->streetName(),
            'telephone' => fake()->phoneNumber(),
        ];
    }
}
