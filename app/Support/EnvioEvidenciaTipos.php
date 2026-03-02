<?php

namespace App\Support;

final class EnvioEvidenciaTipos
{
    public const SALIDA = 'SALIDA';
    public const RECEPCION = 'RECEPCION';
    public const RETORNO = 'RETORNO';
    public const INCIDENCIA = 'INCIDENCIA';
    public const HANDOFF_ENTREGA = 'HANDOFF_ENTREGA';
    public const HANDOFF_RECIBE = 'HANDOFF_RECIBE';

    public static function all(): array
    {
        return [
            self::SALIDA,
            self::RECEPCION,
            self::RETORNO,
            self::INCIDENCIA,
            self::HANDOFF_ENTREGA,
            self::HANDOFF_RECIBE,
        ];
    }
}