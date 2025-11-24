<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $grandTotal = $this->faker->randomFloat(2, 100, 5000);
        $paid = $this->faker->randomFloat(2, 0, $grandTotal);
        $due = $grandTotal - $paid;
        $status = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentStatus = $paid == $grandTotal ? 'paid' : ($paid > 0 ? 'partial' : 'pending');

        return [
            'customer_id' => Customer::query()->inRandomOrder()->value('id') ?? Customer::factory(),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'warehouse_id' => Warehouse::query()->inRandomOrder()->value('id') ?? Warehouse::factory(),
            'tax' => $this->faker->randomFloat(2, 0, 100),
            'discount' => $this->faker->randomFloat(2, 0, 100),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 50),
            'grand_total' => $grandTotal,
            'paid' => $paid,
            'due' => $due,
            'payment_status' => $paymentStatus,
            'currency' => 'USD',
            'description' => $this->faker->sentence(),
            'expected_delivery_date' => $this->faker->date(),
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'bank_transfer']),
            'status' => $this->faker->randomElement($status),
        ];
    }
}
