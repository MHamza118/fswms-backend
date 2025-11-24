<?php

namespace Database\Seeders;

use App\Models\DraftSalesItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DraftSalesItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DraftSalesItem::factory(10)->create();
    }
}
