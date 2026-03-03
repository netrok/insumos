<?php

namespace App\Support;

final class EnvioTokenTipos
{
    public const SALIDA    = 'SALIDA';
    public const RECEPCION = 'RECEPCION';
    // luego: HANDOFF_ENTREGA / HANDOFF_RECIBE

    public static function all(): array
    {
        return [self::SALIDA, self::RECEPCION];
    }
}