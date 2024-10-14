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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('db_classification')->nullable();
            $table->string('chasiss_number')->nullable();
            $table->string('car_manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('color')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('number')->nullable();
            $table->text('content')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('status_camera')->default(0);
            $table->boolean('show_price')->default(0);
            $table->double('price')->nullable();
            $table->string('buyer')->nullable();
            $table->string('buying_date')->nullable();
            $table->string('company_source')->nullable();
            $table->double('korean_price')->nullable();
            $table->double('price_in_dollar')->nullable();
            $table->double('shipping_price')->nullable();
            $table->double('custom_price')->nullable();
            $table->double('fixing_price')->nullable();
            $table->double('total_cost')->nullable();
            $table->string('city')->nullable();
            $table->string('arrival_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
