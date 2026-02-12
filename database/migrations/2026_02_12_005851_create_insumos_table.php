<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();

            $table->string('sku', 50)->unique()->comment('SKU interno, ej: INS-0001');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('unidad_id')
                ->constrained('unidades')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('costo_promedio', 12, 2)->default(0);
            $table->unsignedInteger('stock_minimo')->default(0);

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index('categoria_id');
            $table->index('unidad_id');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
