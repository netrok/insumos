<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();   // Ej: Almacén Central, Sucursal X
            $table->string('codigo')->unique();   // Ej: ALM-CEN, SUC-01
            $table->string('ubicacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacens');
    }
};
