<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 18px 20px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#111; }

    .h {
      background:#f3f4f6;
      padding:10px 12px;
      border:1px solid #e5e7eb;
      border-radius: 6px;
    }
    .title { font-size:14px; font-weight:700; margin-bottom:2px; }
    .muted { color:#6b7280; }

    .t { width:100%; border-collapse: collapse; margin-top:10px; }
    .t th, .t td { border-bottom:1px solid #e5e7eb; padding:6px 8px; vertical-align: top; }
    .t th { background:#fafafa; text-align:left; color:#374151; font-weight:700; }

    .right { text-align:right; }
    .nowrap { white-space: nowrap; }

    .total {
      margin-top:10px;
      text-align:right;
      font-weight:700;
      font-size: 12px;
    }

    .footer {
      position: fixed;
      bottom: -6px;
      left: 0;
      right: 0;
      text-align: right;
      font-size: 10px;
      color: #6b7280;
    }
  </style>
</head>
<body>

  <div class="h">
    <div class="title">Reporte de Salidas</div>
    <div class="muted">
      Periodo: <b>{{ $filters['desde'] }}</b> a <b>{{ $filters['hasta'] }}</b>
      | Almacén: <b>{{ $labels['almacen'] ?? 'Todos' }}</b>
      | Tipo: <b>{{ strtoupper((string)($labels['tipo'] ?? 'Todos')) }}</b>
    </div>
  </div>

  <table class="t">
    <thead>
      <tr>
        <th class="nowrap">Folio</th>
        <th class="nowrap">Fecha</th>
        <th>Almacén</th>
        <th class="nowrap">Tipo</th>
        <th class="right nowrap">Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
        <tr>
          <td class="nowrap">{{ $r->folio }}</td>
          <td class="nowrap">
            {{ $r->fecha ? \Illuminate\Support\Carbon::parse($r->fecha)->format('d/m/Y') : '—' }}
          </td>
          <td>{{ $r->almacen ?? '—' }}</td>
          <td class="nowrap">{{ strtoupper((string)($r->tipo ?? '—')) }}</td>
          <td class="right nowrap">${{ number_format((float)($r->total ?? 0), 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="muted">Sin resultados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="total">
    Total: ${{ number_format((float)$totalMonto, 2) }}
  </div>

  <div class="footer">
    Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
    Página {PAGE_NUM} de {PAGE_COUNT}
  </div>

</body>
</html>
