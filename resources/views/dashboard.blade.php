@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Resumen general del sistema')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ([
        ['Insumos', '—'],
        ['Entradas (mes)', '—'],
        ['Salidas (mes)', '—'],
    ] as $kpi)
        <div class="bg-white rounded-2xl border p-5">
            <div class="text-sm text-gray-500">{{ $kpi[0] }}</div>
            <div class="mt-2 text-3xl font-bold tracking-tight">{{ $kpi[1] }}</div>
        </div>
    @endforeach
</div>
@endsection
