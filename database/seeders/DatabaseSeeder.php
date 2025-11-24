<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            WarehouseSeeder::class,
            UnitSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            SaleSeeder::class,
            SaleItemSeeder::class,
            DraftSaleSeeder::class,
            DraftSalesItemSeeder::class,
            ShippingSeeder::class,
        ]);
    }
}
