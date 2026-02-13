<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('existencias', function (Blueprint $table) {
            // Índices (si no existen)
            $table->index('almacen_id', 'existencias_almacen_id_idx');
            $table->index('insumo_id', 'existencias_insumo_id_idx');

            // Unicidad por almacén+insumo (clave de verdad)
            $table->unique(['almacen_id', 'insumo_id'], 'existencias_almacen_insumo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('existencias', function (Blueprint $table) {
            $table->dropUnique('existencias_almacen_insumo_unique');
            $table->dropIndex('existencias_almacen_id_idx');
            $table->dropIndex('existencias_insumo_id_idx');
        });
    }
};
