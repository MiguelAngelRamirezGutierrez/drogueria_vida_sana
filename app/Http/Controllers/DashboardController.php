<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Category;
use App\Models\Client;
use App\Models\Cliente;
use App\Models\Domiciliario;
use App\Models\Pedido;
use App\Models\Product;
use App\Models\Producto;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal del ERP con indicadores clave (KPIs) y módulos según roles y permisos.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $roles = $user ? $user->getRoleNames() : collect();

        // 1. Módulos dinámicos según permisos del usuario (Clase 7 & Clase 9)
        $modules = collect([
            [
                'title' => 'Productos',
                'description' => ($user && $user->can('crear-productos'))
                    ? 'Consulta y administra el inventario de platos y alimentos.'
                    : 'Consulta el catálogo y precios de productos.',
                'route' => 'productos.index',
                'permission' => 'ver-productos',
                'icon' => 'fa-burger',
            ],
            [
                'title' => 'Categorías',
                'description' => ($user && $user->can('crear-categorias'))
                    ? 'Organiza los productos por categorías gastronómicas.'
                    : 'Consulta las categorías del menú.',
                'route' => 'categorias.index',
                'permission' => 'ver-categorias',
                'icon' => 'fa-tags',
            ],
            [
                'title' => 'Clientes',
                'description' => ($user && $user->can('crear-clientes'))
                    ? 'Administra el directorio de clientes y facturación.'
                    : 'Consulta el directorio de clientes.',
                'route' => 'clients.index',
                'permission' => 'ver-clientes',
                'icon' => 'fa-users',
            ],
            [
                'title' => 'Ventas & Facturación',
                'description' => ($user && $user->can('crear-ventas'))
                    ? 'Registra ventas con descuento automático de stock e IVA.'
                    : 'Consulta el histórico de facturas y ventas.',
                'route' => 'sales.index',
                'permission' => 'ver-ventas',
                'icon' => 'fa-cash-register',
            ],
            [
                'title' => 'Pedidos Cocina',
                'description' => ($user && $user->can('crear-pedidos'))
                    ? 'Gestiona órdenes de cocina, comandas y despachos.'
                    : 'Consulta los pedidos en preparación.',
                'route' => 'pedidos.index',
                'permission' => 'ver-pedidos',
                'icon' => 'fa-clipboard-list',
            ],
            [
                'title' => 'Domiciliarios',
                'description' => 'Supervisa la flota de reparto y vehículos.',
                'route' => 'domiciliarios.index',
                'permission' => 'ver-domiciliarios',
                'icon' => 'fa-motorcycle',
            ],
        ])->filter(function (array $module) use ($user) {
            return !$user || $user->hasRole('admin') || $user->can($module['permission']);
        })->values();

        // 2. Indicadores Clave de Desempeño (KPIs - Paso 12 Guía Clase 9)
        $stats = collect();

        if (!$user || $user->can('ver-productos') || $user->hasRole('admin')) {
            $stats->push([
                'label' => 'Productos activos',
                'value' => Product::where('active', true)->count(),
                'icon' => 'fa-box',
                'color' => 'blue',
            ]);
            $stats->push([
                'label' => 'Stock bajo (≤ 5)',
                'value' => Product::where('active', true)->where('stock', '<=', 5)->count(),
                'icon' => 'fa-triangle-exclamation',
                'color' => 'amber',
            ]);
            $stats->push([
                'label' => 'Valor del inventario',
                'value' => '$ ' . number_format((float) Product::where('active', true)->sum(DB::raw('price * stock')), 0, ',', '.'),
                'icon' => 'fa-vault',
                'color' => 'emerald',
            ]);
        }

        if (!$user || $user->can('ver-clientes') || $user->hasRole('admin')) {
            $stats->push([
                'label' => 'Clientes activos',
                'value' => Client::where('active', true)->count(),
                'icon' => 'fa-users',
                'color' => 'indigo',
            ]);
        }

        if (!$user || $user->can('ver-ventas') || $user->hasRole('admin')) {
            $salesThisMonth = Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->where('status', '!=', 'anulada');

            $countSales = $salesThisMonth->count();
            $sumSales = (float) $salesThisMonth->sum('total');

            $stats->push([
                'label' => 'Ventas del mes',
                'value' => $countSales . ' ($ ' . number_format($sumSales, 0, ',', '.') . ')',
                'icon' => 'fa-chart-line',
                'color' => 'purple',
            ]);
        }

        // 3. Top 5 productos más vendidos del mes actual (Paso 12 Guía Clase 9)
        $topProducts = collect();
        if (!$user || $user->can('ver-reportes') || $user->can('ver-ventas') || $user->hasRole('admin')) {
            $topProducts = SaleDetail::query()
                ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->whereHas('sale', function ($q) {
                    $q->whereMonth('sale_date', now()->month)
                      ->whereYear('sale_date', now()->year)
                      ->where('status', '!=', 'anulada');
                })
                ->with('product:id,nombre,name,precio,price')
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        }

        // 4. Métricas operativas complementarias del ERP Dark Kitchen
        $pedidosHoy = Pedido::hoy()->count();
        $pedidosPendientes = Pedido::pendientes()->count();
        $pedidosEntregados = Pedido::entregados()->count();
        $ventasHoy = Pedido::hoy()->where('estado', '!=', 'Cancelado')->sum('total');

        $pedidosRecientes = Pedido::with(['cliente', 'domiciliario', 'metodoPago'])
            ->latest()
            ->take(6)
            ->get();

        $estadosConteo = [
            'Recibido' => Pedido::where('estado', 'Recibido')->count(),
            'Preparando' => Pedido::where('estado', 'Preparando')->count(),
            'Listo' => Pedido::where('estado', 'Listo')->count(),
            'En camino' => Pedido::where('estado', 'En camino')->count(),
            'Entregado' => Pedido::where('estado', 'Entregado')->count(),
            'Cancelado' => Pedido::where('estado', 'Cancelado')->count(),
        ];

        return view('dashboard', compact(
            'user',
            'roles',
            'modules',
            'stats',
            'topProducts',
            'pedidosHoy',
            'pedidosPendientes',
            'pedidosEntregados',
            'ventasHoy',
            'pedidosRecientes',
            'estadosConteo'
        ));
    }

    /**
     * Soporte para invocación directa como Action invokable.
     */
    public function __invoke(Request $request)
    {
        return $this->index($request);
    }
}
