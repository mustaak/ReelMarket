<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'product_id' => fake()->boolean(30) ? Product::inRandomOrder()->value('id') : null,
            'content' => fake()->sentence(fake()->numberBetween(8, 20)),
            'visibility' => fake()->randomElement(['public', 'followers']),
            'status' => fake()->randomElement(['published', 'published', 'published', 'flagged']),
        ];
    }
}