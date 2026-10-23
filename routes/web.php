<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomiciliarioController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - QuickFood ERP (Clase 7 & Clase 9: Integración de Módulos y KPIs)
|--------------------------------------------------------------------------
*/

// Rutas de Autenticación y Conmutador Rápido de Roles (Paso 11 de la Guía)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login');

// Redirección principal: si no está logueado va a login, si está logueado va a dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Panel de control / Dashboard con KPIs (Paso 12 de la Guía)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// --- MÓDULO PRODUCTOS (Con Soft Deletes y Permisos) ---
Route::patch('productos/{producto}/restore', [ProductoController::class, 'restore'])
    ->withTrashed()
    ->name('productos.restore');
Route::resource('productos', ProductoController::class);

// Alias en inglés para compatibilidad con la guía
Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
    ->withTrashed()
    ->name('products.restore');
Route::resource('products', ProductController::class)->except('show');

// --- MÓDULO CATEGORÍAS (Con Soft Deletes y Permisos) ---
Route::patch('categorias/{categoria}/restore', [CategoriaController::class, 'restore'])
    ->withTrashed()
    ->name('categorias.restore');
Route::resource('categorias', CategoriaController::class);

// Alias en inglés para categorías
Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
    ->withTrashed()
    ->name('categories.restore');
Route::resource('categories', CategoryController::class)->except('show');

// --- MÓDULO CLIENTES (Clase 9: Soft Deletes y Permisos) ---
Route::patch('clients/{client}/restore', [ClientController::class, 'restore'])
    ->withTrashed()
    ->name('clients.restore');
Route::resource('clients', ClientController::class)->except('show');

// --- MÓDULO VENTAS (Clase 9: Transacciones, Descuento de Stock y Facturas) ---
Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::resource('ventas', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

// --- OTROS MÓDULOS DEL ERP QUICKFOOD ---
Route::resource('pedidos', PedidoController::class);
Route::patch('pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
Route::resource('clientes', ClienteController::class);
Route::resource('domiciliarios', DomiciliarioController::class);
Route::resource('metodos-pago', MetodoPagoController::class);
