@extends('layouts.app')

@section('title', 'Factura ' . $sale->invoice_number)
@section('page_title', 'Comprobante de Venta & Factura Electrónica')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Acciones Superiores (Volver / Imprimir) -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('sales.index') }}" class="inline-flex items-center space-x-1.5 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Volver al Listado de Ventas</span>
        </a>
        <button onclick="window.print()" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
            <i class="fa-solid fa-print"></i>
            <span>Imprimir Factura</span>
        </button>
    </div>

    <!-- Factura Principal -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-8">

        <!-- Encabezado Factura -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white shadow-md">
                        <i class="fa-solid fa-burger"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">QuickFood ERP</span>
                </div>
                <p class="text-xs text-slate-500">Sistema Integral de Gestión & Dark Kitchen</p>
                <p class="text-xs text-slate-400">NIT: 900.123.456-7 · Régimen Común</p>
            </div>

            <div class="sm:text-right">
                <span class="inline-block bg-orange-50 border border-orange-200 text-orange-700 font-mono font-bold text-sm px-3 py-1 rounded-lg">
                    FACTURA: {{ $sale->invoice_number }}
                </span>
                <p class="text-xs text-slate-500 mt-2">
                    <strong>Fecha:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '' }}
                </p>
                <p class="text-xs text-slate-500">
                    <strong>Vendedor:</strong> {{ $sale->user?->name ?? 'Usuario Sistema' }}
                </p>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="bg-slate-50/75 p-5 rounded-xl border border-slate-100">
            <p class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Datos del Cliente</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-bold text-slate-800">{{ $sale->client?->name ?? 'Cliente Desconocido' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <span class="font-semibold">{{ $sale->client?->document_type }}</span>: {{ $sale->client?->document_number }}
                    </p>
                </div>
                <div class="text-xs text-slate-600 space-y-1">
                    @if($sale->client?->email)
                        <p><i class="fa-regular fa-envelope text-slate-400 mr-1.5"></i>{{ $sale->client->email }}</p>
                    @endif
                    @if($sale->client?->phone)
                        <p><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i>{{ $sale->client->phone }}</p>
                    @endif
                    @if($sale->client?->address)
                        <p><i class="fa-solid fa-location-dot text-slate-400 mr-1.5"></i>{{ $sale->client->address }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detalle de Productos Vendidos -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-slate-100">
                <thead>
                    <tr class="text-xs uppercase text-slate-400 font-semibold text-left">
                        <th class="py-3">Producto / Concepto</th>
                        <th class="py-3 text-center">Cant.</th>
                        <th class="py-3 text-right">Precio Unitario</th>
                        <th class="py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach ($sale->details as $detail)
                        <tr>
                            <td class="py-3 font-medium text-slate-900">
                                {{ $detail->product?->name ?? 'Producto ID #' . $detail->product_id }}
                            </td>
                            <td class="py-3 text-center font-mono font-semibold">
                                {{ $detail->quantity }}
                            </td>
                            <td class="py-3 text-right font-mono text-slate-600">
                                ${{ number_format((float) $detail->unit_price, 2, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-slate-900">
                                ${{ number_format((float) $detail->subtotal, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales e Impuestos -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pt-4 border-t border-slate-100">
            <div class="text-xs text-slate-500 max-w-sm">
                @if($sale->notes)
                    <p class="font-bold text-slate-700 mb-1">Observaciones:</p>
                    <p class="italic bg-slate-50 p-2.5 rounded-lg border border-slate-100">{{ $sale->notes }}</p>
                @endif
                <p class="mt-2 text-[11px] text-slate-400">Esta factura constituye título valor según la normatividad tributaria colombiana (Ley 1231 de 2008).</p>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600 text-xs">
                    <span>Subtotal Gravable:</span>
                    <span class="font-mono font-semibold">${{ number_format((float) $sale->subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600 text-xs">
                    <span>IVA (19%):</span>
                    <span class="font-mono font-semibold">${{ number_format((float) $sale->tax, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-100">
                    <span>Total a Pagar:</span>
                    <span class="font-mono text-emerald-600">${{ number_format((float) $sale->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
