@extends('layouts.app')

@section('title', 'Nueva Venta')
@section('page_title', 'Registrar Nueva Venta con Descuento de Stock')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="bg-white shadow-sm border border-slate-100 rounded-2xl p-8 space-y-6">
        @csrf

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <div class="flex items-center space-x-2 font-bold mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>Por favor verifique los siguientes errores:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="client_id" class="block text-sm font-semibold text-slate-700 mb-1">Cliente *</label>
                <select id="client_id" name="client_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                            {{ $client->name }} — {{ $client->document_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="sale_date" class="block text-sm font-semibold text-slate-700 mb-1">Fecha de Venta *</label>
                <input id="sale_date" name="sale_date" type="date"
                       value="{{ old('sale_date', now()->format('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
            </div>
        </div>

        <!-- Tabla dinámica de Items -->
        <div class="border-t border-slate-100 pt-4">
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-semibold text-slate-800">Productos y Cantidades a Facturar *</label>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase text-slate-500 font-semibold">
                            <th class="p-2">Producto</th>
                            <th class="p-2 w-28">Cantidad</th>
                            <th class="p-2 w-36 text-right">Precio Unit.</th>
                            <th class="p-2 w-36 text-right">Subtotal</th>
                            <th class="p-2 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <!-- Filas dinámicas con JS -->
                    </tbody>
                </table>
            </div>

            <button type="button" id="add-item" class="mt-3 inline-flex items-center space-x-1.5 text-sm font-semibold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 px-4 py-2 rounded-xl transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Agregar Producto</span>
            </button>
        </div>

        <!-- Resumen de Totales e Impuestos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-slate-100 pt-6 bg-slate-50/50 p-6 rounded-2xl">
            <div>
                <p class="text-xs uppercase text-slate-500 font-semibold tracking-wider">Subtotal Gravable</p>
                <p id="subtotal-display" class="text-xl font-bold font-mono text-slate-800 mt-1">$0.00</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-500 font-semibold tracking-wider">IVA (19% Tarifa General)</p>
                <p id="tax-display" class="text-xl font-bold font-mono text-slate-800 mt-1">$0.00</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-500 font-semibold tracking-wider">Total a Pagar</p>
                <p id="total-display" class="text-2xl font-black font-mono text-emerald-600 mt-1">$0.00</p>
            </div>
        </div>

        <div>
            <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">Notas u Observaciones (Opcional)</label>
            <textarea id="notes" name="notes" rows="2" placeholder="Detalles de facturación, condiciones de entrega..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
            <a href="{{ route('sales.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                Cancelar
            </a>
            <button type="submit" class="bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-md shadow-orange-500/20 transition">
                <i class="fa-solid fa-check mr-1.5"></i> Registrar Venta
            </button>
        </div>
    </form>

    <!-- Template JS -->
    <template id="item-row-template">
        <tr class="border-b item-row">
            <td class="p-2">
                <select name="items[__INDEX__][product_id]" class="product-select w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-orange-500/20" required>
                    <option value="">Seleccione un producto</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                            {{ $product->name }} (Stock: {{ $product->stock }}) — ${{ number_format((float)$product->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="p-2">
                <input type="number" name="items[__INDEX__][quantity]" value="1" min="1"
                       class="quantity-input w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-center font-mono focus:ring-2 focus:ring-orange-500/20" required>
            </td>
            <td class="p-2 text-right font-mono text-sm text-slate-600">
                <span class="price-display">$0.00</span>
            </td>
            <td class="p-2 text-right font-mono font-semibold text-sm text-slate-900">
                <span class="subtotal-display">$0.00</span>
            </td>
            <td class="p-2 text-center">
                <button type="button" class="remove-row text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Eliminar fila">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        </tr>
    </template>

</div>
@endsection

@push('scripts')
<script>
    let index = 0;
    const itemsBody = document.getElementById('items-body');
    const template = document.getElementById('item-row-template').innerHTML;
    const addBtn = document.getElementById('add-item');

    const oldItems = @json(array_values(old('items', [])));

    function addRow(item = null) {
        const html = template.replace(/__INDEX__/g, index++);
        itemsBody.insertAdjacentHTML('beforeend', html);
        const row = itemsBody.lastElementChild;
        if (item) {
            row.querySelector('.product-select').value = item.product_id ?? '';
            row.querySelector('.quantity-input').value = item.quantity ?? 1;
        }
        bindRow(row);
        recalculate();
    }

    function bindRow(row) {
        row.querySelector('.product-select').addEventListener('change', recalculate);
        row.querySelector('.quantity-input').addEventListener('input', recalculate);
        row.querySelector('.remove-row').addEventListener('click', () => {
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                recalculate();
            } else {
                alert('La venta debe tener al menos una fila de producto.');
            }
        });
    }

    function formatCOP(value) {
        return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalculate() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const qty = parseInt(row.querySelector('.quantity-input').value || 0);
            const opt = select.options[select.selectedIndex];
            const price = parseFloat(opt?.dataset?.price || 0);
            const lineSubtotal = price * qty;

            row.querySelector('.price-display').textContent = formatCOP(price);
            row.querySelector('.subtotal-display').textContent = formatCOP(lineSubtotal);
            subtotal += lineSubtotal;
        });

        const tax = Math.round(subtotal * 0.19 * 100) / 100;
        const total = Math.round((subtotal + tax) * 100) / 100;

        document.getElementById('subtotal-display').textContent = formatCOP(subtotal);
        document.getElementById('tax-display').textContent = formatCOP(tax);
        document.getElementById('total-display').textContent = formatCOP(total);
    }

    addBtn.addEventListener('click', () => addRow());

    if (oldItems.length) {
        oldItems.forEach(item => addRow(item));
    } else {
        addRow(); // Fila inicial
    }
</script>
@endpush
