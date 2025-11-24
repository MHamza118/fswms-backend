<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $qty = $this->faker->numberBetween(1, 10);
        $price = $product->selling_price;
        $subtotal = $price * $qty;

        return [
            'sale_id' => Sale::factory(),
            'product_id' => $product->id,
            'price' => $price,
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
    }
}
