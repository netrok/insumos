<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('rfc', 20)->nullable()->index();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
