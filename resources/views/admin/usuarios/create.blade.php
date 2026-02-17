@extends('layouts.app')

@section('title', 'Nuevo usuario')
@section('page_title', 'Nuevo usuario')

@section('page_actions')
  <x-btn variant="secondary" href="{{ route('admin.usuarios.index') }}">
    <x-icon name="arrow-left" class="h-4 w-4" />
    Volver
  </x-btn>
@endsection

@section('content')
  <x-card>
    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="p-6 space-y-5">
      @csrf

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-semibold text-gray-600">Nombre</label>
          <input name="name" value="{{ old('name') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
          @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-xs font-semibold text-gray-600">Email</label>
          <input type="email" name="email" value="{{ old('email') }}"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
          @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-xs font-semibold text-gray-600">Contraseña</label>
          <input type="password" name="password"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
          @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="text-xs font-semibold text-gray-600">Confirmar contraseña</label>
          <input type="password" name="password_confirmation"
                 class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black">
        </div>
      </div>

      <div>
        <label class="text-xs font-semibold text-gray-600">Roles</label>
        <div class="mt-2 flex flex-wrap gap-2">
          @foreach($roles as $r)
            <label class="inline-flex items-center gap-2 border rounded-xl px-3 py-2 text-sm">
              <input type="checkbox" name="roles[]" value="{{ $r->name }}"
                     @checked(in_array($r->name, old('roles', [])))>
              <span>{{ $r->name }}</span>
            </label>
          @endforeach
        </div>
        @error('roles') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <x-btn variant="secondary" href="{{ route('admin.usuarios.index') }}">Cancelar</x-btn>
        <x-btn type="submit">
          <x-icon name="check" class="h-4 w-4" />
          Guardar
        </x-btn>
      </div>
    </form>
  </x-card>
@endsection
