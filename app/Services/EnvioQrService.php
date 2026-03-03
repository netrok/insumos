<?php

namespace App\Services;

use App\Models\Envio;
use App\Models\EnvioToken;
use App\Support\EnvioEstados;
use App\Support\EnvioTokenTipos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnvioQrService
{
    /**
     * Genera token plano (solo se muestra 1 vez) y guarda hash en BD.
     * - SALIDA: 1 uso, expira rápido (ej 12h)
     * - RECEPCION: multi-uso hasta cierre (ej 50 usos), expira más (ej 7 días) o null
     */
    public function generarToken(Envio $envio, string $tipo, int $createdBy): array
    {
        if (!in_array($tipo, EnvioTokenTipos::all(), true)) {
            throw ValidationException::withMessages(['tipo' => 'Tipo de token inválido.']);
        }

        // Reglas por tipo
        if ($tipo === EnvioTokenTipos::SALIDA && $envio->estado !== EnvioEstados::SURTIDA) {
            throw ValidationException::withMessages(['estado' => 'QR de SALIDA solo se genera cuando está SURTIDA.']);
        }

        if ($tipo === EnvioTokenTipos::RECEPCION && !in_array($envio->estado, [EnvioEstados::EN_TRANSITO, EnvioEstados::SURTIDA], true)) {
            // puedes permitir generarlo desde SURTIDA para traerlo impreso, o solo EN_TRÁNSITO
            throw ValidationException::withMessages(['estado' => 'QR de RECEPCIÓN se genera cuando está SURTIDA o EN TRÁNSITO.']);
        }

        $plain = Str::random(48);               // token plano (no se guarda)
        $hash  = hash('sha256', $plain);

        [$maxUsos, $expiresAt] = $this->defaultsPorTipo($tipo);

        $token = DB::transaction(function () use ($envio, $tipo, $hash, $maxUsos, $expiresAt, $createdBy) {
            // Opcional: revocar tokens anteriores del mismo tipo para el mismo envío
            EnvioToken::where('envio_id', $envio->id)->where('tipo', $tipo)->update(['revocado' => true]);

            return EnvioToken::create([
                'envio_id' => $envio->id,
                'tipo' => $tipo,
                'token_hash' => $hash,
                'max_usos' => $maxUsos,
                'usos' => 0,
                'expires_at' => $expiresAt,
                'created_by' => $createdBy,
                'revocado' => false,
            ]);
        });

        // payload para QR (minimalista y estable)
        // NO uses IDs a pelo sin token, el token es la llave.
        $payload = "ENVIO|{$envio->id}|{$tipo}|{$plain}";

        return [
            'token_id' => $token->id,
            'tipo' => $tipo,
            'payload' => $payload, // esto se convierte a QR
            'expires_at' => $token->expires_at,
            'max_usos' => $token->max_usos,
        ];
    }

    /**
     * Valida payload y consume 1 uso.
     */
    public function consumir(string $payload, int $userId): EnvioToken
    {
        [$envioId, $tipo, $plain] = $this->parsePayload($payload);
        $hash = hash('sha256', $plain);

        $token = EnvioToken::where('envio_id', $envioId)
            ->where('tipo', $tipo)
            ->where('token_hash', $hash)
            ->first();

        if (!$token) {
            throw ValidationException::withMessages(['qr' => 'QR inválido.']);
        }
        if ($token->revocado) {
            throw ValidationException::withMessages(['qr' => 'QR revocado.']);
        }
        if ($token->expires_at && now()->greaterThan($token->expires_at)) {
            throw ValidationException::withMessages(['qr' => 'QR expirado.']);
        }
        if ($token->usos >= $token->max_usos) {
            throw ValidationException::withMessages(['qr' => 'QR ya fue utilizado (límite alcanzado).']);
        }

        return DB::transaction(function () use ($token, $userId) {
            $token->usos += 1;
            $token->last_used_by = $userId;
            $token->used_at = $token->used_at ?? now();
            $token->save();

            return $token->fresh();
        });
    }

    private function defaultsPorTipo(string $tipo): array
    {
        if ($tipo === EnvioTokenTipos::SALIDA) {
            return [1, now()->addHours(12)];
        }
        if ($tipo === EnvioTokenTipos::RECEPCION) {
            // reusable hasta cierre: limita usos para evitar abuso
            return [50, now()->addDays(7)];
        }
        return [1, now()->addHours(12)];
    }

    private function parsePayload(string $payload): array
    {
        // formato: ENVIO|{envioId}|{tipo}|{token}
        $parts = explode('|', trim($payload));
        if (count($parts) !== 4 || $parts[0] !== 'ENVIO') {
            throw ValidationException::withMessages(['qr' => 'Formato de QR inválido.']);
        }

        $envioId = (int) $parts[1];
        $tipo = $parts[2];
        $plain = $parts[3];

        if ($envioId <= 0 || $plain === '') {
            throw ValidationException::withMessages(['qr' => 'QR inválido.']);
        }

        return [$envioId, $tipo, $plain];
    }
}