<?php

namespace Database\Factories;

use App\Models\DraftSale;
use App\Models\DraftSalesItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class DraftSalesItemFactory extends Factory
{
    protected $model = DraftSalesItem::class;

    public function definition()
    {
        $price = $this->faker->randomFloat(2, 100, 1000);
        $qty = $this->faker->numberBetween(1, 20);
        $subtotal = $price * $qty;
        $draft_sale_id= DraftSale::query()->inRandomOrder()->value('id') ?? DraftSale::factory();

        return [
            'product_id'   => Product::query()->inRandomOrder()->value('id') ?? Product::factory(),
            'draft_sale_id'=>$draft_sale_id,
            'price' => $price,
            'qty' => $qty,
            'subtotal' => $subtotal,

        ];
    }
}
