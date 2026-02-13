@extends('layouts.app')

@section('title', 'Empresa')
@section('page_title', 'Empresa')
@section('page_subtitle')
  Datos generales y logo usado en PDFs.
@endsection

@section('content')
  <x-card class="p-6">
    @if(session('status'))
      <div class="mb-4 rounded-xl border bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.empresa.update') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold">Nombre</label>
          <input class="mt-1 w-full rounded-xl border px-3 py-2" name="nombre" value="{{ old('nombre', $empresa['nombre']) }}" required>
          @error('nombre') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-semibold">RFC</label>
          <input class="mt-1 w-full rounded-xl border px-3 py-2" name="rfc" value="{{ old('rfc', $empresa['rfc']) }}">
        </div>

        <div>
          <label class="text-sm font-semibold">Teléfono</label>
          <input class="mt-1 w-full rounded-xl border px-3 py-2" name="telefono" value="{{ old('telefono', $empresa['telefono']) }}">
        </div>

        <div>
          <label class="text-sm font-semibold">Email</label>
          <input class="mt-1 w-full rounded-xl border px-3 py-2" name="email" value="{{ old('email', $empresa['email']) }}">
          @error('email') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="text-sm font-semibold">Dirección</label>
          <input class="mt-1 w-full rounded-xl border px-3 py-2" name="direccion" value="{{ old('direccion', $empresa['direccion']) }}">
        </div>
      </div>

      <div class="rounded-2xl border p-4">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="text-sm font-bold">Logo para PDFs</div>
            <div class="text-sm text-gray-600">PNG/JPG. Recomendado: fondo transparente, 300px ancho aprox.</div>

            <div class="mt-3">
              <input type="file" name="logo" accept="image/png,image/jpeg" class="block">
              @error('logo') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
            </div>

            @if(!empty($empresa['logo']))
              <label class="mt-3 inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="quitar_logo" value="1">
                Quitar logo actual
              </label>
            @endif
          </div>

          <div class="w-40 text-right">
            <div class="text-xs text-gray-500 mb-2">Vista previa</div>
            @if(!empty($empresa['logo']))
              <img src="{{ asset('storage/' . $empresa['logo']) }}" class="inline-block max-w-full rounded-xl border p-2 bg-white" alt="Logo">
            @else
              <div class="rounded-xl border bg-gray-50 p-6 text-sm text-gray-500">Sin logo</div>
            @endif
          </div>
        </div>
      </div>

      <div class="flex gap-2">
        <x-btn type="submit">Guardar</x-btn>
        <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
      </div>
    </form>
  </x-card>
@endsection
