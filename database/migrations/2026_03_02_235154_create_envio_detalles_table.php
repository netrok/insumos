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
    Schema::create('envio_detalles', function (Blueprint $table) {
        $table->id();

        $table->foreignId('envio_id')->constrained('envios')->cascadeOnDelete();
        $table->foreignId('insumo_id')->constrained('insumos');

        $table->decimal('cantidad', 12, 3); // por si manejas fracciones
        $table->string('unidad')->nullable(); // opcional, si no está en insumo

        $table->timestamps();

        $table->unique(['envio_id', 'insumo_id']); // evita duplicados del mismo insumo
    });
}

public function down(): void
{
    Schema::dropIfExists('envio_detalles');
}
};
