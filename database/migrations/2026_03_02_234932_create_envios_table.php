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
    Schema::create('envios', function (Blueprint $table) {
        $table->id();

        $table->string('folio')->unique(); // EJ: ENV-20260302-0001
        $table->foreignId('almacen_origen_id')->constrained('almacenes');
        $table->foreignId('almacen_destino_id')->constrained('almacenes');

        $table->foreignId('solicitado_por_user_id')->constrained('users');
        $table->foreignId('aprobado_por_user_id')->nullable()->constrained('users');
        $table->foreignId('surtido_por_user_id')->nullable()->constrained('users');

        $table->foreignId('chofer_user_id')->nullable()->constrained('users');

        // Estados (string para claridad y auditoría)
        $table->string('estado', 20)->default('SOLICITADA')->index();

        // Fechas de transición (para trazabilidad real)
        $table->timestamp('aprobado_at')->nullable();
        $table->timestamp('surtido_at')->nullable();
        $table->timestamp('salio_at')->nullable();     // EN_TRÁNSITO
        $table->timestamp('recibido_at')->nullable();  // RECIBIDA
        $table->timestamp('cerrado_at')->nullable();   // CERRADA

        $table->text('notas')->nullable();

        $table->timestamps();

        $table->index(['almacen_origen_id', 'almacen_destino_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('envios');
}
};
