<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'category_id' => 1,
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'condition_type' => 'new',
            'selling_price' => 1000,
            'status' => 'active',
        ];
    }
}
