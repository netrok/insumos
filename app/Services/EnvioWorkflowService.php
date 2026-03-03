<?php

namespace App\Services;

use App\Models\Envio;
use App\Support\EnvioEstados;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnvioWorkflowService
{
    public function aprobar(Envio $envio, int $userId): Envio
    {
        $this->assertEstado($envio, EnvioEstados::SOLICITADA);

        return DB::transaction(function () use ($envio, $userId) {
            $envio->update([
                'estado' => EnvioEstados::APROBADA,
                'aprobado_por_user_id' => $userId,
                'aprobado_at' => now(),
            ]);
            return $envio->fresh();
        });
    }

    public function surtir(Envio $envio, int $userId, ?int $choferUserId = null): Envio
    {
        $this->assertEstado($envio, EnvioEstados::APROBADA);

        return DB::transaction(function () use ($envio, $userId, $choferUserId) {
            $envio->update([
                'estado' => EnvioEstados::SURTIDA,
                'surtido_por_user_id' => $userId,
                'surtido_at' => now(),
                'chofer_user_id' => $choferUserId ?? $envio->chofer_user_id,
            ]);
            return $envio->fresh();
        });
    }

    public function marcarSalida(Envio $envio, int $userId): Envio
    {
        $this->assertEstado($envio, EnvioEstados::SURTIDA);
        if (!$envio->chofer_user_id) {
            throw ValidationException::withMessages(['chofer_user_id' => 'Asigna chofer antes de marcar salida.']);
        }

        return DB::transaction(function () use ($envio) {
            $envio->update([
                'estado' => EnvioEstados::EN_TRANSITO,
                'salio_at' => now(),
            ]);
            return $envio->fresh();
        });
    }

    public function recibir(Envio $envio, int $userId): Envio
    {
        $this->assertEstado($envio, EnvioEstados::EN_TRANSITO);

        return DB::transaction(function () use ($envio) {
            $envio->update([
                'estado' => EnvioEstados::RECIBIDA,
                'recibido_at' => now(),
            ]);
            return $envio->fresh();
        });
    }

    public function reportarIncidencia(Envio $envio, int $userId, string $nota): Envio
    {
        $this->assertEstado($envio, EnvioEstados::EN_TRANSITO);

        return DB::transaction(function () use ($envio, $nota) {
            $envio->update([
                'estado' => EnvioEstados::INCIDENCIA,
                'notas' => trim(($envio->notas ?? '') . "\nINCIDENCIA: " . $nota),
            ]);
            return $envio->fresh();
        });
    }

    public function cerrar(Envio $envio, int $userId): Envio
    {
        if (!in_array($envio->estado, [EnvioEstados::RECIBIDA, EnvioEstados::INCIDENCIA], true)) {
            throw ValidationException::withMessages(['estado' => 'Solo se puede cerrar si está RECIBIDA o INCIDENCIA.']);
        }

        return DB::transaction(function () use ($envio) {
            $envio->update([
                'estado' => EnvioEstados::CERRADA,
                'cerrado_at' => now(),
            ]);
            return $envio->fresh();
        });
    }

    private function assertEstado(Envio $envio, string $expected): void
    {
        if ($envio->estado !== $expected) {
            throw ValidationException::withMessages([
                'estado' => "Transición inválida. Estado actual: {$envio->estado}. Se esperaba: {$expected}."
            ]);
        }
    }
}