<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\ReporteController;

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

    // Salidas
    Route::resource('salidas', SalidaController::class)->parameters(['salidas' => 'salida']);

    // Admin (temporal)
    Route::view('/admin', 'admin.index')->name('admin.index');

    // Reportes -> manda directo a Kardex (y conserva nombre reportes.index)
    Route::redirect('/reportes', '/reportes/kardex')->name('reportes.index');

    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/kardex', [ReporteController::class, 'kardex'])->name('kardex');
        Route::get('/kardex.xlsx', [ReporteController::class, 'kardexXlsx'])->name('kardex.xlsx');
        Route::get('/kardex.pdf', [ReporteController::class, 'kardexPdf'])->name('kardex.pdf');
    });
});

require __DIR__ . '/auth.php';
