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
        Schema::create('device_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('condition')->nullable();
            $table->string('model_number')->nullable();
            $table->string('processor_type')->nullable();
            $table->string('processor_speed')->nullable();
            $table->string('processor_generation')->nullable();
            $table->string('ram_size')->nullable();//ram_size = memory_size
            $table->string('ram_type')->nullable(); //added ram_type
            $table->string('storage_size')->nullable(); //hdd_capacity=storage_size
            $table->string('storage_type')->nullable(); // storage type =ssd_capacity
            $table->string('screen_size')->nullable();
            $table->boolean('webcam')->default(false);
            $table->boolean('touch_screen')->default(false);
            $table->string('operating_system')->nullable();
            $table->string('power_supply_unit')->nullable();
            $table->string('pallet')->nullable();
            $table->string('asset_sse')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_attributes');
    }
};
