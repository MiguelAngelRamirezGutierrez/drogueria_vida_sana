<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispensingService
{
    /**
     * Dispensa una cantidad de producto aplicando el algoritmo FEFO (First Expired, First Out)
     * dentro de una transacción de base de datos segura.
     *
     * Reglas sanitarias y técnicas:
     *  1. Ignora estrictamente los lotes vencidos (expiration_date < today).
     *  2. Valida que alcance el stock no vencido (si no alcanza, lanza ValidationException y aplica rollback).
     *  3. Descuenta primero del lote que vence antes (FEFO).
     *  4. Descuenta también el stock consolidado del producto.
     *
     * @param Product $product Medicamento a dispensar
     * @param int $quantity Cantidad de unidades requeridas
     * @return array Detalle de auditoría con los lotes afectados y existencias finales
     * @throws ValidationException
     */
    public function dispense(Product $product, int $quantity): array
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad a dispensar debe ser un número entero mayor a cero.',
            ]);
        }

        return DB::transaction(function () use ($product, $quantity) {
            // 1. Bloqueo de concurrencia pesimista sobre el producto maestro
            $lockedProduct = Product::lockForUpdate()->findOrFail($product->id);

            // 2. Consulta de lotes vigentes (no vencidos) con stock disponible, ordenados por FEFO
            // La condición >= today() asegura que ningún lote caducado sea considerado para la venta
            $availableLots = Lot::where('product_id', $lockedProduct->id)
                ->whereDate('expiration_date', '>=', now()->toDateString())
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date', 'asc')
                ->lockForUpdate()
                ->get();

            // 3. Validación de suficiencia de existencias no vencidas
            $totalAvailable = $availableLots->sum('quantity');

            if ($totalAvailable < $quantity) {
                // Conteo informativo de unidades vencidas bloqueadas
                $expiredQuantity = Lot::where('product_id', $lockedProduct->id)
                    ->whereDate('expiration_date', '<', now()->toDateString())
                    ->sum('quantity');

                $errorMessage = "Stock insuficiente no vencido para «{$lockedProduct->name}». " .
                                "Disponible vigente: {$totalAvailable}, solicitado: {$quantity}.";

                if ($expiredQuantity > 0) {
                    $errorMessage .= " (Se bloquearon {$expiredQuantity} unidades en lotes vencidos por regla sanitaria).";
                }

                throw ValidationException::withMessages([
                    'stock' => $errorMessage,
                ]);
            }

            // 4. Algoritmo FEFO: Descontar primero del lote que vence antes
            $remainingToDispense = $quantity;
            $dispensedDetails = [];

            foreach ($availableLots as $lot) {
                if ($remainingToDispense <= 0) {
                    break;
                }

                // Cantidad a tomar del lote actual
                $takeFromLot = min($lot->quantity, $remainingToDispense);

                // Descuento en la tabla de lotes
                $lot->decrement('quantity', $takeFromLot);

                $dispensedDetails[] = [
                    'lot_id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'expiration_date' => $lot->expiration_date->format('Y-m-d'),
                    'dispensed_quantity' => $takeFromLot,
                    'remaining_lot_stock' => $lot->fresh()->quantity,
                ];

                $remainingToDispense -= $takeFromLot;
            }

            // 5. Descuento simultáneo en el stock general del producto
            $lockedProduct->decrement('stock', $quantity);

            return [
                'product_id' => $lockedProduct->id,
                'product_name' => $lockedProduct->name,
                'total_dispensed' => $quantity,
                'product_new_stock' => $lockedProduct->fresh()->stock,
                'lots_affected' => $dispensedDetails,
            ];
        });
    }
}
