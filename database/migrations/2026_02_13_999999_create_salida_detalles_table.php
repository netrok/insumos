<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salida_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('salida_id')->constrained('salidas')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos');

            $table->decimal('cantidad', 14, 3);
            $table->decimal('costo_unitario', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);

            $table->timestamps();

            $table->index(['salida_id', 'insumo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salida_detalles');
    }
};
