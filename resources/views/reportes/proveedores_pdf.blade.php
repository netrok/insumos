<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de Proveedores</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .muted { color:#666; }
    .title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
    .meta { font-size: 11px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
    th { background: #f2f2f2; text-align: left; }
    .right { text-align: right; }
  </style>
</head>
<body>
  <div class="title">Reporte de Proveedores</div>
  <div class="meta muted">
    Generado: {{ ($generado ?? now())->format('Y-m-d H:i') }}
    @if(!empty($q)) — Filtro: “{{ $q }}” @endif
    — Total: {{ isset($proveedores) ? $proveedores->count() : 0 }}
  </div>

  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>RFC</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th class="right">Activo</th>
      </tr>
    </thead>
    <tbody>
      @forelse(($proveedores ?? collect()) as $p)
        <tr>
          <td>{{ $p->nombre ?? '—' }}</td>
          <td>{{ $p->rfc ?? '—' }}</td>
          <td>{{ $p->telefono ?? '—' }}</td>
          <td>{{ $p->email ?? '—' }}</td>
          <td class="right">{{ !empty($p->activo) ? 'Sí' : 'No' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="muted">Sin proveedores para mostrar.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
