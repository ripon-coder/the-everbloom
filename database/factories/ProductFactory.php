<?php

namespace Database\Factories;

use App\Constants\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
     protected $model = \App\Models\Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sku' => $this->faker->unique()->numerify('PRD-####'),
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4),
            'is_active' => ProductStatus::ACTIVE,
        ];
    }
}
