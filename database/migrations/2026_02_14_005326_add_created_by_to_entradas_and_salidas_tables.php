<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            // created_by (nullable) + FK a users + index
            if (!Schema::hasColumn('entradas', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('id'); // ajusta si quieres después de otro campo

                $table->index('created_by');
            }
        });

        Schema::table('salidas', function (Blueprint $table) {
            if (!Schema::hasColumn('salidas', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('id');

                $table->index('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            if (Schema::hasColumn('entradas', 'created_by')) {
                // baja FK + columna
                $table->dropConstrainedForeignId('created_by');
            }
        });

        Schema::table('salidas', function (Blueprint $table) {
            if (Schema::hasColumn('salidas', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};
