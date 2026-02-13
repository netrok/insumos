<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Kardex</title>
  <style>
    @page { margin: 18px 18px 42px 18px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
    .muted { color:#6B7280; }
    .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 10px; }
    .row { display: table; width: 100%; }
    .col { display: table-cell; vertical-align: middle; }
    .logo { width: 130px; }
    .title { font-size: 16px; font-weight: 700; letter-spacing: .2px; }
    .subtitle { font-size: 11px; color:#374151; margin-top: 2px; }
    .chips { margin-top: 6px; }
    .chip {
      display: inline-block; padding: 4px 8px; border: 1px solid #E5E7EB; border-radius: 999px;
      margin-right: 6px; margin-bottom: 6px; background: #F9FAFB; font-size: 10px;
    }
    .totals { margin: 10px 0 12px; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px; background:#F9FAFB;}
    .tgrid { width:100%; border-collapse: collapse; }
    .tgrid td { padding: 4px 6px; }
    .tgrid .k { color:#6B7280; font-size: 10px; }
    .tgrid .v { font-weight: 700; font-size: 12px; text-align: right; }

    table { width:100%; border-collapse: collapse; }
    thead th {
      background:#111827; color:#fff; font-weight:700; padding: 8px 6px; font-size: 10px;
      text-transform: uppercase; letter-spacing: .3px;
    }
    tbody td { padding: 7px 6px; border-bottom: 1px solid #E5E7EB; }
    .right { text-align: right; }
    .center { text-align: center; }
    .badge {
      display:inline-block; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700;
    }
    .ent { background:#E8F7EE; color:#166534; border:1px solid #BBF7D0; }
    .sal { background:#FFF7ED; color:#9A3412; border:1px solid #FED7AA; }

    .footer {
      position: fixed; bottom: -28px; left: 0; right: 0;
      border-top: 1px solid #E5E7EB; padding-top: 8px; font-size: 10px; color:#6B7280;
    }
    .pagenum:after { content: counter(page) " / " counter(pages); }
  </style>
</head>
<body>

@php
  // Mostrar saldo por renglón solo cuando hay insumo seleccionado
  $showSaldo = !empty($showSaldo) || !empty($filters['insumo_id']);

  // Saldo acumulado en PDF: arranca en saldo inicial y se va sumando cantidad (ENT +, SAL -)
  $running = (float)($saldoInicial ?? 0);
@endphp

  {{-- HEADER --}}
  <div class="header">
    <div class="row">
      <div class="col" style="width:140px;">
        <img class="logo" src="{{ public_path('img/logo.png') }}" alt="Logo">
      </div>
      <div class="col">
        <div class="title">KARDEX</div>
        <div class="subtitle">
          Movimientos de inventario (Entradas / Salidas)
          <span class="muted">• Generado: {{ now()->format('d/m/Y H:i') }}</span>
        </div>

        <div class="chips">
          <span class="chip"><b>Almacén:</b> {{ $labels['almacen'] ?? 'Todos' }}</span>
          <span class="chip"><b>Insumo:</b> {{ $labels['insumo'] ?? 'Todos' }}</span>
          <span class="chip"><b>Tipo:</b> {{ $labels['tipo'] ?? 'Todos' }}</span>
          <span class="chip"><b>Desde:</b> {{ \Illuminate\Support\Carbon::parse($filters['desde'])->format('d/m/Y') }}</span>
          <span class="chip"><b>Hasta:</b> {{ \Illuminate\Support\Carbon::parse($filters['hasta'])->format('d/m/Y') }}</span>
          @if(!empty($filters['q']))
            <span class="chip"><b>Buscar:</b> {{ $filters['q'] }}</span>
          @endif
          @if($showSaldo)
            <span class="chip"><b>Saldo por renglón:</b> Sí</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- TOTALES --}}
  <div class="totals">
    <table class="tgrid">
      <tr>
        <td class="k">Saldo inicial</td>
        <td class="v">{{ number_format((float)$saldoInicial, 3) }}</td>

        <td class="k">Entradas</td>
        <td class="v">{{ number_format((float)($totals['entradas_qty'] ?? 0), 3) }}</td>

        <td class="k">Salidas</td>
        <td class="v">{{ number_format((float)($totals['salidas_qty'] ?? 0), 3) }}</td>

        <td class="k">Saldo rango</td>
        <td class="v">{{ number_format((float)($totals['saldo_qty'] ?? 0), 3) }}</td>

        <td class="k">Saldo final</td>
        <td class="v">{{ number_format((float)$saldoInicial + (float)($totals['saldo_qty'] ?? 0), 3) }}</td>
      </tr>
    </table>
  </div>

  {{-- TABLA --}}
  <table>
    <thead>
      <tr>
        <th style="width:70px;">Fecha</th>
        <th style="width:55px;">Tipo</th>
        <th style="width:90px;">Folio</th>
        <th style="width:120px;">Almacén</th>
        <th>Insumo</th>
        <th style="width:110px;">Tercero</th>
        <th class="right" style="width:75px;">Cantidad</th>
        <th class="right" style="width:70px;">Costo</th>
        <th class="right" style="width:80px;">Subtotal</th>
        @if($showSaldo)
          <th class="right" style="width:80px;">Saldo</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $m)
        @php
          $isEnt = ($m->tipo === 'ENT');

          // reales (ENT +, SAL -)
          $qtySigned = (float) $m->cantidad;
          $subSigned = (float) $m->subtotal;

          // mostrar bonito (SAL positivo visual)
          $qtyShow = $isEnt ? $qtySigned : abs($qtySigned);
          $subShow = $isEnt ? $subSigned : abs($subSigned);

          if ($showSaldo) {
            $running += $qtySigned;
          }
        @endphp
        <tr>
          <td>{{ \Illuminate\Support\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
          <td class="center">
            <span class="badge {{ $isEnt ? 'ent' : 'sal' }}">{{ $m->tipo }}</span>
          </td>
          <td><b>{{ $m->folio }}</b></td>
          <td>{{ $m->almacen_nombre ?? '—' }}</td>
          <td>{{ $m->sku }} — {{ $m->insumo_nombre }}</td>
          <td>{{ $m->tercero ?? '—' }}</td>
          <td class="right">{{ number_format($qtyShow, 3) }}</td>
          <td class="right">${{ number_format((float)$m->costo_unitario, 2) }}</td>
          <td class="right"><b>${{ number_format($subShow, 2) }}</b></td>
          @if($showSaldo)
            <td class="right"><b>{{ number_format($running, 3) }}</b></td>
          @endif
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- FOOTER --}}
  <div class="footer">
    <div style="float:left;">
      GV • Kardex • {{ config('app.name') }}
    </div>
    <div style="float:right;">
      Página <span class="pagenum"></span>
    </div>
    <div style="clear:both;"></div>
  </div>

</body>
</html>
