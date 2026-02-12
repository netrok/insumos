<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrada_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrada_id')
                ->constrained('entradas')
                ->cascadeOnDelete();

            $table->foreignId('insumo_id')->constrained('insumos');

            // Si solo manejas piezas enteras, cámbialo a integer
            $table->decimal('cantidad', 12, 3);

            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();

            $table->index(['entrada_id', 'insumo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrada_detalles');
    }
};
