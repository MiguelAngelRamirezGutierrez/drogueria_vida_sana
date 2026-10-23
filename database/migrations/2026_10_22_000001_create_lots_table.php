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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('productos')->onDelete('cascade');
            $table->string('lot_number', 50);
            $table->date('expiration_date');
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Restricción de unicidad: un producto no puede tener duplicado el mismo número de lote
            $table->unique(['product_id', 'lot_number']);

            // Índice para optimización de consultas FEFO (First Expired, First Out)
            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
