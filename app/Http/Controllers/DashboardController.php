<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Lot;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal de Droguería Vida Sana con KPIs farmacéuticos y control de lotes FEFO.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $roles = $user ? $user->getRoleNames() : collect();

        // 1. Indicadores Clave de Desempeño Farmacéutico (KPIs)
        $totalMedicamentos = Product::where('active', true)->count();
        $totalLotes = Lot::count();
        $lotesVigentes = Lot::vigentes()->where('quantity', '>', 0)->count();
        $lotesAlerta = Lot::vigentes()->where('quantity', '>', 0)->whereDate('expiration_date', '<=', now()->addDays(90))->count();
        $lotesVencidos = Lot::vencidos()->count();
        $valorInventario = (float) Product::where('active', true)->sum(DB::raw('price * stock'));

        // 2. Módulos dinámicos según permisos del usuario
        $modules = collect([
            [
                'title' => 'Catálogo de Medicamentos',
                'description' => 'Consulta y administra el inventario farmacéutico, precios e indicaciones.',
                'route' => 'productos.index',
                'permission' => 'ver-productos',
                'icon' => 'fa-pills',
                'badge' => "{$totalMedicamentos} productos",
            ],
            [
                'title' => 'Control de Lotes & FEFO',
                'description' => 'Supervisa lotes de fabricación, fechas de caducidad y orden de despacho.',
                'route' => 'lots.index',
                'permission' => 'ver-productos',
                'icon' => 'fa-boxes-stacked',
                'badge' => "{$lotesVigentes} vigentes",
            ],
            [
                'title' => 'Categorías Farmacéuticas',
                'description' => 'Organiza productos: Venta Libre, Con Fórmula Médica, Control Especial.',
                'route' => 'categorias.index',
                'permission' => 'ver-categorias',
                'icon' => 'fa-tags',
                'badge' => Category::count() . ' categorías',
            ],
            [
                'title' => 'Dispensación & Ventas',
                'description' => 'Registra ventas y salidas con descuento automático según algoritmo FEFO.',
                'route' => 'sales.index',
                'permission' => 'ver-ventas',
                'icon' => 'fa-cash-register',
                'badge' => Sale::count() . ' facturas',
            ],
            [
                'title' => 'Pacientes / Clientes',
                'description' => 'Directorio de pacientes, prescripciones y tratamientos continuos.',
                'route' => 'clients.index',
                'permission' => 'ver-clientes',
                'icon' => 'fa-users',
                'badge' => Client::count() . ' pacientes',
            ],
        ])->filter(function (array $module) use ($user) {
            return !$user || $user->hasRole('admin') || $user->can($module['permission']);
        })->values();

        // 3. Lotes prioritarios para despacho FEFO (Próximos a vencer)
        $lotesFefo = Lot::with('product')
            ->orderBy('expiration_date', 'asc')
            ->take(8)
            ->get();

        // 4. Productos para simulación rápida de FEFO
        $productsParaSimulacion = Product::with('lots')->where('active', true)->get();

        // 5. Usuarios disponibles para conmutador rápido de roles
        $usuariosPrueba = User::with('roles')->get();

        return view('dashboard', compact(
            'user',
            'roles',
            'modules',
            'totalMedicamentos',
            'totalLotes',
            'lotesVigentes',
            'lotesAlerta',
            'lotesVencidos',
            'valorInventario',
            'lotesFefo',
            'productsParaSimulacion',
            'usuariosPrueba'
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
