@extends('layouts.app')

@section('title', 'Dashboard Farmacéutico & KPIs')
@section('page_title', 'Panel de Control Farmacéutico - Droguería Vida Sana')

@section('content')
<div class="space-y-6">

    <!-- Banner de Identificación Empresarial -->
    <div class="bg-gradient-to-r from-emerald-800 via-teal-800 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-emerald-700/40">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-300 text-xs px-3 py-1 rounded-full font-bold border border-emerald-400/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-mortar-pestle"></i> Droguería Vida Sana &bull; Cartago (Valle)
                    </span>
                    <span class="bg-amber-400/20 text-amber-300 text-xs px-3 py-1 rounded-full font-bold border border-amber-400/30">
                        Resolución 1403 de 2007 MinSalud / INVIMA
                    </span>
                    <span class="bg-teal-400/20 text-teal-200 text-xs px-3 py-1 rounded-full font-bold border border-teal-400/30">
                        COTECNOVA SGE &bull; Segundo Parcial 2026
                    </span>
                </div>
                <h2 class="text-2xl font-black tracking-tight">Sistema de Gestión Empresarial y Trazabilidad Farmacéutica</h2>
                <p class="text-slate-300 text-sm max-w-3xl">
                    Gestión integral de compras, recepción técnica con cuarentena sanitaria, inventario multicanal y dispensación automatizada bajo el algoritmo <strong class="text-white">FEFO (First Expired, First Out)</strong>.
                </p>
            </div>

            <!-- Accesos rápidos a Documentación y Consola -->
            <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                <a href="{{ route('lots.index') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow flex items-center justify-center gap-2">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Consola de Lotes FEFO</span>
                </a>
                <a href="{{ route('productos.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl transition border border-slate-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-pills"></i>
                    <span>Catálogo Medicamentos</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Indicadores Clave de Desempeño (KPIs Farmacéuticos) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- KPI 1: Medicamentos Activos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Medicamentos</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-pills"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-800">{{ $totalMedicamentos }}</span>
                <span class="text-[11px] font-semibold text-emerald-600">Catálogo Activo</span>
            </div>
        </div>

        <!-- KPI 2: Lotes Vigentes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lotes Vigentes</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-700">{{ $lotesVigentes }}</span>
                <span class="text-[11px] font-semibold text-emerald-600">Dispensables</span>
            </div>
        </div>

        <!-- KPI 3: Alerta Vencimiento Próximo -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alerta FEFO</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-600">{{ $lotesAlerta }}</span>
                <span class="text-[11px] font-semibold text-amber-700">&le; 90 Días</span>
            </div>
        </div>

        <!-- KPI 4: Lotes Vencidos (Cuarentena) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cuarentena</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-rose-600">{{ $lotesVencidos }}</span>
                <span class="text-[11px] font-semibold text-rose-600">Bloqueados</span>
            </div>
        </div>

        <!-- KPI 5: Valor del Inventario -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Inventario</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-lg font-black text-slate-800">$ {{ number_format($valorInventario, 0, ',', '.') }}</span>
                <span class="text-[11px] font-semibold text-purple-600">COP</span>
            </div>
        </div>
    </div>

    <!-- Simulador de Dispensación FEFO Interactivo -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-lg shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-flask-vial"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Prueba de Algoritmo FEFO en Vivo (First Expired, First Out)</h3>
                <p class="text-xs text-slate-500">Seleccione un medicamento y cantidad. El sistema descontará primero de los lotes más próximos a caducar e ignorará los vencidos.</p>
            </div>
        </div>

        <form action="{{ route('lots.simular-fefo') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Medicamento a Dispensar</label>
                <select name="product_id" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                    @foreach($productsParaSimulacion as $prod)
                        <option value="{{ $prod->id }}">
                            {{ $prod->nombre }} &bull; Stock: {{ $prod->stock }} un. &bull; ({{ $prod->lots->where('quantity', '>', 0)->count() }} lotes disponibles)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cantidad a Dispensar</label>
                <input type="number" name="quantity" min="1" value="8" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold" required>
            </div>

            <div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-play"></i>
                    <span>Dispensar con FEFO</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla: Estado de Lotes y Prioridad FEFO de Despacho -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800">Control de Lotes y Cola de Prioridad FEFO</h3>
                <p class="text-xs text-slate-500">Los lotes se ordenan cronológicamente por fecha de expiración para garantizar dispensación segura.</p>
            </div>
            <a href="{{ route('lots.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center gap-1">
                <span>Ver todos los lotes</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Medicamento</th>
                        <th class="py-3.5 px-4">Lote Fabricante</th>
                        <th class="py-3.5 px-4">Fecha Vencimiento</th>
                        <th class="py-3.5 px-4">Tiempo Restante</th>
                        <th class="py-3.5 px-4 text-center">Stock</th>
                        <th class="py-3.5 px-4 text-center">Estado Sanitario</th>
                        <th class="py-3.5 px-4 text-center">Orden FEFO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lotesFefo as $lot)
                        @php
                            $dias = now()->startOfDay()->diffInDays($lot->expiration_date->startOfDay(), false);
                            $esVencido = $dias < 0;
                            $esAlerta = !$esVencido && $dias <= 90;
                        @endphp
                        <tr class="hover:bg-slate-50 transition {{ $esVencido ? 'bg-rose-50/40' : '' }}">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $lot->product->nombre ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-400 block line-clamp-1">{{ $lot->product->descripcion ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-xs font-semibold px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-800">
                                    {{ $lot->lot_number }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                <i class="fa-regular fa-calendar mr-1.5 text-slate-400 text-xs"></i>
                                {{ $lot->expiration_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-xs">
                                @if($esVencido)
                                    <span class="text-rose-600 font-bold">
                                        <i class="fa-solid fa-ban mr-1"></i> Vencido hace {{ abs($dias) }} días
                                    </span>
                                @elseif($dias == 0)
                                    <span class="text-rose-600 font-bold">Vence Hoy</span>
                                @elseif($esAlerta)
                                    <span class="text-amber-600 font-bold">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Quedan {{ $dias }} días
                                    </span>
                                @else
                                    <span class="text-emerald-700">
                                        <i class="fa-regular fa-calendar-check mr-1"></i> Quedan {{ $dias }} días
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-slate-800">
                                {{ $lot->quantity }} un.
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($esVencido)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        Cuarentena
                                    </span>
                                @elseif($esAlerta)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Próximo a Vencer
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Vigente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($esVencido)
                                    <span class="text-xs text-rose-500 font-bold uppercase">Bloqueado</span>
                                @elseif($lot->quantity == 0)
                                    <span class="text-xs text-slate-400">Agotado</span>
                                @elseif($esAlerta)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-700 text-white">
                                        <i class="fa-solid fa-bolt mr-1 text-[9px]"></i> Prioridad 1 FEFO
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-200 text-slate-700">
                                        Prioridad FEFO
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No hay lotes registrados actualmente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Matriz de Roles y Conmutador Rápido de Pruebas -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-base font-bold flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                    <span>Seguridad y Segregación de Funciones (Spatie Permission)</span>
                </h3>
                <p class="text-xs text-slate-400">Haz clic en cualquiera de los usuarios para cambiar de rol inmediatamente y validar los permisos en la barra lateral:</p>
            </div>
            <span class="text-xs bg-slate-800 text-slate-300 px-3 py-1 rounded-lg border border-slate-700">
                4 Roles Farmacéuticos
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($usuariosPrueba as $u)
                @php
                    $roleName = $u->roles->first()?->name ?? 'sin-rol';
                    $isCurrent = auth()->id() === $u->id;
                @endphp
                <a href="{{ route('quick-login', $u->id) }}" class="p-3.5 rounded-xl border transition flex flex-col justify-between {{ $isCurrent ? 'bg-emerald-600/20 border-emerald-500 ring-2 ring-emerald-500/50' : 'bg-slate-800/80 border-slate-700 hover:border-slate-600 hover:bg-slate-800' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-white">{{ $u->name }}</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleName === 'admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : ($roleName === 'regente' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($roleName === 'cajero' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-blue-500/20 text-blue-300 border border-blue-500/40')) }}">
                            {{ $roleName }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-400 truncate">{{ $u->email }}</div>
                    <div class="mt-2 text-[10px] text-slate-300 flex items-center gap-1">
                        @if($isCurrent)
                            <i class="fa-solid fa-circle-check text-emerald-400"></i>
                            <span class="text-emerald-400 font-semibold">Sesión Actual</span>
                        @else
                            <i class="fa-solid fa-arrow-right-to-bracket text-slate-400"></i>
                            <span>Cambiar a este usuario</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection
