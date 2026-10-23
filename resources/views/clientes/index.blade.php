@extends('layouts.app')

@section('title', 'Directorio de Clientes')
@section('page_title', 'Clientes & Puntos de Entrega')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('clientes.index') }}" method="GET" class="flex items-center space-x-2">
            <div class="relative">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, tel o dirección..." class="w-72 pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition">
                Buscar
            </button>
            @if(request('buscar'))
                <a href="{{ route('clientes.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Limpiar</a>
            @endif
        </form>

        <a href="{{ route('clientes.create') }}" class="inline-flex items-center space-x-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-user-plus"></i>
            <span>Nuevo Cliente</span>
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
                        <th class="py-3.5 px-4">Dirección Habitual</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4 text-center">Pedidos</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clientes as $cliente)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-500">
                                #{{ $cliente->id }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <a href="{{ route('clientes.show', $cliente) }}" class="text-slate-900 hover:text-orange-600">
                                    {{ $cliente->nombre }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-700">
                                <i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $cliente->telefono }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600 max-w-xs truncate" title="{{ $cliente->direccion }}">
                                <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $cliente->direccion }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">
                                {{ $cliente->email ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="bg-orange-50 text-orange-700 font-bold px-2 py-0.5 rounded-full text-xs border border-orange-200">
                                    {{ $cliente->pedidos_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="p-1.5 text-slate-600 hover:text-orange-600 hover:bg-slate-100 rounded transition" title="Ver Historial">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que desea eliminar a {{ $cliente->nombre }}?');">
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
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                No se encontraron clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clientes->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
