<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####??'),
            'slug' => fake()->unique()->slug(),
            'price' => fake()->randomFloat(2, 10, 500),
            'sale_price' => null,
            'stock_quantity' => fake()->numberBetween(0, 20),
            'status' => 'active',
            'visibility' => 'visible',
            'featured' => false,
        ];
    }
}
