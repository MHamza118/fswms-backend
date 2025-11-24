<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Elite x2 1012 G1
            $table->string('product_image')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->unique(); //5CG7432M4F
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); //Laptop
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade'); //HP
            $table->integer('qty_alert')->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('purchase_price', 10, 2);
            //Condition,Model Number, Processor Type, Processor Speed, Processor Generation,Mem Size (Total), HDD Capacity, Screen Size,Webcam,Operating System,Power supply unit, Pallet
            $table->decimal('selling_price', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
