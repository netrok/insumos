<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('existencias', function (Blueprint $table) {
            // 1) renombra
            $table->renameColumn('cantidad', 'stock');
        });

        Schema::table('existencias', function (Blueprint $table) {
            // 2) cambia tipo a decimal para soportar 0.001
            $table->decimal('stock', 14, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('existencias', function (Blueprint $table) {
            $table->renameColumn('stock', 'cantidad');
        });

        Schema::table('existencias', function (Blueprint $table) {
            $table->unsignedInteger('cantidad')->default(0)->change();
        });
    }
};
