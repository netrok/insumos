<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Postgres: convertimos cantidad a numeric(14,3)
        // Si era integer, esto lo convierte sin pérdida (solo agrega .000)
        DB::statement('ALTER TABLE existencias ALTER COLUMN cantidad TYPE numeric(14,3) USING cantidad::numeric(14,3)');
        DB::statement('ALTER TABLE existencias ALTER COLUMN cantidad SET DEFAULT 0');
        DB::statement('UPDATE existencias SET cantidad = 0 WHERE cantidad IS NULL');
        DB::statement('ALTER TABLE existencias ALTER COLUMN cantidad SET NOT NULL');
    }

    public function down(): void
    {
        // Reversa: lo regresamos a integer (redondeando)
        DB::statement('ALTER TABLE existencias ALTER COLUMN cantidad TYPE integer USING round(cantidad)::integer');
        DB::statement('ALTER TABLE existencias ALTER COLUMN cantidad SET DEFAULT 0');
        DB::statement('UPDATE existencias SET cantidad = 0 WHERE cantidad IS NULL');
        DB::statement('ALTER TABLE existencias ALTER COLUMN cantidad SET NOT NULL');
    }
};
