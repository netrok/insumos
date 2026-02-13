@extends('layouts.app')

@section('title', 'Empresa')
@section('page_title', 'Empresa')
@section('page_subtitle', 'Datos corporativos para reportes y PDFs.')

@section('page_actions')
  <x-btn variant="outline" href="{{ route('admin.index') }}">Volver</x-btn>
@endsection

@section('content')
  <x-card>
    <form method="POST" action="{{ route('admin.empresa.update') }}" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold">Nombre</label>
          <input name="nombre" value="{{ old('nombre', $empresa['nombre']) }}"
                 class="mt-1 w-full rounded-xl border px-3 py-2" required>
          @error('nombre') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-sm font-semibold">RFC</label>
          <input name="rfc" value="{{ old('rfc', $empresa['rfc']) }}"
                 class="mt-1 w-full rounded-xl border px-3 py-2">
        </div>

        <div class="md:col-span-2">
          <label class="text-sm font-semibold">Dirección</label>
          <input name="direccion" value="{{ old('direccion', $empresa['direccion']) }}"
                 class="mt-1 w-full rounded-xl border px-3 py-2">
        </div>

        <div>
          <label class="text-sm font-semibold">Teléfono</label>
          <input name="telefono" value="{{ old('telefono', $empresa['telefono']) }}"
                 class="mt-1 w-full rounded-xl border px-3 py-2">
        </div>

        <div>
          <label class="text-sm font-semibold">Email</label>
          <input name="email" value="{{ old('email', $empresa['email']) }}"
                 class="mt-1 w-full rounded-xl border px-3 py-2">
        </div>

        <div class="md:col-span-2">
          <label class="text-sm font-semibold">Leyenda (pie de página)</label>
          <input name="leyenda" value="{{ old('leyenda', $empresa['leyenda']) }}"
                 class="mt-1 w-full rounded-xl border px-3 py-2">
        </div>
      </div>

      <div class="rounded-2xl border p-4">
        <div class="flex items-center justify-between gap-4">
          <div>
            <div class="font-semibold">Logo</div>
            <div class="text-sm text-gray-600">PNG/JPG/WEBP (máx 2MB). Se usa en PDFs.</div>
          </div>

          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="logo_on" value="1" {{ old('logo_on', $empresa['logo_on']) ? 'checked' : '' }}>
            Mostrar logo en reportes
          </label>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <input type="file" name="logo" class="w-full">
            @error('logo') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="flex items-center gap-3">
            @if($empresa['logo_path'])
              <img src="{{ asset('storage/'.$empresa['logo_path']) }}" class="h-14 rounded-xl border bg-white p-1" alt="logo">
              <label class="inline-flex items-center gap-2 text-sm text-rose-700">
                <input type="checkbox" name="remove_logo" value="1">
                Quitar logo actual
              </label>
            @else
              <div class="text-sm text-gray-600">Sin logo cargado.</div>
            @endif
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-2">
        <x-btn type="submit">Guardar</x-btn>
      </div>
    </form>
  </x-card>
@endsection
