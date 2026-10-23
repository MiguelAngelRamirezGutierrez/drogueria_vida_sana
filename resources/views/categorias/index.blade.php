@extends('layouts.app')

@section('title', $showTrashed ? 'Categorías (Papelera)' : 'Categorías de Alimentos')
@section('page_title', 'Categorías de Menú ' . ($showTrashed ? '(Papelera)' : ''))

@section('content')
<div class="space-y-6">

    <!-- Mensajes de Estado / Alertas -->
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('categorias.index') }}" method="GET" class="flex items-center space-x-2">
            @if ($showTrashed)
                <input type="hidden" name="trashed" value="1">
            @endif

            <div class="relative">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar categoría..." class="w-64 pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition">
                Buscar
            </button>
            @if(request('buscar'))
                <a href="{{ route('categorias.index', $showTrashed ? ['trashed' => 1] : []) }}" class="text-xs text-slate-500 hover:text-slate-800">Limpiar</a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            {{-- Pestaña de Papelera con can --}}
            @can('eliminar-categorias')
                @if ($showTrashed)
                    <a href="{{ route('categorias.index') }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-2 rounded-lg border border-slate-300 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Ver Categorías Activas</span>
                    </a>
                @else
                    <a href="{{ route('categorias.index', ['trashed' => 1]) }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-2 rounded-lg border border-slate-300 transition">
                        <i class="fa-solid fa-trash-can text-rose-500"></i>
                        <span>Papelera ({{ $trashedCount }})</span>
                    </a>
                @endif
            @endcan

            {{-- Botón Crear Categoría con can --}}
            @can('crear-categorias')
                <a href="{{ route('categorias.create') }}" class="inline-flex items-center space-x-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Nueva Categoría</span>
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4"># ID</th>
                        <th class="py-3.5 px-4">Nombre de Categoría</th>
                        <th class="py-3.5 px-4">Descripción</th>
                        <th class="py-3.5 px-4 text-center">Productos</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categorias as $cat)
                        <tr class="hover:bg-slate-50/75 transition {{ $cat->trashed() ? 'bg-rose-50/40' : '' }}">
                            <td class="py-3.5 px-4 font-bold text-slate-500">#{{ $cat->id }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $cat->nombre }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500 max-w-md truncate">
                                {{ $cat->descripcion ?? 'Sin descripción' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="bg-purple-50 text-purple-700 font-bold px-2 py-0.5 rounded-full text-xs border border-purple-200">
                                    {{ $cat->productos_count }} platos
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($cat->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-200 text-rose-900">
                                        <i class="fa-solid fa-trash mr-1 text-[10px]"></i> Eliminada
                                    </span>
                                @elseif($cat->estado)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    @if ($cat->trashed())
                                        {{-- Botón Restaurar Categoría --}}
                                        @can('eliminar-categorias')
                                            <form method="POST" action="{{ route('categorias.restore', $cat) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center space-x-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-2.5 py-1.5 rounded-md font-semibold transition" title="Restaurar de la papelera">
                                                    <i class="fa-solid fa-rotate-left text-xs"></i>
                                                    <span>Restaurar</span>
                                                </button>
                                            </form>
                                        @endcan
                                    @else
                                        {{-- Botón Editar con can --}}
                                        @can('editar-categorias')
                                            <a href="{{ route('categorias.edit', $cat) }}" class="p-1.5 text-slate-500 hover:text-orange-600 transition" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        @endcan

                                        {{-- Botón Eliminar con can --}}
                                        @can('eliminar-categorias')
                                            <form action="{{ route('categorias.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('¿Enviar esta categoría a la papelera?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 transition" title="Enviar a papelera">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-tag text-3xl mb-2 block"></i>
                                {{ $showTrashed ? 'No hay categorías en la papelera.' : 'No se encontraron categorías registradas.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categorias->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $categorias->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
