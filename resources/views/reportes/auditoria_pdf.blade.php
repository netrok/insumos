{{-- resources/views/reportes/auditoria_pdf.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Auditoría de movimientos</title>

  <style>
    @page { margin: 12mm 12mm 16mm 12mm; }

    body{
      font-family: DejaVu Sans, sans-serif;
      font-size: 10.5px;
      color:#111827;
      margin: 0;
      padding: 0;
    }

    .muted{ color:#6b7280; }

    /* Header GV */
    .header{
      border: 1px solid #e5e7eb;
      background: #f9fafb;
      border-radius: 10px;
      padding: 10px 12px;
      margin: 0 0 8px 0;
    }
    .h-title{
      font-size: 16px;
      font-weight: 800;
      margin: 0;
      letter-spacing: .2px;
    }
    .h-sub{
      margin: 4px 0 0 0;
      font-size: 10px;
      color:#6b7280;
    }

    /* Tabla */
    table.data{
      width:100%;
      border-collapse: collapse;
      border: 1px solid #e5e7eb;
      table-layout: fixed;
      margin: 0;
    }

    /* IMPORTANTE: NO forzar avoid en TR (DomPDF se pone exquisito) */
    table.data tr{ page-break-inside: auto; }

    table.data th{
      background:#f3f4f6;
      border-bottom: 1px solid #e5e7eb;
      padding: 6px 8px;
      text-align:left;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .06em;
      color:#374151;
      white-space: nowrap;
      line-height: 12px;
    }

    table.data td{
      border-bottom: 1px solid #f1f5f9;
      padding: 6px 8px;
      vertical-align: middle;
      line-height: 12px;
      height: 18px;      /* fuerza filas normales */
      /* NO overflow hidden (DomPDF hace cosas raras) */
    }

    .right{ text-align:right; }
    .center{ text-align:center; }

    /* “Badge” SIN span: estilo directo del TD */
    .tipoCell{
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .06em;
      text-transform: uppercase;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      padding: 0;            /* lo controla el TD base */
    }
    .tipoEnt{
      border-color:#a7f3d0;
      background:#ecfdf5;
      color:#065f46;
    }
    .tipoSal{
      border-color:#fecaca;
      background:#fff1f2;
      color:#9f1239;
    }

    .neg{ color:#9f1239; font-weight: 900; }

    .wrap{
      white-space: normal;
      height: auto;
      line-height: 13px;
    }

    /* Footer fijo (normal, sin negativos) */
    .footer{
      position: fixed;
      bottom: 0mm;
      left: 0;
      right: 0;
      font-size: 9px;
      color:#6b7280;
    }
    .footer .l{ float:left; }
    .footer .r{ float:right; }

    * { box-shadow:none !important; }
  </style>
</head>

<body>
@php
  $from = $labels['from'] ?? '—';
  $to   = $labels['to'] ?? '—';
  $tipo = $labels['tipo'] ?? 'Todos';
  $q    = $labels['q'] ?? '—';
@endphp

<div class="header">
  <p class="h-title">Auditoría de movimientos</p>
  <p class="h-sub">
    Rango: <strong>{{ $from }}</strong> a <strong>{{ $to }}</strong>
    &nbsp;|&nbsp; Tipo: <strong>{{ $tipo }}</strong>
    &nbsp;|&nbsp; Búsqueda: <strong>{{ $q }}</strong>
  </p>
</div>

<table class="data">
  <thead>
    <tr>
      <th style="width:11%;">Fecha</th>
      <th style="width:7%;">Tipo</th>
      <th style="width:16%;">Folio</th>
      <th style="width:14%;">Usuario</th>
      <th style="width:26%;">Insumo</th>
      <th style="width:16%;">Almacén</th>
      <th style="width:10%;" class="right">Cantidad</th>
    </tr>
  </thead>
  <tbody>
    @forelse(($rows ?? []) as $r)
      @php
        $t = (string)($r->tipo ?? '');
        $cant = (float)($r->cantidad ?? 0);
        $tipoClass = $t === 'ENT' ? 'tipoEnt' : ($t === 'SAL' ? 'tipoSal' : '');
      @endphp

      <tr>
        <td>{{ $r->fecha ?? '—' }}</td>

        {{-- CLAVE: sin span, el TD mismo es el “badge” --}}
        <td class="center tipoCell {{ $tipoClass }}">
          {{ $t ?: '—' }}
        </td>

        <td><strong>{{ $r->folio ?? '—' }}</strong></td>
        <td>{{ $r->usuario ?? '—' }}</td>
        <td class="wrap">{{ $r->insumo ?? '—' }}</td>
        <td>{{ $r->almacen ?? '—' }}</td>
        <td class="right {{ $cant < 0 ? 'neg' : '' }}">{{ number_format($cant, 2) }}</td>
      </tr>

    @empty
      <tr>
        <td colspan="7" class="muted" style="text-align:center; padding:16px; height:auto; line-height:14px;">
          Sin movimientos para el filtro seleccionado.
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <div class="l">Generado: {{ now()->format('Y-m-d H:i') }}</div>
  <div class="r">Página {PAGE_NUM} de {PAGE_COUNT}</div>
</div>

</body>
</html>
