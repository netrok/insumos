<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('existencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('insumo_id')
                ->constrained('insumos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('almacen_id')
                ->constrained('almacenes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Stock final: decimal para soportar 0.001
            $table->decimal('stock', 14, 3)->default(0);

            $table->timestamps();

            $table->unique(['insumo_id', 'almacen_id']);
            $table->index('almacen_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('existencias');
    }
};
