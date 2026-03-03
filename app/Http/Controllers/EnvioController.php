<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Services\EnvioWorkflowService;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function __construct(private EnvioWorkflowService $svc) {}

    public function aprobar(Request $request, Envio $envio)
    {
        $this->authorize('approve', $envio);

        $this->svc->aprobar($envio, $request->user()->id);

        return back()->with('success', 'Envío aprobado.');
    }

    public function surtir(Request $request, Envio $envio)
    {
        $this->authorize('surtir', $envio);

        $data = $request->validate([
            'chofer_user_id' => ['nullable','integer','exists:users,id'],
        ]);

        $this->svc->surtir($envio, $request->user()->id, $data['chofer_user_id'] ?? null);

        return back()->with('success', 'Envío surtido.');
    }

    public function salida(Request $request, Envio $envio)
    {
        $this->authorize('salida', $envio);

        $this->svc->marcarSalida($envio, $request->user()->id);

        return back()->with('success', 'Salida registrada (EN TRÁNSITO).');
    }

    public function recibir(Request $request, Envio $envio)
    {
        $this->authorize('recibir', $envio);

        $this->svc->recibir($envio, $request->user()->id);

        return back()->with('success', 'Recepción registrada.');
    }

    public function incidencia(Request $request, Envio $envio)
    {
        $this->authorize('incidencia', $envio);

        $data = $request->validate([
            'nota' => ['required','string','min:5','max:1000'],
        ]);

        $this->svc->reportarIncidencia($envio, $request->user()->id, $data['nota']);

        return back()->with('warning', 'Incidencia registrada.');
    }

    public function cerrar(Request $request, Envio $envio)
    {
        $this->authorize('cerrar', $envio);

        $this->svc->cerrar($envio, $request->user()->id);

        return back()->with('success', 'Envío cerrado.');
    }
}