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
        schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('quantity');
            $table->text('movement_type'); // 'in' or 'out'
        });
        schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('current_quantity')->default(0);
            $table->float('minimum_quantity')->default(0);
            $table->float('maximum_quantity')->default(0);
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_movement_id')->constrained();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
