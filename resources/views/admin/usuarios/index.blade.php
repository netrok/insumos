@extends('layouts.app')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios')

@section('page_subtitle')
  Gestión de usuarios del sistema y asignación de roles.
@endsection

@section('page_actions')
  <x-btn href="{{ route('admin.usuarios.create') }}">
    <x-icon name="plus" class="h-4 w-4" />
    Nuevo usuario
  </x-btn>
@endsection

@section('content')
  <x-card>

    <div class="p-4 border-b bg-white">
      <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
          <label class="text-xs font-semibold text-gray-600">Buscar</label>
          <input
            type="text"
            name="q"
            value="{{ $q }}"
            placeholder="Nombre o correo…"
            class="mt-1 w-full rounded-xl border-gray-300 focus:border-gv-black focus:ring-gv-black"
          >
        </div>

        <div class="flex gap-2">
          <x-btn variant="soft" type="submit">
            <x-icon name="search" class="h-4 w-4" />
            Filtrar
          </x-btn>

          @if($q)
            <x-btn variant="secondary" href="{{ route('admin.usuarios.index') }}">
              <x-icon name="x" class="h-4 w-4" />
              Limpiar
            </x-btn>
          @endif
        </div>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Nombre</th>
            <th class="text-left font-medium px-4 py-3">Email</th>
            <th class="text-left font-medium px-4 py-3">Roles</th>
            <th class="text-right font-medium px-4 py-3 w-48">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($users as $u)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
              <td class="px-4 py-3">{{ $u->email }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  @forelse($u->roles as $r)
                    <x-badge variant="gold">{{ $r->name }}</x-badge>
                  @empty
                    <span class="text-gray-500">—</span>
                  @endforelse
                </div>
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <x-btn variant="outline" iconOnly href="{{ route('admin.usuarios.edit', $u) }}" title="Editar">
                    <x-icon name="edit" class="h-4 w-4" />
                  </x-btn>

                  <form method="POST" action="{{ route('admin.usuarios.destroy', $u) }}"
                        onsubmit="return confirm('¿Eliminar usuario {{ $u->email }}?');">
                    @csrf
                    @method('DELETE')
                    <x-btn variant="danger" iconOnly type="submit" title="Eliminar">
                      <x-icon name="trash" class="h-4 w-4" />
                    </x-btn>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                No hay usuarios.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t">
      {{ $users->links() }}
    </div>

  </x-card>
@endsection
