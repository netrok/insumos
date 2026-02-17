<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'ilike', "%{$q}%")
                      ->orWhere('email', 'ilike', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', compact('users', 'q'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:120'],
            'email'                 => ['required', 'email', 'max:180', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'roles'                 => ['nullable', 'array'],
            'roles.*'               => ['string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['roles'] ?? []);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.usuarios.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:120'],
            'email'                 => ['required', 'email', 'max:180', 'unique:users,email,' . $user->id],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles'                 => ['nullable', 'array'],
            'roles.*'               => ['string', 'exists:roles,name'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $user->syncRoles($data['roles'] ?? []);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        // Evita suicidio administrativo 😄
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
