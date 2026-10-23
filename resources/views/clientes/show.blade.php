@extends('layouts.app')

@section('title', 'Historial del Cliente')
@section('page_title', 'Ficha del Cliente - ' . $cliente->nombre)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('clientes.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Clientes
        </a>
        <a href="{{ route('clientes.edit', $cliente) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg transition">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Editar Cliente
        </a>
    </div>

    <!-- Tarjeta de Información General -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-2xl font-bold">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $cliente->nombre }}</h2>
                <p class="text-xs text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $cliente->telefono }} &bull; <i class="fa-solid fa-envelope text-slate-400 mx-1"></i>{{ $cliente->email ?? 'Sin correo' }}</p>
                <p class="text-xs text-slate-600 mt-1"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $cliente->direccion }}</p>
            </div>
        </div>

        <div class="text-right border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Total de Pedidos</span>
            <span class="text-3xl font-black text-orange-600">{{ $cliente->pedidos->count() }}</span>
        </div>
    </div>

    <!-- Historial de Pedidos -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center space-x-2">
            <i class="fa-solid fa-receipt text-orange-600"></i>
            <span>Historial de Pedidos Realizados</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3"># Pedido</th>
                        <th class="py-2.5 px-3">Fecha</th>
                        <th class="py-2.5 px-3">Estado</th>
                        <th class="py-2.5 px-3">Método de Pago</th>
                        <th class="py-2.5 px-3">Domiciliario</th>
                        <th class="py-2.5 px-3 text-right">Total</th>
                        <th class="py-2.5 px-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cliente->pedidos as $ped)
                        <tr>
                            <td class="py-2.5 px-3 font-bold text-slate-900">#{{ $ped->id }}</td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $ped->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full font-semibold border text-[11px] bg-slate-50 text-slate-700">
                                    {{ $ped->estado }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3">{{ $ped->metodoPago->nombre }}</td>
                            <td class="py-2.5 px-3">{{ $ped->domiciliario->nombre ?? 'Sin asignar' }}</td>
                            <td class="py-2.5 px-3 text-right font-black text-slate-800">${{ number_format($ped->total, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <a href="{{ route('pedidos.show', $ped) }}" class="text-orange-600 hover:underline font-semibold">
                                    Ver Comanda
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">
                                Este cliente no tiene pedidos registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
