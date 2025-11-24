<?php

namespace Database\Seeders;

use App\Models\DraftSale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DraftSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DraftSale::factory(20)->create();
    }
}
