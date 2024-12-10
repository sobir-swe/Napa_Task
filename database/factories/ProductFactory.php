<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'category_id' => Category::all()->random()->id,
            'description' => $this->faker->text(),
            'price' => $this->faker->numberBetween(1000, 10000),
            'status' => $this->faker->randomElement(['active', 'inactive'])
        ];
    }
}
