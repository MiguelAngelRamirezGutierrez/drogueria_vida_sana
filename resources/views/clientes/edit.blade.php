@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('page_title', 'Modificar Cliente #' . $cliente->id)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Modificar Ficha de Cliente</h2>
            <p class="text-xs text-slate-500">Actualice número de teléfono o dirección de residencia.</p>
        </div>
        <a href="{{ route('clientes.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Clientes
        </a>
    </div>

    <form action="{{ route('clientes.update', $cliente) }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-4 text-xs">
            <div>
                <label for="nombre" class="block font-semibold text-slate-700 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $cliente->nombre) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="telefono" class="block font-semibold text-slate-700 mb-1">Teléfono / Celular <span class="text-rose-500">*</span></label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $cliente->telefono) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>

                <div>
                    <label for="email" class="block font-semibold text-slate-700 mb-1">Correo Electrónico (Opcional)</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $cliente->email) }}" class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="direccion" class="block font-semibold text-slate-700 mb-1">Dirección Habitual de Entrega <span class="text-rose-500">*</span></label>
                <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $cliente->direccion) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div>
                <label for="estado" class="block font-semibold text-slate-700 mb-1">Estado</label>
                <select name="estado" id="estado" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="1" {{ old('estado', $cliente->estado ? '1' : '0') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $cliente->estado ? '1' : '0') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('clientes.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition">
                Guardar Cambios
            </button>
        </div>
    </form>

</div>
@endsection
