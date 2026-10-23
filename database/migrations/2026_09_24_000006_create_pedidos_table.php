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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('domiciliario_id')->nullable()->constrained('domiciliarios')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('direccion_entrega', 255);
            $table->string('estado', 30)->default('Recibido');
            $table->decimal('total', 12, 2)->default(0.00);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
