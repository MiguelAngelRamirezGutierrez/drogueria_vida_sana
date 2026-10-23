@extends('layouts.app')

@section('title', 'Equipo de Domiciliarios')
@section('page_title', 'Flota de Repartidores & Domiciliarios')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('domiciliarios.index') }}" method="GET" class="flex items-center space-x-2">
            <div class="relative">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, tel, doc o vehículo..." class="w-72 pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition">
                Buscar
            </button>
            @if(request('buscar'))
                <a href="{{ route('domiciliarios.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Limpiar</a>
            @endif
        </form>

        <a href="{{ route('domiciliarios.create') }}" class="inline-flex items-center space-x-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-motorcycle"></i>
            <span>Nuevo Domiciliario</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4"># ID</th>
                        <th class="py-3.5 px-4">Nombre Completo</th>
                        <th class="py-3.5 px-4">Teléfono</th>
                        <th class="py-3.5 px-4">Cédula / Documento</th>
                        <th class="py-3.5 px-4">Vehículo Asignado</th>
                        <th class="py-3.5 px-4 text-center">Entregas</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($domiciliarios as $dom)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-500">#{{ $dom->id }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $dom->nombre }}
                            </td>
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-700">
                                <i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $dom->telefono }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                CC {{ $dom->documento }}
                            </td>
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-700">
                                <i class="fa-solid fa-motorcycle text-slate-400 mr-1"></i>{{ $dom->vehiculo }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="bg-blue-50 text-blue-700 font-bold px-2 py-0.5 rounded-full text-xs border border-blue-200">
                                    {{ $dom->pedidos_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($dom->estado)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Disponible
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('domiciliarios.edit', $dom) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('domiciliarios.destroy', $dom) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que desea eliminar a {{ $dom->nombre }}?');">
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
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                No se encontraron domiciliarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($domiciliarios->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $domiciliarios->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
