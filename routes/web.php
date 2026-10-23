<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomiciliarioController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Droguería Vida Sana &bull; ERP Farmacéutico & FEFO
|--------------------------------------------------------------------------
*/

// Rutas de Autenticación y Conmutador Rápido de Roles Farmacéuticos
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login');

// Redirección principal
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Panel de control / Dashboard Farmacéutico & KPIs
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// --- MÓDULO CONTROL DE LOTES & ALGORITMO FEFO (Segundo Parcial) ---
Route::get('/lots', [LotController::class, 'index'])->name('lots.index');
Route::get('/lotes', [LotController::class, 'index'])->name('lotes.index');
Route::post('/lots/simular-fefo', [LotController::class, 'simularFefo'])->name('lots.simular-fefo');

// --- MÓDULO MEDICAMENTOS / PRODUCTOS (Con Soft Deletes y Permisos) ---
Route::patch('productos/{producto}/restore', [ProductoController::class, 'restore'])
    ->withTrashed()
    ->name('productos.restore');
Route::resource('productos', ProductoController::class);

Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
    ->withTrashed()
    ->name('products.restore');
Route::resource('products', ProductController::class)->except('show');

// --- MÓDULO CATEGORÍAS FARMACÉUTICAS ---
Route::patch('categorias/{categoria}/restore', [CategoriaController::class, 'restore'])
    ->withTrashed()
    ->name('categorias.restore');
Route::resource('categorias', CategoriaController::class);

Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
    ->withTrashed()
    ->name('categories.restore');
Route::resource('categories', CategoryController::class)->except('show');

// --- MÓDULO CLIENTES / PACIENTES ---
Route::patch('clients/{client}/restore', [ClientController::class, 'restore'])
    ->withTrashed()
    ->name('clients.restore');
Route::resource('clients', ClientController::class)->except('show');
Route::resource('clientes', ClienteController::class);

// --- MÓDULO DISPENSACIÓN Y VENTAS FARMACÉUTICAS ---
Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::resource('ventas', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

// Módulos complementarios
Route::resource('pedidos', PedidoController::class);
Route::patch('pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
Route::resource('domiciliarios', DomiciliarioController::class);
Route::resource('metodos-pago', MetodoPagoController::class);
