<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('envio_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('envio_id')->constrained('envios')->cascadeOnDelete();

            // SALIDA | RECEPCION | HANDOFF_ENTREGA | HANDOFF_RECIBE (si luego quieres)
            $table->string('tipo', 30)->index();

            // Guardamos hash (no el token plano)
            $table->string('token_hash', 64)->unique();

            // Para control de uso
            $table->integer('max_usos')->default(1);
            $table->integer('usos')->default(0);

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable(); // primera vez que se usó

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('last_used_by')->nullable()->constrained('users');

            $table->boolean('revocado')->default(false)->index();
            $table->timestamps();

            $table->index(['envio_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_tokens');
    }
};