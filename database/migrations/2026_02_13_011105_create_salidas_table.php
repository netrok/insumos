<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('consecutivo')->nullable();
            $table->string('folio', 20)->unique();

            $table->date('fecha');

            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->string('tipo', 30); // consumo | merma | ajuste | traspaso (luego)
            $table->text('observaciones')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->decimal('total', 14, 2)->default(0);

            $table->timestamps();

            $table->index(['fecha', 'almacen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
