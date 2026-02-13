<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Insumos')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
<div class="min-h-screen flex">

    {{-- Sidebar (desktop) --}}
    <aside class="w-64 bg-white border-r hidden md:flex md:flex-col">
        <div class="h-16 flex items-center px-5 border-b">
            <span class="font-bold tracking-wide">INSUMOS</span>
        </div>

        <nav class="p-3 space-y-1 text-sm">
            @php
                $item = function(string $route, string $label, string $icon) {
                    $active = request()->routeIs($route);
                    return '<a href="'.route($route).'" class="flex items-center gap-2 px-3 py-2 rounded-lg '.
                        ($active ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100').
                        '"><span class="opacity-80">'.$icon.'</span><span>'.$label.'</span></a>';
                };
            @endphp

            {!! $item('dashboard', 'Dashboard', '🏠') !!}
            {!! $item('insumos.index', 'Insumos', '📦') !!}
            {!! $item('categorias.index', 'Categorías', '🧾') !!}
            {!! $item('unidades.index', 'Unidades', '📐') !!}
            {!! $item('almacenes.index', 'Almacenes', '🏬') !!}

            <div class="pt-3 mt-3 border-t space-y-1">
                {!! $item('entradas.index', 'Entradas', '📥') !!}
                {!! $item('salidas.index', 'Salidas', '📤') !!}
                {!! $item('proveedores.index', 'Proveedores', '🏷️') !!}
                {!! $item('reportes.index', 'Reportes', '📊') !!}
                {!! $item('admin.index', 'Administración', '🛡️') !!}
            </div>
        </nav>

        <div class="mt-auto p-4 border-t text-xs text-gray-500">
            <div>Gran Vía</div>
            <div>{{ date('Y') }}</div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col">

        {{-- Header --}}
        <header class="h-16 bg-white border-b flex items-center justify-between px-4 md:px-6">
            <div class="flex items-center gap-3">
                <button class="md:hidden px-3 py-2 rounded-lg border" id="btnMobileMenu">☰</button>
                <div class="font-semibold">@yield('header', 'Panel')</div>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600 hidden sm:inline">
                    {{ auth()->user()->name ?? 'Usuario' }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm hover:opacity-90">
                        Salir
                    </button>
                </form>
            </div>
        </header>

        {{-- Content --}}
        <main class="p-4 md:p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Page header --}}
            @if (trim($__env->yieldContent('page_title')))
                <div class="mb-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-semibold leading-tight">
                                @yield('page_title')
                            </h1>

                            @hasSection('page_subtitle')
                                <p class="mt-1 text-sm text-gray-600">@yield('page_subtitle')</p>
                            @endif
                        </div>

                        {{-- ✅ Renderiza HTML real en page_actions --}}
                        @hasSection('page_actions')
                            <div class="shrink-0">
                                {!! trim($__env->yieldContent('page_actions')) !!}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile sidebar --}}
<div class="md:hidden fixed inset-0 bg-black/40 hidden" id="mobileBackdrop"></div>
<div class="md:hidden fixed inset-y-0 left-0 w-64 bg-white border-r -translate-x-full transition-transform" id="mobileSidebar">
    <div class="h-16 flex items-center justify-between px-5 border-b">
        <span class="font-bold tracking-wide">INSUMOS</span>
        <button class="px-3 py-2 rounded-lg border" id="btnCloseMobile">✕</button>
    </div>

    <div class="p-3 space-y-1 text-sm">
        <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
           href="{{ route('dashboard') }}">🏠 Dashboard</a>

        <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('insumos.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
           href="{{ route('insumos.index') }}">📦 Insumos</a>

        <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('categorias.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
           href="{{ route('categorias.index') }}">🧾 Categorías</a>

        <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('unidades.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
           href="{{ route('unidades.index') }}">📐 Unidades</a>

        <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('almacenes.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
           href="{{ route('almacenes.index') }}">🏬 Almacenes</a>

        <div class="pt-3 mt-3 border-t space-y-1">
            <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('entradas.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
               href="{{ route('entradas.index') }}">📥 Entradas</a>
            <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('salidas.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
               href="{{ route('salidas.index') }}">📤 Salidas</a>
            <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('proveedores.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
               href="{{ route('proveedores.index') }}">🏷️ Proveedores</a>
            <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('reportes.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
               href="{{ route('reportes.index') }}">📊 Reportes</a>
            <a class="block px-3 py-2 rounded-lg {{ request()->routeIs('admin.index') ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' }}"
               href="{{ route('admin.index') }}">🛡️ Administración</a>
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

@if(session('error'))
  <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
    {{ session('error') }}
  </div>
@endif

@if(session('success'))
  <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
    {{ session('success') }}
  </div>
@endif
