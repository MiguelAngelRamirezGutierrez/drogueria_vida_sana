@extends('layouts.app')

@section('title', 'Métodos de Pago')
@section('page_title', 'Métodos de Pago & Canales de Recaudo')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('metodos-pago.index') }}" method="GET" class="flex items-center space-x-2">
            <div class="relative">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar método de pago..." class="w-64 pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition">
                Buscar
            </button>
            @if(request('buscar'))
                <a href="{{ route('metodos-pago.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Limpiar</a>
            @endif
        </form>

        <a href="{{ route('metodos-pago.create') }}" class="inline-flex items-center space-x-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo Método</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4"># ID</th>
                        <th class="py-3.5 px-4">Nombre del Medio / Canal</th>
                        <th class="py-3.5 px-4 text-center">Pedidos Recaudados</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($metodos as $metodo)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-500">#{{ $metodo->id }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $metodo->nombre }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full text-xs border border-emerald-200">
                                    {{ $metodo->pedidos_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($metodo->estado)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('metodos-pago.edit', $metodo) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('metodos-pago.destroy', $metodo) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que desea eliminar el método {{ $metodo->nombre }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded transition" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                No se encontraron métodos de pago.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($metodos->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $metodos->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
