<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 30)->unique();      // ej: ENT-20260212-153015-ABCD
            $table->date('fecha');

            $table->foreignId('almacen_id')->constrained('almacenes');

            // Si aún no tienes proveedores, puedes comentar esta línea o crear tabla después
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');

            $table->string('tipo', 30)->default('compra'); // compra|ajuste|devolucion|traspaso_entrada...
            $table->text('observaciones')->nullable();

            $table->decimal('total', 12, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->index(['fecha', 'almacen_id']);
            $table->index(['tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};


