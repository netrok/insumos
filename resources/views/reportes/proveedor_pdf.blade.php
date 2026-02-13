<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Proveedor</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .muted { color:#666; }
    .title { font-size: 16px; font-weight: bold; }
    .box { border: 1px solid #ddd; border-radius: 8px; padding: 12px; margin-top: 12px; }
    .row { margin: 6px 0; }
    .label { display:inline-block; width: 120px; color:#666; }
  </style>
</head>
<body>
  <div class="title">Proveedor</div>
  <div class="muted">Generado: {{ $generado->format('Y-m-d H:i') }}</div>

  <div class="box">
    <div class="row"><span class="label">ID:</span> {{ $p->id }}</div>
    <div class="row"><span class="label">Nombre:</span> {{ $p->nombre }}</div>
    <div class="row"><span class="label">RFC:</span> {{ $p->rfc ?? '—' }}</div>
    <div class="row"><span class="label">Teléfono:</span> {{ $p->telefono ?? '—' }}</div>
    <div class="row"><span class="label">Email:</span> {{ $p->email ?? '—' }}</div>
    <div class="row"><span class="label">Activo:</span> {{ $p->activo ? 'Sí' : 'No' }}</div>
  </div>
</body>
</html>
