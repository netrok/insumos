@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Resumen general del sistema')

@section('content')
<div class="space-y-6">

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @php
            $kpis = [
                ['Insumos', $insumos ?? 0],
                ['Existencia total', number_format($existenciaTotal ?? 0)],
                ['Entradas (mes)', $entradasMes ?? 0],
                ['Salidas (mes)', $salidasMes ?? 0],
                ['Movimientos hoy', $movHoy ?? 0],
            ];
        @endphp

        @foreach ($kpis as [$label, $value])
            <div class="bg-white rounded-2xl border p-5">
                <div class="text-sm text-gray-500">{{ $label }}</div>
                <div class="mt-2 text-3xl font-bold tracking-tight">{{ $value }}</div>
                <div class="mt-3 h-1 w-12 rounded-full bg-gray-900/10"></div>
            </div>
        @endforeach
    </div>

    {{-- Acciones rápidas --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('entradas.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-white text-sm font-semibold hover:bg-black">
            + Nueva entrada
        </a>
        <a href="{{ route('salidas.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-white text-sm font-semibold hover:bg-black">
            + Nueva salida
        </a>
        <a href="{{ route('reportes.kardex') }}"
           class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold hover:bg-gray-50">
            Ver Kárdex
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Últimos movimientos --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-bold">Últimos movimientos</div>
                    <div class="text-sm text-gray-500">Entradas y salidas recientes</div>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-gray-500">
                        <tr class="border-b">
                            <th class="py-2 text-left font-semibold">Fecha</th>
                            <th class="py-2 text-left font-semibold">Tipo</th>
                            <th class="py-2 text-left font-semibold">Referencia</th>
                            <th class="py-2 text-left font-semibold">Insumo</th>
                            <th class="py-2 text-left font-semibold">Almacén</th>
                            <th class="py-2 text-right font-semibold">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($ultimosMovs ?? []) as $m)
                            @php
                                $mFecha = data_get($m, 'fecha');
                                $mTipo = data_get($m, 'tipo', '—');
                                $mRef = data_get($m, 'ref', '—');
                                $mInsumo = data_get($m, 'insumo', '—');
                                $mAlmacen = data_get($m, 'almacen', '—');
                                $mCantidad = (float) data_get($m, 'cantidad', 0);
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2">
                                    {{ $mFecha ? \Carbon\Carbon::parse($mFecha)->format('Y-m-d') : '—' }}
                                </td>

                                <td class="py-2">
                                    @if($mTipo === 'ENT')
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">ENT</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">SAL</span>
                                    @endif
                                </td>

                                <td class="py-2">{{ $mRef }}</td>
                                <td class="py-2 font-medium">{{ $mInsumo }}</td>
                                <td class="py-2">{{ $mAlmacen }}</td>
                                <td class="py-2 text-right font-semibold">
                                    {{ number_format($mCantidad, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">
                                    Sin movimientos recientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bajo stock --}}
        <div class="bg-white rounded-2xl border p-5">
            <div class="text-lg font-bold">Bajo stock</div>
            <div class="text-sm text-gray-500">Lo que se te va a acabar antes de que te des cuenta</div>

            <div class="mt-4 space-y-3">
                @forelse(($bajoStock ?? []) as $r)
                    @php
                        $rInsumo = data_get($r, 'insumo', '—');
                        $rAlmacen = data_get($r, 'almacen', '—');
                        $rCantidad = (float) data_get($r, 'cantidad', 0);
                        $rMinimo = (float) data_get($r, 'minimo', 0);
                    @endphp

                    <div class="rounded-xl border p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold truncate">{{ $rInsumo }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $rAlmacen }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold">{{ number_format($rCantidad, 2) }}</div>
                                <div class="text-xs text-gray-500">mín: {{ number_format($rMinimo, 2) }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border p-4 text-sm text-gray-600 bg-gray-50">
                        Bien. Nada en rojo por ahora.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
