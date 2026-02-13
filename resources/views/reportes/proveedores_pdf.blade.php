<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de Proveedores</title>
  <style>
    @page { margin: 95px 28px 65px 28px; }

    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
    .muted { color:#6b7280; }
    .strong { font-weight: 700; }

    /* Header / Footer fijos */
    header {
      position: fixed;
      top: -75px; left: 0; right: 0;
      height: 70px;
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 10px;
    }
    footer {
      position: fixed;
      bottom: -45px; left: 0; right: 0;
      height: 40px;
      border-top: 1px solid #e5e7eb;
      padding-top: 8px;
      font-size: 10px;
    }

    .h-wrap { display: table; width: 100%; }
    .h-left  { display: table-cell; vertical-align: middle; width: 110px; }
    .h-mid   { display: table-cell; vertical-align: middle; }
    .h-right { display: table-cell; vertical-align: middle; text-align: right; width: 220px; }

    .logo {
      width: 95px;
      height: auto;
    }

    .title { font-size: 14px; font-weight: 800; margin: 0; }
    .subtitle { margin-top: 2px; font-size: 10.5px; }

    .pill {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 999px;
      border: 1px solid #e5e7eb;
      font-size: 10px;
      margin-left: 6px;
    }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #e5e7eb; padding: 7px; vertical-align: top; }
    th { background: #f3f4f6; text-align: left; font-weight: 700; }
    tr:nth-child(even) td { background: #fafafa; }
    .right { text-align: right; }
    .center { text-align: center; }

    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 10px;
      border: 1px solid #e5e7eb;
    }
    .badge-ok { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
    .badge-off { background: #f3f4f6; color: #374151; }

    .page:after { content: counter(page); }
  </style>
</head>
<body>

@php
  $generado = $generado ?? now();
  $q = $q ?? '';
  $proveedores = $proveedores ?? collect();
  $usuario = auth()->user();
  $logoPath = public_path('images/logo.png');
@endphp

<header>
  <div class="h-wrap">
    <div class="h-left">
      @if(file_exists($logoPath))
        <img class="logo" src="{{ $logoPath }}" alt="Logo">
      @endif
    </div>
    <div class="h-mid">
      <p class="title">Reporte de Proveedores</p>
      <div class="subtitle muted">
        Catálogo para compras y entradas
        <span class="pill">Total: <span class="strong">{{ $proveedores->count() }}</span></span>
        @if(!empty($q))
          <span class="pill">Filtro: <span class="strong">{{ $q }}</span></span>
        @endif
      </div>
    </div>
    <div class="h-right muted">
      <div><span class="strong">Generado:</span> {{ $generado->format('Y-m-d H:i') }}</div>
      <div><span class="strong">Usuario:</span> {{ $usuario?->name ?? '—' }}</div>
    </div>
  </div>
</header>

<footer class="muted">
  <div style="display: table; width: 100%;">
    <div style="display: table-cell;">
      Insumos • Reporte de Proveedores
    </div>
    <div style="display: table-cell; text-align: right;">
      Página <span class="page"></span>
    </div>
  </div>
</footer>

<main>
  <table>
    <thead>
      <tr>
        <th style="width: 34%;">Nombre</th>
        <th style="width: 14%;">RFC</th>
        <th style="width: 16%;">Teléfono</th>
        <th style="width: 26%;">Email</th>
        <th class="center" style="width: 10%;">Activo</th>
      </tr>
    </thead>
    <tbody>
      @forelse($proveedores as $p)
        <tr>
          <td><span class="strong">{{ $p->nombre ?? '—' }}</span></td>
          <td>{{ $p->rfc ?? '—' }}</td>
          <td>{{ $p->telefono ?? '—' }}</td>
          <td>{{ $p->email ?? '—' }}</td>
          <td class="center">
            @if(!empty($p->activo))
              <span class="badge badge-ok">Sí</span>
            @else
              <span class="badge badge-off">No</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="muted">Sin proveedores para mostrar.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</main>

</body>
</html>
