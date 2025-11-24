<?php
namespace Database\Factories;

use App\Models\DraftSale;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DraftSaleFactory extends Factory
{
    protected $model = DraftSale::class;

    public function definition()
    {
        return [
            'customer_id' => Customer::query()->inRandomOrder()->value('id') ?? Customer::factory(),
            'warehouse_id' => Warehouse::query()->inRandomOrder()->value('id') ?? Warehouse::factory(),
            // 'grand_total' => $this->faker->randomFloat(2, 100, 5000),
            'date' => $this->faker->date(),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
           'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),

        ];
    }
}
