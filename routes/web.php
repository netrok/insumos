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
use App\Http\Controllers\ReporteProveedorController;
use App\Http\Controllers\DashboardController;

// Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\FolioController;
use App\Http\Controllers\Admin\ParametroController;
use App\Http\Controllers\Admin\AuditoriaMovController;

// Raíz: manda al dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

// Dashboard (auth + verified)
Route::get('/dashboard', [DashboardController::class, 'index'])
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

    // Reportes (index manda a Kardex)
    Route::redirect('/reportes', '/reportes/kardex')->name('reportes.index');

    Route::prefix('reportes')->name('reportes.')->group(function () {

        // Kardex
        Route::get('/kardex', [ReporteController::class, 'kardex'])->name('kardex');
        Route::get('/kardex.xlsx', [ReporteController::class, 'kardexXlsx'])->name('kardex.xlsx');
        Route::get('/kardex.pdf', [ReporteController::class, 'kardexPdf'])->name('kardex.pdf');

        // Proveedores (lista)
        Route::get('/proveedores.xlsx', [ReporteProveedorController::class, 'listaXlsx'])->name('proveedores.xlsx');
        Route::get('/proveedores.pdf',  [ReporteProveedorController::class, 'listaPdf'])->name('proveedores.pdf');

        // Proveedor específico
        Route::get('/proveedores/{proveedor}.xlsx', [ReporteProveedorController::class, 'proveedorXlsx'])->name('proveedores.proveedor.xlsx');
        Route::get('/proveedores/{proveedor}.pdf',  [ReporteProveedorController::class, 'proveedorPdf'])->name('proveedores.proveedor.pdf');
    });

    /**
     * ADMIN — protegido
     * Nota: tu rol real es "ADMIN" (mayúsculas), por eso aquí va así.
     */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['role:ADMIN'])
        ->group(function () {

            Route::get('/', [AdminController::class, 'index'])->name('index');

            // Empresa
            Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
            Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

            // Folios
            Route::get('/folios', [FolioController::class, 'index'])->name('folios.index');
            Route::put('/folios', [FolioController::class, 'update'])->name('folios.update');

            // Parámetros
            Route::get('/parametros', [ParametroController::class, 'index'])->name('parametros.index');
            Route::put('/parametros', [ParametroController::class, 'update'])->name('parametros.update');

            // Auditoría
            Route::get('/auditoria/movimientos', [AuditoriaMovController::class, 'index'])->name('auditoria.movimientos');
        });
});

require __DIR__ . '/auth.php';
