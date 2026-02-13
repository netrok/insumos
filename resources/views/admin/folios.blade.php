@extends('layouts.app')

@section('title', 'Folios')
@section('page_title', 'Folios')
@section('page_subtitle')
  Prefijos y consecutivos por módulo.
@endsection

@section('content')
  <x-card class="p-6">
    @if(session('status'))
      <div class="mb-4 rounded-xl border bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.folios.update') }}" class="space-y-6">
      @csrf
      @method('PUT')

      @php
        $rows = [
          ['Entradas','entradas_prefix','entradas_next'],
          ['Salidas','salidas_prefix','salidas_next'],
          ['Ajustes','ajustes_prefix','ajustes_next'],
          ['Traspasos','traspasos_prefix','traspasos_next'],
        ];
      @endphp

      <div class="grid grid-cols-1 gap-4">
        @foreach($rows as [$label,$kPrefix,$kNext])
          <div class="rounded-2xl border p-4">
            <div class="font-bold">{{ $label }}</div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-semibold">Prefijo</label>
                <input class="mt-1 w-full rounded-xl border px-3 py-2"
                       name="{{ $kPrefix }}"
                       value="{{ old($kPrefix, $folios[$kPrefix]) }}"
                       required>
              </div>
              <div>
                <label class="text-sm font-semibold">Siguiente</label>
                <input type="number" min="1" class="mt-1 w-full rounded-xl border px-3 py-2"
                       name="{{ $kNext }}"
                       value="{{ old($kNext, $folios[$kNext]) }}"
                       required>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex gap-2">
        <x-btn type="submit">Guardar</x-btn>
        <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
      </div>
    </form>
  </x-card>
@endsection
