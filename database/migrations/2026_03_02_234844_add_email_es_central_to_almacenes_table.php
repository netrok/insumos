<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('almacenes', function (Blueprint $table) {
        $table->string('email')->nullable()->after('nombre');
        $table->boolean('es_central')->default(false)->after('email');
        $table->index('es_central');
    });
}

public function down(): void
{
    Schema::table('almacenes', function (Blueprint $table) {
        $table->dropIndex(['es_central']);
        $table->dropColumn(['email', 'es_central']);
    });
}
};
