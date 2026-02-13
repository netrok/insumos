<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Proveedor</title>
  <style>
    @page { margin: 95px 28px 65px 28px; }

    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
    .muted { color:#6b7280; }
    .strong { font-weight: 700; }

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

    .logo { width: 95px; height: auto; }
    .title { font-size: 14px; font-weight: 800; margin: 0; }
    .subtitle { margin-top: 2px; font-size: 10.5px; }

    .card {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 12px;
      margin-top: 10px;
      background: #fff;
    }

    .grid { width: 100%; border-collapse: collapse; }
    .grid td { padding: 8px; border-bottom: 1px solid #f1f5f9; }
    .grid td:first-child { width: 140px; color: #6b7280; }
    .grid tr:last-child td { border-bottom: none; }

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
  $p = $p ?? null;
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
      <p class="title">Ficha de Proveedor</p>
      <div class="subtitle muted">
        {{ $p?->nombre ?? 'Proveedor' }}
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
      Insumos • Ficha de Proveedor
    </div>
    <div style="display: table-cell; text-align: right;">
      Página <span class="page"></span>
    </div>
  </div>
</footer>

<main>
  <div class="card">
    <table class="grid">
      <tr>
        <td>ID</td>
        <td class="strong">{{ $p?->id ?? '—' }}</td>
      </tr>
      <tr>
        <td>Nombre</td>
        <td class="strong">{{ $p?->nombre ?? '—' }}</td>
      </tr>
      <tr>
        <td>RFC</td>
        <td>{{ $p?->rfc ?? '—' }}</td>
      </tr>
      <tr>
        <td>Teléfono</td>
        <td>{{ $p?->telefono ?? '—' }}</td>
      </tr>
      <tr>
        <td>Email</td>
        <td>{{ $p?->email ?? '—' }}</td>
      </tr>
      <tr>
        <td>Activo</td>
        <td>
          @if(!empty($p?->activo))
            <span class="badge badge-ok">Sí</span>
          @else
            <span class="badge badge-off">No</span>
          @endif
        </td>
      </tr>
      <tr>
        <td>Creado</td>
        <td class="muted">{{ optional($p?->created_at)->format('Y-m-d H:i') ?? '—' }}</td>
      </tr>
      <tr>
        <td>Actualizado</td>
        <td class="muted">{{ optional($p?->updated_at)->format('Y-m-d H:i') ?? '—' }}</td>
      </tr>
    </table>
  </div>
</main>

</body>
</html>
