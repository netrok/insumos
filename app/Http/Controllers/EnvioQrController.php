<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Services\EnvioQrService;
use App\Services\EnvioWorkflowService;
use App\Support\EnvioTokenTipos;
use Illuminate\Http\Request;

class EnvioQrController extends Controller
{
    public function __construct(
        private EnvioQrService $qr,
        private EnvioWorkflowService $wf,
    ) {}

    // Generar QR de salida o recepción (se mostrará en UI)
    public function generar(Request $request, Envio $envio)
    {
        $data = $request->validate([
            'tipo' => ['required','string'],
        ]);

        // autorización: genera QR solo almacén/admin
        $request->user()->hasAnyRole(['SUPERADMIN','ADMIN','ALMACEN']) || abort(403);

        $out = $this->qr->generarToken($envio, $data['tipo'], $request->user()->id);

        // Por ahora regresamos JSON. En UI lo conviertes a QR.
        return response()->json($out);
    }

    // Escaneo: consume token y ejecuta acción
    public function escanear(Request $request)
    {
        $data = $request->validate([
            'payload' => ['required','string'],
        ]);

        $token = $this->qr->consumir($data['payload'], $request->user()->id);
        $envio = $token->envio()->first();

        // Acción según tipo
        if ($token->tipo === EnvioTokenTipos::SALIDA) {
            // marca salida: SURTIDA -> EN_TRÁNSITO
            // aquí puedes exigir rol ALMACEN o CHOFER si quieres
            $this->wf->marcarSalida($envio, $request->user()->id);
        }

        if ($token->tipo === EnvioTokenTipos::RECEPCION) {
            // EN_TRÁNSITO -> RECIBIDA
            $this->wf->recibir($envio, $request->user()->id);
        }

        return response()->json([
            'ok' => true,
            'envio_id' => $envio->id,
            'estado' => $envio->fresh()->estado,
            'token_usos' => $token->usos,
            'token_max_usos' => $token->max_usos,
        ]);
    }
}