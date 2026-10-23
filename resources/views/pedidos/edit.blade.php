@extends('layouts.app')

@section('title', 'Editar Pedido #' . $pedido->id)
@section('page_title', 'Modificar Pedido #' . $pedido->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Modificar Datos del Pedido</h2>
            <p class="text-xs text-slate-500">Actualice la asignación de domiciliario, dirección de entrega o estado.</p>
        </div>
        <a href="{{ route('pedidos.show', $pedido) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Comanda
        </a>
    </div>

    <form action="{{ route('pedidos.update', $pedido) }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Cliente</label>
                <input type="text" disabled value="{{ $pedido->cliente->nombre }}" class="w-full bg-slate-100 rounded-lg border border-slate-200 p-2.5 text-slate-500 font-medium">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Método de Pago</label>
                <input type="text" disabled value="{{ $pedido->metodoPago->nombre }}" class="w-full bg-slate-100 rounded-lg border border-slate-200 p-2.5 text-slate-500 font-medium">
            </div>

            <div>
                <label for="domiciliario_id" class="block font-semibold text-slate-700 mb-1">Domiciliario Asignado</label>
                <select name="domiciliario_id" id="domiciliario_id" class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="">-- Sin domiciliario asignado --</option>
                    @foreach($domiciliarios as $d)
                        <option value="{{ $d->id }}" {{ old('domiciliario_id', $pedido->domiciliario_id) == $d->id ? 'selected' : '' }}>
                            {{ $d->nombre }} - {{ $d->vehiculo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado" class="block font-semibold text-slate-700 mb-1">Estado del Pedido <span class="text-rose-500">*</span></label>
                <select name="estado" id="estado" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none font-bold">
                    @foreach(\App\Models\Pedido::ESTADOS as $est)
                        <option value="{{ $est }}" {{ old('estado', $pedido->estado) == $est ? 'selected' : '' }}>
                            {{ $est }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="direccion_entrega" class="block font-semibold text-slate-700 mb-1">Dirección de Entrega <span class="text-rose-500">*</span></label>
                <input type="text" name="direccion_entrega" id="direccion_entrega" value="{{ old('direccion_entrega', $pedido->direccion_entrega) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div class="md:col-span-2">
                <label for="observaciones" class="block font-semibold text-slate-700 mb-1">Observaciones / Notas para Cocina</label>
                <textarea name="observaciones" id="observaciones" rows="3" class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">{{ old('observaciones', $pedido->observaciones) }}</textarea>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('pedidos.show', $pedido) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition">
                Guardar Cambios
            </button>
        </div>
    </form>

</div>
@endsection
