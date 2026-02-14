<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Insumos')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900">
<div class="min-h-screen flex">

  {{-- Sidebar (desktop) --}}
  <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex md:flex-col">
    <div class="h-16 flex items-center px-5 border-b border-slate-200">
      <div class="flex items-center gap-2">
        <div class="h-9 w-9 rounded-xl bg-slate-900 text-white grid place-items-center font-black">GV</div>
        <div>
          <div class="text-sm font-extrabold tracking-wide">INSUMOS</div>
          <div class="text-xs text-slate-500 -mt-0.5">Panel interno</div>
        </div>
      </div>
    </div>

    <nav class="p-3 space-y-1 text-sm">
      @php
        $item = function(string $route, string $label, string $icon) {
          $active = request()->routeIs($route);
          return '<a href="'.route($route).'" class="group flex items-center gap-2 px-3 py-2.5 rounded-xl border transition '.
              ($active
                ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                : 'bg-white text-slate-700 border-transparent hover:bg-slate-50 hover:border-slate-200'
              ).
              '"><span class="opacity-80">'.$icon.'</span><span class="font-semibold">'.$label.'</span></a>';
        };
      @endphp

      {!! $item('dashboard', 'Dashboard', '🏠') !!}
      {!! $item('insumos.index', 'Insumos', '📦') !!}
      {!! $item('categorias.index', 'Categorías', '🧾') !!}
      {!! $item('unidades.index', 'Unidades', '📐') !!}
      {!! $item('almacenes.index', 'Almacenes', '🏬') !!}

      <div class="pt-3 mt-3 border-t border-slate-200 space-y-1">
        {!! $item('entradas.index', 'Entradas', '📥') !!}
        {!! $item('salidas.index', 'Salidas', '📤') !!}
        {!! $item('proveedores.index', 'Proveedores', '🏷️') !!}
        {!! $item('reportes.index', 'Reportes', '📊') !!}
        {!! $item('admin.index', 'Administración', '🛡️') !!}
      </div>
    </nav>

    <div class="mt-auto p-4 border-t border-slate-200 text-xs text-slate-500">
      <div class="font-semibold text-slate-700">Gran Vía</div>
      <div>{{ date('Y') }}</div>
    </div>
  </aside>

  {{-- Main --}}
  <div class="flex-1 flex flex-col">

    {{-- Header --}}
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6">
      <div class="flex items-center gap-3">
        <button class="md:hidden px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50" id="btnMobileMenu">☰</button>
        <div class="font-semibold text-slate-900">@yield('header', 'Panel')</div>
      </div>

      <div class="flex items-center gap-3">
        <span class="hidden sm:inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
          {{ auth()->user()->name ?? 'Usuario' }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold shadow-sm transition hover:bg-slate-800">
            Salir
          </button>
        </form>
      </div>
    </header>

    {{-- Content --}}
    <main class="flex-1">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">

        {{-- Alerts --}}
        @if (session('success'))
          <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
            <div class="text-sm font-semibold">Listo.</div>
            <div class="text-sm">{{ session('success') }}</div>
          </div>
        @endif

        @if (session('error'))
          <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900">
            <div class="text-sm font-semibold">Ojo.</div>
            <div class="text-sm">{{ session('error') }}</div>
          </div>
        @endif

        {{-- Page header --}}
        @if (trim($__env->yieldContent('page_title')))
          <div class="mb-6">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">
                  @yield('page_title')
                </h1>

                @hasSection('page_subtitle')
                  <p class="mt-2 text-slate-600 max-w-2xl">@yield('page_subtitle')</p>
                @endif
              </div>

              @hasSection('page_actions')
                <div class="shrink-0">
                  {!! trim($__env->yieldContent('page_actions')) !!}
                </div>
              @endif
            </div>
          </div>
        @endif

        @yield('content')
      </div>
    </main>
  </div>
</div>

{{-- Mobile sidebar --}}
<div class="md:hidden fixed inset-0 bg-black/40 hidden" id="mobileBackdrop"></div>
<div class="md:hidden fixed inset-y-0 left-0 w-72 bg-white border-r border-slate-200 -translate-x-full transition-transform" id="mobileSidebar">
  <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200">
    <div class="flex items-center gap-2">
      <div class="h-9 w-9 rounded-xl bg-slate-900 text-white grid place-items-center font-black">GV</div>
      <div class="font-extrabold tracking-wide">INSUMOS</div>
    </div>
    <button class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50" id="btnCloseMobile">✕</button>
  </div>

  <div class="p-3 space-y-1 text-sm">
    @php
      $m = fn($route,$label,$icon) =>
        '<a class="block px-3 py-2.5 rounded-xl border transition '.
        (request()->routeIs($route) ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-transparent hover:bg-slate-50 hover:border-slate-200').
        '" href="'.route($route).'">'.$icon.' <span class="font-semibold">'.$label.'</span></a>';
    @endphp

    {!! $m('dashboard','Dashboard','🏠') !!}
    {!! $m('insumos.index','Insumos','📦') !!}
    {!! $m('categorias.index','Categorías','🧾') !!}
    {!! $m('unidades.index','Unidades','📐') !!}
    {!! $m('almacenes.index','Almacenes','🏬') !!}

    <div class="pt-3 mt-3 border-t border-slate-200 space-y-1">
      {!! $m('entradas.index','Entradas','📥') !!}
      {!! $m('salidas.index','Salidas','📤') !!}
      {!! $m('proveedores.index','Proveedores','🏷️') !!}
      {!! $m('reportes.index','Reportes','📊') !!}
      {!! $m('admin.index','Administración','🛡️') !!}
    </div>
  </div>
</div>

<script>
(function () {
  const btn = document.getElementById('btnMobileMenu');
  const close = document.getElementById('btnCloseMobile');
  const sidebar = document.getElementById('mobileSidebar');
  const backdrop = document.getElementById('mobileBackdrop');

  const open = () => {
    sidebar.classList.remove('-translate-x-full');
    backdrop.classList.remove('hidden');
  };

  const hide = () => {
    sidebar.classList.add('-translate-x-full');
    backdrop.classList.add('hidden');
  };

  btn?.addEventListener('click', open);
  close?.addEventListener('click', hide);
  backdrop?.addEventListener('click', hide);
})();
</script>
</body>
</html>
