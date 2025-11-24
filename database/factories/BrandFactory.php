<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->sentence(10),
            'image' => 'brands/' . $this->faker->unique()->word() . '.png',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
