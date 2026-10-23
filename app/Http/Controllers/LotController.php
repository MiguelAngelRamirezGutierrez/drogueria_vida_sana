<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Product;
use App\Services\DispensingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LotController extends Controller
{
    /**
     * Muestra la lista de lotes farmacéuticos ordenados con prioridad FEFO.
     */
    public function index(Request $request)
    {
        $filtro = $request->get('filtro', 'todos');

        $query = Lot::with('product');

        if ($filtro === 'vigentes') {
            $query->vigentes()->where('quantity', '>', 0);
        } elseif ($filtro === 'vencidos') {
            $query->vencidos();
        } elseif ($filtro === 'alerta') {
            $query->vigentes()->where('quantity', '>', 0)->whereDate('expiration_date', '<=', now()->addDays(90));
        }

        $lots = $query->orderBy('expiration_date', 'asc')->paginate(15);
        $products = Product::where('active', true)->get();

        return view('lots.index', compact('lots', 'filtro', 'products'));
    }

    /**
     * Ejecuta una simulación interactiva del servicio de dispensación FEFO.
     */
    public function simularFefo(Request $request, DispensingService $dispensingService)
    {
        $request->validate([
            'product_id' => 'required|exists:productos,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cantidad = (int) $request->quantity;

        try {
            $resultado = $dispensingService->dispense($product, $cantidad);

            $detalleLotes = collect($resultado['lots_used'])
                ->map(fn($item) => "Lote {$item['lot_number']} (Vence: {$item['expiration_date']}) - Descontadas {$item['quantity_deducted']} un.")
                ->implode(' | ');

            return back()->with('success', "Dispensación FEFO exitosa: Se dispensaron {$resultado['quantity_dispensed']} unidades de '{$product->nombre}'. Lotes afectados: {$detalleLotes}. Stock restante del producto: {$resultado['product_remaining_stock']}.");
        } catch (ValidationException $e) {
            $errorMsg = collect($e->errors())->flatten()->first() ?? 'Error al dispensar por algoritmo FEFO.';
            return back()->with('error', "Rechazo de Dispensación: {$errorMsg}");
        } catch (\Exception $e) {
            return back()->with('error', "Excepción en dispensación: " . $e->getMessage());
        }
    }
}
