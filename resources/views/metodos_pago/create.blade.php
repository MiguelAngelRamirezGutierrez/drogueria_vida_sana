@extends('layouts.app')

@section('title', 'Nuevo Método de Pago')
@section('page_title', 'Registrar Canal de Pago')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Crear Canal de Recaudo</h2>
            <p class="text-xs text-slate-500">Agregue medios de cobro como billeteras electrónicas o efectivo.</p>
        </div>
        <a href="{{ route('metodos-pago.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Métodos de Pago
        </a>
    </div>

    <form action="{{ route('metodos-pago.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        @csrf

        <div class="space-y-4 text-xs">
            <div>
                <label for="nombre" class="block font-semibold text-slate-700 mb-1">Nombre del Método <span class="text-rose-500">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required placeholder="Ej: Transferencia Bancolombia / QR" class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div>
                <label for="estado" class="block font-semibold text-slate-700 mb-1">Estado</label>
                <select name="estado" id="estado" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('metodos-pago.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition">
                Guardar Método
            </button>
        </div>
    </form>

</div>
@endsection
