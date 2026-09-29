<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'product_id' => fake()->boolean(40) ? Product::inRandomOrder()->value('id') : null,
            'video_path' => 'reels/sample-placeholder.mp4',
            'thumbnail' => null,
            'caption' => fake()->sentence(fake()->numberBetween(5, 15)),
            'views_count' => fake()->numberBetween(0, 5000),
            'status' => fake()->randomElement(['published', 'published', 'published', 'flagged']),
        ];
    }
}