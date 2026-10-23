@extends('layouts.app')

@section('title', 'Tomar Nuevo Pedido')
@section('page_title', 'Registrar Nuevo Pedido en Cocina')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Formulario de Pedido & Domicilio</h2>
            <p class="text-xs text-slate-500">Seleccione el cliente, arme la comanda con los productos y registre la entrega.</p>
        </div>
        <a href="{{ route('pedidos.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Pedidos
        </a>
    </div>

    <form action="{{ route('pedidos.store') }}" method="POST" id="formPedido" class="space-y-6">
        @csrf

        <!-- Bloque 1: Datos del Cliente y Logística de Entrega -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-orange-600 flex items-center space-x-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-user-tag"></i>
                <span>1. Información del Cliente & Entrega</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <!-- Cliente -->
                <div>
                    <label for="cliente_id" class="block font-semibold text-slate-700 text-xs mb-1">
                        Cliente <span class="text-rose-500">*</span>
                    </label>
                    <select name="cliente_id" id="cliente_id" required class="w-full rounded-lg border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach($clientes as $cli)
                            <option value="{{ $cli->id }}" data-direccion="{{ $cli->direccion }}" data-telefono="{{ $cli->telefono }}" {{ old('cliente_id') == $cli->id ? 'selected' : '' }}>
                                {{ $cli->nombre }} ({{ $cli->telefono }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label for="metodo_pago_id" class="block font-semibold text-slate-700 text-xs mb-1">
                        Método de Pago <span class="text-rose-500">*</span>
                    </label>
                    <select name="metodo_pago_id" id="metodo_pago_id" required class="w-full rounded-lg border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        <option value="">-- Seleccione método de pago --</option>
                        @foreach($metodosPago as $met)
                            <option value="{{ $met->id }}" {{ old('metodo_pago_id') == $met->id ? 'selected' : '' }}>
                                {{ $met->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dirección de Entrega -->
                <div class="md:col-span-2">
                    <label for="direccion_entrega" class="block font-semibold text-slate-700 text-xs mb-1">
                        Dirección Exacta de Entrega <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="direccion_entrega" id="direccion_entrega" value="{{ old('direccion_entrega') }}" required placeholder="Se autocompleta con la dirección del cliente o modifíquela para esta orden..." class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        <i class="fa-solid fa-location-dot absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                    <span class="text-[11px] text-slate-400">Queda grabada en este pedido aunque el cliente cambie de dirección en el futuro.</span>
                </div>

                <!-- Domiciliario Asignado (Opcional) -->
                <div>
                    <label for="domiciliario_id" class="block font-semibold text-slate-700 text-xs mb-1">
                        Domiciliario Asignado (Opcional en fase inicial)
                    </label>
                    <select name="domiciliario_id" id="domiciliario_id" class="w-full rounded-lg border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        <option value="">-- Asignar más adelante en cocina --</option>
                        @foreach($domiciliarios as $dom)
                            <option value="{{ $dom->id }}" {{ old('domiciliario_id') == $dom->id ? 'selected' : '' }}>
                                {{ $dom->nombre }} - {{ $dom->vehiculo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="observaciones" class="block font-semibold text-slate-700 text-xs mb-1">
                        Observaciones / Notas para Cocina
                    </label>
                    <input type="text" name="observaciones" id="observaciones" value="{{ old('observaciones') }}" placeholder="Ej: Sin cebolla, salsas aparte, timbrar fuerte..." class="w-full rounded-lg border border-slate-300 p-2.5 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Bloque 2: Comanda de Productos Dinámica -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-orange-600 flex items-center space-x-2">
                    <i class="fa-solid fa-utensils"></i>
                    <span>2. Ítems del Pedido (Comanda)</span>
                </h3>
                <button type="button" id="btnAgregarFila" class="inline-flex items-center space-x-1.5 text-xs font-semibold bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Agregar Producto</span>
                </button>
            </div>

            <!-- Tabla de Ítems -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="tablaItems">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3 w-1/2">Producto</th>
                            <th class="py-2.5 px-3 text-center w-28">Precio Unit.</th>
                            <th class="py-2.5 px-3 text-center w-24">Cantidad</th>
                            <th class="py-2.5 px-3 text-right w-32">Subtotal</th>
                            <th class="py-2.5 px-3 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="contenedorItems" class="divide-y divide-slate-100">
                        <!-- Fila inicial por defecto -->
                        <tr class="fila-producto hover:bg-slate-50/50">
                            <td class="py-3 px-3">
                                <select name="items[0][producto_id]" required class="select-producto w-full rounded-lg border border-slate-300 p-2 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                                    <option value="">-- Seleccione producto --</option>
                                    @foreach($productos as $prod)
                                        <option value="{{ $prod->id }}" data-precio="{{ $prod->precio }}" data-stock="{{ $prod->stock }}">
                                            {{ $prod->nombre }} - [${{ number_format($prod->precio, 0, ',', '.') }}] (Stock: {{ $prod->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="precio-unitario-label font-medium text-slate-700 text-xs">$0</span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <input type="number" name="items[0][cantidad]" min="1" value="1" required class="input-cantidad w-20 text-center rounded-lg border border-slate-300 p-2 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none font-bold">
                            </td>
                            <td class="py-3 px-3 text-right">
                                <span class="subtotal-label font-bold text-slate-800 text-xs">$0</span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <button type="button" class="btn-eliminar-fila text-slate-300 hover:text-rose-600 transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Resumen Total -->
            <div class="border-t border-slate-200 pt-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500">
                    <i class="fa-solid fa-circle-info text-orange-500 mr-1"></i>
                    El stock se reducirá automáticamente al guardar la orden. Estado inicial: <strong>Recibido</strong>.
                </div>
                <div class="flex items-center space-x-3 bg-slate-50 px-5 py-3 rounded-xl border border-slate-200">
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-500">Total a Pagar:</span>
                    <span id="labelGranTotal" class="text-2xl font-black text-orange-600">$0</span>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('pedidos.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition flex items-center space-x-2">
                <i class="fa-solid fa-check"></i>
                <span>Crear Pedido (# Recibido)</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clienteSelect = document.getElementById('cliente_id');
        const direccionInput = document.getElementById('direccion_entrega');
        const contenedorItems = document.getElementById('contenedorItems');
        const btnAgregarFila = document.getElementById('btnAgregarFila');
        const labelGranTotal = document.getElementById('labelGranTotal');

        // Autocompletar dirección del cliente
        clienteSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            if (selected && selected.dataset.direccion) {
                direccionInput.value = selected.dataset.direccion;
            }
        });

        let filaIndex = 1;

        // Opciones de productos para nuevas filas
        const productosOptions = `
            <option value="">-- Seleccione producto --</option>
            @foreach($productos as $prod)
                <option value="{{ $prod->id }}" data-precio="{{ $prod->precio }}" data-stock="{{ $prod->stock }}">
                    {{ $prod->nombre }} - [${{ number_format($prod->precio, 0, ',', '.') }}] (Stock: {{ $prod->stock }})
                </option>
            @endforeach
        `;

        function formatCOP(valor) {
            return '$' + new Intl.NumberFormat('es-CO').format(valor);
        }

        function recalcularTotales() {
            let granTotal = 0;
            const filas = contenedorItems.querySelectorAll('.fila-producto');

            filas.forEach(fila => {
                const selectProd = fila.querySelector('.select-producto');
                const inputCant = fila.querySelector('.input-cantidad');
                const labelPrecio = fila.querySelector('.precio-unitario-label');
                const labelSubtotal = fila.querySelector('.subtotal-label');

                const selectedOption = selectProd.options[selectProd.selectedIndex];
                const precio = parseFloat(selectedOption?.dataset?.precio || 0);
                const cantidad = parseInt(inputCant.value || 0);

                labelPrecio.textContent = formatCOP(precio);

                const subtotal = precio * cantidad;
                labelSubtotal.textContent = formatCOP(subtotal);

                granTotal += subtotal;
            });

            labelGranTotal.textContent = formatCOP(granTotal);
        }

        // Eventos delegados en la tabla
        contenedorItems.addEventListener('change', function (e) {
            if (e.target.classList.contains('select-producto') || e.target.classList.contains('input-cantidad')) {
                recalcularTotales();
            }
        });

        contenedorItems.addEventListener('input', function (e) {
            if (e.target.classList.contains('input-cantidad')) {
                recalcularTotales();
            }
        });

        contenedorItems.addEventListener('click', function (e) {
            const btnEliminar = e.target.closest('.btn-eliminar-fila');
            if (btnEliminar) {
                const filas = contenedorItems.querySelectorAll('.fila-producto');
                if (filas.length > 1) {
                    btnEliminar.closest('tr').remove();
                    recalcularTotales();
                } else {
                    alert('El pedido debe tener al menos un producto.');
                }
            }
        });

        // Agregar nueva fila
        btnAgregarFila.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.className = 'fila-producto hover:bg-slate-50/50';
            tr.innerHTML = `
                <td class="py-3 px-3">
                    <select name="items[${filaIndex}][producto_id]" required class="select-producto w-full rounded-lg border border-slate-300 p-2 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        ${productosOptions}
                    </select>
                </td>
                <td class="py-3 px-3 text-center">
                    <span class="precio-unitario-label font-medium text-slate-700 text-xs">$0</span>
                </td>
                <td class="py-3 px-3 text-center">
                    <input type="number" name="items[${filaIndex}][cantidad]" min="1" value="1" required class="input-cantidad w-20 text-center rounded-lg border border-slate-300 p-2 text-xs focus:ring-2 focus:ring-orange-500 focus:outline-none font-bold">
                </td>
                <td class="py-3 px-3 text-right">
                    <span class="subtotal-label font-bold text-slate-800 text-xs">$0</span>
                </td>
                <td class="py-3 px-3 text-center">
                    <button type="button" class="btn-eliminar-fila text-slate-400 hover:text-rose-600 transition">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;
            contenedorItems.appendChild(tr);
            filaIndex++;
        });

        // Inicializar cálculos
        recalcularTotales();
    });
</script>
@endpush
@endsection
