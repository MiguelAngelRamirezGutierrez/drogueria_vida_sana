@extends('layouts.app')

@section('title', 'Dashboard Operativo & KPIs')
@section('page_title', 'Panel de Control Operativo - QuickFood ERP')

@section('content')
<div class="space-y-6">

    <!-- Tarjeta de Bienvenida con Roles y Permisos (Paso 10 de la Guía) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Bienvenido, {{ $user?->name ?? 'Usuario del Sistema' }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Rol asignado: 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $roles->contains('admin') ? 'bg-orange-100 text-orange-800' : ($roles->contains('almacenista') ? 'bg-purple-100 text-purple-800' : ($roles->contains('vendedor') ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700')) }}">
                        <i class="fa-solid fa-id-badge mr-1"></i>
                        {{ $roles->isNotEmpty() ? $roles->join(', ') : 'Sin rol asignado' }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 transition">
                    <i class="fa-solid fa-users-gear text-slate-500"></i>
                    <span>Cambiar Rol / Probar Usuario</span>
                </a>
            </div>
        </div>

        <!-- Indicadores Clave de Desempeño (KPIs - Paso 12 Guía Clase 9) -->
        @if ($stats->isNotEmpty())
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center space-x-2">
                    <i class="fa-solid fa-chart-pie text-orange-500"></i>
                    <span>Indicadores Clave de Desempeño (KPIs del Sistema)</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach ($stats as $stat)
                        <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 hover:bg-slate-50 transition border-l-4 border-l-orange-500 shadow-sm">
                            <p class="text-xs font-semibold text-slate-500">{{ $stat['label'] }}</p>
                            <p class="text-lg font-black text-slate-800 mt-1">{{ $stat['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Módulos habilitados para tu rol</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse ($modules as $module)
                    <a href="{{ route($module['route']) }}" class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 hover:border-orange-300 hover:bg-orange-50/30 transition group shadow-sm bg-white">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-600 group-hover:text-white transition">
                            <i class="fa-solid {{ $module['icon'] ?? 'fa-folder' }} text-base"></i>
                        </div>
                        <div class="truncate">
                            <span class="text-sm font-bold text-slate-800 group-hover:text-orange-600 transition block truncate">{{ $module['title'] }}</span>
                            <span class="text-xs text-slate-500 truncate block">{{ $module['description'] }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 italic">No tienes módulos disponibles. Pide a un administrador que te asigne un rol.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top 5 Productos más Vendidos del Mes (Paso 12 Guía Clase 9) -->
    @if ($topProducts->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Top 5 Productos más Vendidos este Mes</h3>
                        <p class="text-xs text-slate-500">Indicador clave de rotación comercial y preferencia de clientes</p>
                    </div>
                </div>
                <span class="text-xs font-semibold bg-orange-50 text-orange-700 px-3 py-1 rounded-full border border-orange-200">
                    {{ now()->isoFormat('MMMM YYYY') }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 font-semibold border-b border-slate-100">
                            <th class="pb-3 w-16 text-center">Posición</th>
                            <th class="pb-3">Producto</th>
                            <th class="pb-3 text-right">Precio Unitario</th>
                            <th class="pb-3 text-right">Unidades Vendidas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($topProducts as $i => $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 text-center">
                                    @if($i === 0)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">🥇 1°</span>
                                    @elseif($i === 1)
                                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">🥈 2°</span>
                                    @elseif($i === 2)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-900 text-xs font-bold">🥉 3°</span>
                                    @else
                                        <span class="text-slate-400 font-mono text-xs">{{ $i + 1 }}°</span>
                                    @endif
                                </td>
                                <td class="py-3 font-semibold text-slate-900">
                                    {{ $item->product?->name ?? 'Producto #' . $item->product_id }}
                                </td>
                                <td class="py-3 text-right font-mono text-xs text-slate-500">
                                    ${{ number_format((float) ($item->product?->price ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                        {{ $item->total_sold }} unidades
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tarjetas de Métricas Principales de Pedidos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pedidos Hoy -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pedidos Hoy</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $pedidosHoy }}</h3>
                <span class="text-[11px] text-slate-400">Total registrados hoy</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">En Operación</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $pedidosPendientes }}</h3>
                <span class="text-[11px] text-slate-400">En cocina o en ruta</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-fire-burner"></i>
            </div>
        </div>

        <!-- Pedidos Entregados -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Entregados</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $pedidosEntregados }}</h3>
                <span class="text-[11px] text-slate-400">Servicios finalizados</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Ventas Hoy -->
        <div class="bg-gradient-to-br from-orange-600 to-amber-500 text-white p-5 rounded-2xl shadow-md flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-orange-100">Ventas Hoy</p>
                <h3 class="text-2xl font-black mt-1">${{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                <span class="text-[11px] text-orange-100">Ingresos del día</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center text-xl">
                <i class="fa-solid fa-cash-register"></i>
            </div>
        </div>
    </div>

    <!-- Flujo del Negocio y Estados -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Flujo Operativo de Pedidos & Domicilios</h2>
                <p class="text-xs text-slate-500">Ciclo continuo de atención: Cliente ➔ Pedido ➔ Preparación ➔ Listo ➔ Domicilio ➔ Entrega</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-orange-100 text-orange-800 rounded-md">Dark Kitchen Workflow</span>
        </div>

        <!-- Estados en cadena -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <!-- Recibido -->
            <a href="{{ route('pedidos.index', ['estado' => 'Recibido']) }}" class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 hover:bg-blue-100/60 transition group text-center">
                <div class="text-xs font-bold uppercase tracking-wider text-blue-700 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span>1. Recibido</span>
                </div>
                <div class="text-xl font-black text-blue-900 mt-1">{{ $estadosConteo['Recibido'] }}</div>
                <div class="text-[11px] text-blue-600 mt-0.5">En cola cocina</div>
            </a>

            <!-- Preparando -->
            <a href="{{ route('pedidos.index', ['estado' => 'Preparando']) }}" class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/50 hover:bg-amber-100/60 transition group text-center">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-700 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-fire text-xs"></i>
                    <span>2. Preparando</span>
                </div>
                <div class="text-xl font-black text-amber-900 mt-1">{{ $estadosConteo['Preparando'] }}</div>
                <div class="text-[11px] text-amber-600 mt-0.5">En plancha / freidora</div>
            </a>

            <!-- Listo -->
            <a href="{{ route('pedidos.index', ['estado' => 'Listo']) }}" class="p-3.5 rounded-xl border border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100/60 transition group text-center">
                <div class="text-xs font-bold uppercase tracking-wider text-indigo-700 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-check-double text-xs"></i>
                    <span>3. Listo</span>
                </div>
                <div class="text-xl font-black text-indigo-900 mt-1">{{ $estadosConteo['Listo'] }}</div>
                <div class="text-[11px] text-indigo-600 mt-0.5">Empacado en barra</div>
            </a>

            <!-- En camino -->
            <a href="{{ route('pedidos.index', ['estado' => 'En camino']) }}" class="p-3.5 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-100/60 transition group text-center">
                <div class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-motorcycle text-xs"></i>
                    <span>4. En camino</span>
                </div>
                <div class="text-xl font-black text-purple-900 mt-1">{{ $estadosConteo['En camino'] }}</div>
                <div class="text-[11px] text-purple-600 mt-0.5">Repartidor en ruta</div>
            </a>

            <!-- Entregado -->
            <a href="{{ route('pedidos.index', ['estado' => 'Entregado']) }}" class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/60 transition group text-center">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-house-chimney-check text-xs"></i>
                    <span>5. Entregado</span>
                </div>
                <div class="text-xl font-black text-emerald-900 mt-1">{{ $estadosConteo['Entregado'] }}</div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Completado con éxito</div>
            </a>

            <!-- Cancelado -->
            <a href="{{ route('pedidos.index', ['estado' => 'Cancelado']) }}" class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/50 hover:bg-rose-100/60 transition group text-center">
                <div class="text-xs font-bold uppercase tracking-wider text-rose-700 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-ban text-xs"></i>
                    <span>6. Cancelado</span>
                </div>
                <div class="text-xl font-black text-rose-900 mt-1">{{ $estadosConteo['Cancelado'] }}</div>
                <div class="text-[11px] text-rose-600 mt-0.5">Anulado o fallido</div>
            </a>
        </div>
    </div>

    <!-- Pedidos Recientes -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Últimos Pedidos en Operación</h3>
            <a href="{{ route('pedidos.index') }}" class="text-xs text-orange-600 hover:text-orange-700 font-semibold underline">Ver todos los pedidos →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-slate-100">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4 text-left">N° Pedido</th>
                        <th class="py-3 px-4 text-left">Cliente</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4 text-left">Repartidor</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pedidosRecientes as $pedido)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-xs text-slate-900">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $pedido->cliente->nombre }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ $pedido->estado }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">{{ $pedido->domiciliario?->nombre ?? 'Sin asignar' }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">${{ number_format($pedido->total, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="text-xs font-semibold text-blue-600 hover:underline">Comanda</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No hay pedidos recientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
