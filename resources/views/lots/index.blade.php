@extends('layouts.app')

@section('title', 'Control de Lotes & FEFO')
@section('page_title', 'Gestión de Lotes Farmacéuticos & Prioridad FEFO')

@section('content')
<div class="space-y-6">

    <!-- Banner Informativo Normativo -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-700 text-white rounded-2xl p-6 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center space-x-2">
                <span class="bg-emerald-500/30 text-emerald-200 text-xs px-2.5 py-0.5 rounded-full font-semibold border border-emerald-400/30">
                    Resolución 1403 de 2007 MinSalud &bull; INVIMA
                </span>
                <span class="bg-amber-400 text-slate-900 text-xs px-2.5 py-0.5 rounded-full font-bold">
                    Algoritmo FEFO
                </span>
            </div>
            <h2 class="text-xl font-black">Control Sanitario de Lotes y Trazabilidad por Caducidad</h2>
            <p class="text-emerald-100 text-sm max-w-3xl">
                Los lotes vigentes se despachan estrictamente en orden cronológico por fecha de caducidad (<strong class="text-white">First Expired, First Out</strong>). Los lotes con fecha vencida quedan automáticamente excluidos del stock dispensable y se bloquean para cuarentena o devolución al laboratorio.
            </p>
        </div>
    </div>

    <!-- Simulador Interactivo de Dispensación FEFO -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-flask"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Simulador de Dispensación FEFO en Tiempo Real</h3>
                <p class="text-xs text-slate-500">Selecciona un producto y una cantidad a dispensar para ver cómo el algoritmo descuenta de los lotes según su fecha de vencimiento.</p>
            </div>
        </div>

        <form action="{{ route('lots.simular-fefo') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Medicamento a Dispensar</label>
                <select name="product_id" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">
                            {{ $prod->nombre }} — Stock Total: {{ $prod->stock }} un. ({{ $prod->lots->where('quantity', '>', 0)->count() }} lotes)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Cantidad a Dispensar</label>
                <input type="number" name="quantity" min="1" value="8" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold" required>
            </div>

            <div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-play"></i>
                    <span>Ejecutar FEFO</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Filtros de Estado -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <a href="{{ route('lots.index', ['filtro' => 'todos']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $filtro === 'todos' ? 'bg-slate-800 text-white shadow' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Todos los Lotes
            </a>
            <a href="{{ route('lots.index', ['filtro' => 'vigentes']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $filtro === 'vigentes' ? 'bg-emerald-600 text-white shadow' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Lotes Vigentes
            </a>
            <a href="{{ route('lots.index', ['filtro' => 'alerta']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $filtro === 'alerta' ? 'bg-amber-500 text-white shadow' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Alerta Vencimiento (≤ 90 días)
            </a>
            <a href="{{ route('lots.index', ['filtro' => 'vencidos']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $filtro === 'vencidos' ? 'bg-rose-600 text-white shadow' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Lotes Vencidos (Cuarentena)
            </a>
        </div>
        <span class="text-xs text-slate-500 font-medium">Mostrando {{ $lots->count() }} lotes en base de datos</span>
    </div>

    <!-- Tabla Principal de Lotes -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Medicamento</th>
                        <th class="py-3.5 px-4">Lote Fabricante</th>
                        <th class="py-3.5 px-4">Fecha Vencimiento</th>
                        <th class="py-3.5 px-4">Días Restantes</th>
                        <th class="py-3.5 px-4 text-center">Stock Lote</th>
                        <th class="py-3.5 px-4 text-center">Estado Sanitario</th>
                        <th class="py-3.5 px-4 text-center">Prioridad FEFO</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lots as $lot)
                        @php
                            $diasRestantes = now()->startOfDay()->diffInDays($lot->expiration_date->startOfDay(), false);
                            $esVencido = $diasRestantes < 0;
                            $esAlerta = !$esVencido && $diasRestantes <= 90;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $esVencido ? 'bg-rose-50/30' : '' }}">
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
                            <td class="py-3.5 px-4 font-semibold">
                                @if($esVencido)
                                    <span class="text-rose-600 text-xs">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Vencido hace {{ abs($diasRestantes) }} días
                                    </span>
                                @elseif($diasRestantes == 0)
                                    <span class="text-rose-600 text-xs font-bold">Vence Hoy</span>
                                @elseif($esAlerta)
                                    <span class="text-amber-600 text-xs">
                                        <i class="fa-regular fa-clock mr-1"></i> {{ $diasRestantes }} días
                                    </span>
                                @else
                                    <span class="text-emerald-700 text-xs">
                                        <i class="fa-regular fa-calendar-check mr-1"></i> {{ $diasRestantes }} días
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-sm font-black {{ $lot->quantity == 0 ? 'text-slate-400' : 'text-slate-800' }}">
                                    {{ $lot->quantity }} un.
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($esVencido)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <i class="fa-solid fa-ban mr-1 text-[10px]"></i> Caducado / Cuarentena
                                    </span>
                                @elseif($esAlerta)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-bell mr-1 text-[10px]"></i> Próximo a Vencer
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-check mr-1 text-[10px]"></i> Vigente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($esVencido)
                                    <span class="text-xs text-rose-500 font-bold uppercase">No Dispensable</span>
                                @elseif($lot->quantity == 0)
                                    <span class="text-xs text-slate-400 font-medium">Agotado</span>
                                @elseif($esAlerta)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-700 text-white">
                                        <i class="fa-solid fa-bolt mr-1 text-[9px]"></i> Despacho Prioritario
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-200 text-slate-800">
                                        En Fila FEFO
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300 block"></i>
                                No se encontraron lotes registrados con el filtro seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lots->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $lots->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
