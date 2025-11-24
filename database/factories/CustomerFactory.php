<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'tax_number' => $this->faker->optional()->numerify('TAX-#####'),
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}

