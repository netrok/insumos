<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ProveedorController;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catálogos
    Route::resource('categorias', CategoriaController::class)->parameters(['categorias' => 'categoria']);
    Route::resource('unidades', UnidadController::class)->parameters(['unidades' => 'unidad']);
    Route::resource('almacenes', AlmacenController::class)->parameters(['almacenes' => 'almacen']);

    // Insumos
    Route::resource('insumos', InsumoController::class)->parameters(['insumos' => 'insumo']);

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->parameters(['proveedores' => 'proveedor']);

    // Entradas
    Route::resource('entradas', EntradaController::class)->parameters(['entradas' => 'entrada']);

    // (Temporal) módulos pendientes
    Route::view('/salidas', 'salidas.index')->name('salidas.index');
    Route::view('/reportes', 'reportes.index')->name('reportes.index');
    Route::view('/admin', 'admin.index')->name('admin.index');
});

require __DIR__ . '/auth.php';
