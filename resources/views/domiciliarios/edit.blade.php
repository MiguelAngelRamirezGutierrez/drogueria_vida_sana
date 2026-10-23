@extends('layouts.app')

@section('title', 'Editar Domiciliario')
@section('page_title', 'Modificar Domiciliario #' . $domiciliario->id)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Modificar Domiciliario</h2>
            <p class="text-xs text-slate-500">Actualice datos de contacto, documento o vehículo.</p>
        </div>
        <a href="{{ route('domiciliarios.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Domiciliarios
        </a>
    </div>

    <form action="{{ route('domiciliarios.update', $domiciliario) }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-4 text-xs">
            <div>
                <label for="nombre" class="block font-semibold text-slate-700 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $domiciliario->nombre) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="telefono" class="block font-semibold text-slate-700 mb-1">Teléfono / Celular <span class="text-rose-500">*</span></label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $domiciliario->telefono) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>

                <div>
                    <label for="documento" class="block font-semibold text-slate-700 mb-1">Cédula de Ciudadanía <span class="text-rose-500">*</span></label>
                    <input type="text" name="documento" id="documento" value="{{ old('documento', $domiciliario->documento) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="vehiculo" class="block font-semibold text-slate-700 mb-1">Vehículo / Placa <span class="text-rose-500">*</span></label>
                <input type="text" name="vehiculo" id="vehiculo" value="{{ old('vehiculo', $domiciliario->vehiculo) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div>
                <label for="estado" class="block font-semibold text-slate-700 mb-1">Estado Operativo</label>
                <select name="estado" id="estado" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="1" {{ old('estado', $domiciliario->estado ? '1' : '0') == '1' ? 'selected' : '' }}>Disponible / Activo</option>
                    <option value="0" {{ old('estado', $domiciliario->estado ? '1' : '0') == '0' ? 'selected' : '' }}>Inactivo / Fuera de turno</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('domiciliarios.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition">
                Guardar Cambios
            </button>
        </div>
    </form>

</div>
@endsection
