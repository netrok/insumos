<?php

namespace App\Policies;

use App\Models\Envio;
use App\Models\User;
use App\Support\EnvioEstados;

class EnvioPolicy
{
    public function approve(User $user, Envio $envio): bool
    {
        return $user->hasAnyRole(['SUPERADMIN','ADMIN','ALMACEN'])
            && $envio->estado === EnvioEstados::SOLICITADA;
    }

    public function surtir(User $user, Envio $envio): bool
    {
        return $user->hasAnyRole(['SUPERADMIN','ADMIN','ALMACEN'])
            && $envio->estado === EnvioEstados::APROBADA;
    }

    public function salida(User $user, Envio $envio): bool
    {
        return $user->hasAnyRole(['SUPERADMIN','ADMIN','ALMACEN'])
            && $envio->estado === EnvioEstados::SURTIDA
            && !is_null($envio->chofer_user_id);
    }

    public function recibir(User $user, Envio $envio): bool
    {
        return $user->hasAnyRole(['SUPERADMIN','ADMIN','SUCURSAL'])
            && $envio->estado === EnvioEstados::EN_TRANSITO;
    }

    public function incidencia(User $user, Envio $envio): bool
    {
        return $user->hasAnyRole(['SUPERADMIN','ADMIN','SUCURSAL','ALMACEN'])
            && in_array($envio->estado, [EnvioEstados::EN_TRANSITO, EnvioEstados::RECIBIDA], true) === false
            ? false
            : true; // si quieres permitir incidencia solo en tránsito, lo dejamos SOLO EN_TRANSITO
    }

    public function cerrar(User $user, Envio $envio): bool
    {
        return $user->hasAnyRole(['SUPERADMIN','ADMIN','ALMACEN'])
            && in_array($envio->estado, [EnvioEstados::RECIBIDA, EnvioEstados::INCIDENCIA], true);
    }
}