<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\anexoController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\HierarchiaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('hierarquia');
})->name('home');

// Hierarchical view route (main page)
Route::get('/hierarquia', [HierarchiaController::class, 'index'])->name('hierarquia');

// Empresa routes
Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
Route::get('/empresas/{empresa}', [EmpresaController::class, 'show'])->name('empresas.show');
Route::get('/empresas/{empresa}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->name('empresas.update');
Route::delete('/empresas/{empresa}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');

// Anexo routes
Route::get('/anexos', [anexoController::class, 'index'])->name('anexos.index');
Route::get('/anexos/create', [anexoController::class, 'create'])->name('anexos.create');
Route::post('/anexos', [anexoController::class, 'store'])->name('anexos.store');
Route::get('/anexos/{anexo}', [anexoController::class, 'show'])->name('anexos.show');
Route::get('/anexos/{anexo}/edit', [anexoController::class, 'edit'])->name('anexos.edit');
Route::put('/anexos/{anexo}', [anexoController::class, 'update'])->name('anexos.update');
Route::delete('/anexos/{anexo}', [anexoController::class, 'destroy'])->name('anexos.destroy');
Route::get('/anexos/{anexo}/download', [anexoController::class, 'download'])->name('anexos.download');

// Endereco routes (API-style for CRUD operations)
Route::get('/enderecos', [EnderecoController::class, 'index'])->name('enderecos.index');
Route::get('/enderecos/create', [EnderecoController::class, 'create'])->name('enderecos.create');
Route::post('/enderecos', [EnderecoController::class, 'store'])->name('enderecos.store');
Route::get('/enderecos/{endereco}', [EnderecoController::class, 'show'])->name('enderecos.show');
Route::get('/enderecos/{endereco}/edit', [EnderecoController::class, 'edit'])->name('enderecos.edit');
Route::put('/enderecos/{endereco}', [EnderecoController::class, 'update'])->name('enderecos.update');
Route::delete('/enderecos/{endereco}', [EnderecoController::class, 'destroy'])->name('enderecos.destroy');

// API routes for related data
Route::get('/api/empresas/{empresa}/anexos', [anexoController::class, 'byEmpresa'])->name('api.empresas.anexos');
Route::get('/api/enderecos/search', [EnderecoController::class, 'search'])->name('api.enderecos.search');

require __DIR__.'/settings.php';
