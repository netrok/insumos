<?php

namespace App\Http\Controllers;

use App\Exports\ProveedoresExport;
use App\Exports\ProveedorDetalleExport;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteProveedorController extends Controller
{
    public function listaXlsx(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $filename = 'proveedores_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ProveedoresExport($q), $filename);
    }

    public function listaPdf(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $proveedores = Proveedor::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('nombre', 'ilike', "%{$q}%")
                        ->orWhere('rfc', 'ilike', "%{$q}%")
                        ->orWhere('email', 'ilike', "%{$q}%")
                        ->orWhere('telefono', 'ilike', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('reportes.proveedores_pdf', [
            'proveedores' => $proveedores,
            'q' => $q,
            'generado' => now(),
        ])->setPaper('letter', 'portrait');

        $this->configurePdf($pdf);

        return $pdf->download('proveedores_' . now()->format('Ymd_His') . '.pdf');
    }

    public function proveedorXlsx(Proveedor $proveedor)
    {
        $filename = 'proveedor_' . $proveedor->id . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new ProveedorDetalleExport($proveedor), $filename);
    }

    public function proveedorPdf(Proveedor $proveedor)
    {
        $pdf = Pdf::loadView('reportes.proveedor_pdf', [
            'p' => $proveedor,
            'generado' => now(),
        ])->setPaper('letter', 'portrait');

        $this->configurePdf($pdf);

        return $pdf->download('proveedor_' . $proveedor->id . '_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Configuración DomPDF “anti-PDF-en-blanco”
     */
    private function configurePdf($pdf): void
    {
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
    }
}
