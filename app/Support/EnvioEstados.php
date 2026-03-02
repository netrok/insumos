<?php

namespace App\Support;

final class EnvioEstados
{
    public const SOLICITADA  = 'SOLICITADA';
    public const APROBADA    = 'APROBADA';
    public const SURTIDA     = 'SURTIDA';
    public const EN_TRANSITO = 'EN_TRÁNSITO';
    public const RECIBIDA    = 'RECIBIDA';
    public const INCIDENCIA  = 'INCIDENCIA';
    public const CERRADA     = 'CERRADA';

    public static function all(): array
    {
        return [
            self::SOLICITADA,
            self::APROBADA,
            self::SURTIDA,
            self::EN_TRANSITO,
            self::RECIBIDA,
            self::INCIDENCIA,
            self::CERRADA,
        ];
    }
}