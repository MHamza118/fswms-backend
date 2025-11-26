<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'product_image' => null,
            'sku' => $this->faker->unique()->bothify('SKU-###???'),
            'barcode' => $this->faker->unique()->ean13,
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'unit_id' => Unit::inRandomOrder()->first()->id ?? Unit::factory(),
            'warehouse_id' => Warehouse::inRandomOrder()->first()->id ?? Warehouse::factory(),
            'brand_id' => Brand::inRandomOrder()->first()->id ?? Brand::factory(),
            'qty_alert' => $this->faker->numberBetween(5, 20),
            'stock_quantity' => $this->faker->numberBetween(5, 20),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'tax' => $this->faker->randomFloat(2, 0, 20),
            'purchase_price' => $this->faker->randomFloat(2, 50, 500),
            'selling_price' => $this->faker->randomFloat(2, 100, 1000),
            'description' => $this->faker->sentence,
        ];
    }
}
