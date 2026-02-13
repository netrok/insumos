<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            if (!Schema::hasColumn('entradas', 'consecutivo')) {
                $table->unsignedBigInteger('consecutivo')->nullable()->after('id');
                $table->unique('consecutivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('entradas', function (Blueprint $table) {
            if (Schema::hasColumn('entradas', 'consecutivo')) {
                $table->dropUnique(['consecutivo']);
                $table->dropColumn('consecutivo');
            }
        });
    }
};
