@extends('layouts.app')

@section('title', $showTrashed ? 'Productos (Papelera)' : 'Catálogo de Productos')
@section('page_title', 'Catálogo de Productos & Menú ' . ($showTrashed ? '(Papelera)' : ''))

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
        <!-- Filtros y Búsqueda -->
        <form action="{{ route('productos.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            @if ($showTrashed)
                <input type="hidden" name="trashed" value="1">
            @endif

            <div class="relative">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar producto..." class="w-60 pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <select name="categoria_id" class="text-xs rounded-lg border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>

            <select name="estado" class="text-xs rounded-lg border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
            </select>

            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filtrar</span>
            </button>
            @if(request('buscar') || request('categoria_id') || request('estado'))
                <a href="{{ route('productos.index', $showTrashed ? ['trashed' => 1] : []) }}" class="text-xs text-slate-500 hover:text-slate-800">Limpiar</a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            {{-- Pestaña de Papelera / Catálogo Activo (Paso 9) --}}
            @can('eliminar-productos')
                @if ($showTrashed)
                    <a href="{{ route('productos.index') }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-2 rounded-lg border border-slate-300 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Ver Catálogo Activo</span>
                    </a>
                @else
                    <a href="{{ route('productos.index', ['trashed' => 1]) }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-2 rounded-lg border border-slate-300 transition">
                        <i class="fa-solid fa-trash-can text-rose-500"></i>
                        <span>Papelera ({{ $trashedCount }})</span>
                    </a>
                @endif
            @endcan

            {{-- Botón Crear Producto protegido con can --}}
            @can('crear-productos')
                <a href="{{ route('productos.create') }}" class="inline-flex items-center space-x-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Nuevo Producto</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4"># ID</th>
                        <th class="py-3.5 px-4">Producto & Descripción</th>
                        <th class="py-3.5 px-4">Categoría</th>
                        <th class="py-3.5 px-4 text-right">Precio</th>
                        <th class="py-3.5 px-4 text-center">Stock</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productos as $producto)
                        <tr class="hover:bg-slate-50/75 transition {{ $producto->trashed() ? 'bg-rose-50/40' : '' }}">
                            <td class="py-3.5 px-4 font-bold text-slate-500">
                                #{{ $producto->id }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $producto->nombre }}</div>
                                <div class="text-xs text-slate-500 truncate max-w-xs">{{ $producto->descripcion ?? 'Sin descripción' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                                    @if($producto->categoria?->trashed())
                                        <span class="text-rose-500 text-[10px] ml-1">(en papelera)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($producto->stock <= 5)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        {{ $producto->stock }} unids (Bajo)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $producto->stock }} unids
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($producto->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-200 text-rose-900">
                                        <i class="fa-solid fa-trash mr-1 text-[10px]"></i> Eliminado
                                    </span>
                                @elseif($producto->estado)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    @if ($producto->trashed())
                                        {{-- Botón Restaurar desde la Papelera --}}
                                        @can('eliminar-productos')
                                            <form method="POST" action="{{ route('productos.restore', $producto) }}" class="inline">
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
                                        @can('editar-productos')
                                            <a href="{{ route('productos.edit', $producto) }}" class="p-1.5 text-slate-500 hover:text-orange-600 transition" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        @endcan

                                        {{-- Botón Eliminar (Soft Delete) con can --}}
                                        @can('eliminar-productos')
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline" onsubmit="return confirm('¿Enviar este producto a la papelera (Soft Delete)?');">
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
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                                {{ $showTrashed ? 'No hay productos en la papelera.' : 'No se encontraron productos registrados.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productos->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $productos->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
