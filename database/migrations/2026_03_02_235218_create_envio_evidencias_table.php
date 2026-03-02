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
    Schema::create('envio_evidencias', function (Blueprint $table) {
        $table->id();

        $table->foreignId('envio_id')->constrained('envios')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users'); // quién subió

        // tipo: SALIDA, RECEPCION, RETORNO, INCIDENCIA, HANDOFF_ENTREGA, HANDOFF_RECIBE
        $table->string('tipo', 30)->index();

        $table->string('archivo'); // path storage (ej: envios/123/salida_1.jpg)
        $table->text('nota')->nullable();

        // opcional: en qué estado estaba el envío cuando se subió
        $table->string('estado_envio', 20)->nullable()->index();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('envio_evidencias');
}
};
