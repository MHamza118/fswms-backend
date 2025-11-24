<?php

namespace Database\Factories;

use App\Models\Shipping;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingFactory extends Factory
{
    protected $model = Shipping::class;

    public function definition()
    {
        return [
            'sale_id' => Sale::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'warehouse_id' => Warehouse::factory()->create()->id,
            'date_time' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement(['ordered', 'packed', 'shipped', 'delivered', 'cancelled']),
            'deliver_to' => $this->faker->name(),
            'address' => $this->faker->address(),
            'description' => $this->faker->sentence()
        ];
    }
}
