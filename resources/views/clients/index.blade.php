@extends('layouts.app')

@section('title', $showTrashed ? 'Clientes (Papelera)' : 'Directorio de Clientes')
@section('page_title', 'Gestión de Clientes ' . ($showTrashed ? '(Papelera)' : ''))

@section('content')
<div class="space-y-6">

    <!-- Barra Superior de Acciones -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center space-x-3">
            @can('crear-clientes')
                <a href="{{ route('clients.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Nuevo Cliente</span>
                </a>
            @endcan

            @can('eliminar-clientes')
                @if ($showTrashed)
                    <a href="{{ route('clients.index') }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-users"></i>
                        <span>Ver Clientes Activos</span>
                    </a>
                @else
                    <a href="{{ route('clients.index', ['trashed' => 1]) }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-trash-can text-slate-500"></i>
                        <span>Papelera ({{ $trashedCount }})</span>
                    </a>
                @endif
            @endcan
        </div>

        <!-- Formulario de Búsqueda y Filtro -->
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if ($showTrashed)
                <input type="hidden" name="trashed" value="1">
            @endif
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar nombre, doc o email..."
                       class="w-full pl-8 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
            </div>
            <select name="status" class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
                <option value="">Todos los estados</option>
                <option value="active" @selected(request('status') === 'active')>Activos</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
            </select>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('clients.index', $showTrashed ? ['trashed' => 1] : []) }}" class="text-xs text-slate-500 hover:text-slate-700 underline py-2">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla de Clientes -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50/75 text-slate-600 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Documento</th>
                        <th class="px-6 py-4">Nombre / Razón Social</th>
                        <th class="px-6 py-4">Contacto</th>
                        <th class="px-6 py-4">Dirección</th>
                        <th class="px-6 py-4 text-center">Ventas</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-normal">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-900 whitespace-nowrap">
                                <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-700">{{ $client->document_type }}</span>
                                {{ $client->document_number }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $client->name }}
                            </td>
                            <td class="px-6 py-4 text-xs space-y-1">
                                @if($client->email)
                                    <div><i class="fa-regular fa-envelope text-slate-400 mr-1"></i>{{ $client->email }}</div>
                                @endif
                                @if($client->phone)
                                    <div><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $client->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $client->address ?? 'No especificada' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ $client->sales_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($client->active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                @if ($client->trashed())
                                    <form method="POST" action="{{ route('clients.restore', $client) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center space-x-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>Restaurar</span>
                                        </button>
                                    </form>
                                @else
                                    @can('editar-clientes')
                                        <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center space-x-1 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>Editar</span>
                                        </a>
                                    @endcan
                                    @can('eliminar-clientes')
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="inline"
                                              onsubmit="return confirm('¿Enviar este cliente a la papelera?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center space-x-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">
                                                <i class="fa-solid fa-trash"></i>
                                                <span>Eliminar</span>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users text-3xl mb-2 block"></i>
                                No se encontraron clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
