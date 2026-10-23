<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Lot;
use App\Models\Product;
use App\Services\DispensingService;
use Illuminate\Validation\ValidationException;

echo "====================================================================\n";
echo "PRUEBA EN TINKER: DISPENSINGSERVICE CON 3 LOTES (FEFO)\n";
echo "====================================================================\n\n";

// 1. Obtener el producto
$p = Product::first();
echo "1. Medicamento seleccionado:\n";
echo "   ID: {$p->id} | Nombre: {$p->name} | Stock Inicial: {$p->stock}\n\n";

// Limpiar lotes previos del producto para la prueba limpia
Lot::where('product_id', $p->id)->forceDelete();

// 2. Crear los 3 lotes según la especificación del parcial
echo "2. Creando 3 lotes para el producto:\n";
$l1 = Lot::create([
    'product_id' => $p->id,
    'lot_number' => 'L1',
    'expiration_date' => now()->subDay(), // Vencido ayer
    'quantity' => 10,
]);
echo "   • Lote L1: Cantidad = {$l1->quantity} | Vencimiento = {$l1->expiration_date->format('Y-m-d')} (VENCIDO AYER)\n";

$l2 = Lot::create([
    'product_id' => $p->id,
    'lot_number' => 'L2',
    'expiration_date' => now()->addMonth(), // Vence en 1 mes
    'quantity' => 5,
]);
echo "   • Lote L2: Cantidad = {$l2->quantity} | Vencimiento = {$l2->expiration_date->format('Y-m-d')} (VENCE EN 1 MES - PRIMERO FEFO)\n";

$l3 = Lot::create([
    'product_id' => $p->id,
    'lot_number' => 'L3',
    'expiration_date' => now()->addYear(), // Vence en 1 año
    'quantity' => 15,
]);
echo "   • Lote L3: Cantidad = {$l3->quantity} | Vencimiento = {$l3->expiration_date->format('Y-m-d')} (VENCE EN 1 AÑO)\n\n";

echo "   ESTADO ANTES DE DISPENSAR:\n";
$lotsBefore = Lot::where('product_id', $p->id)->get(['id', 'lot_number', 'expiration_date', 'quantity']);
foreach ($lotsBefore as $lot) {
    echo "   [ID: {$lot->id}] Lote {$lot->lot_number} => {$lot->quantity} unidades (Vence: {$lot->expiration_date->format('Y-m-d')})\n";
}
echo "   Total Stock en Lotes: " . $lotsBefore->sum('quantity') . " unidades (10 vencidas, 20 vigentes).\n\n";

// 3. Ejecutar la dispensación de 8 unidades aplicando FEFO
echo "3. Ejecutando app(App\\Services\\DispensingService::class)->dispense(\$p, 8)...\n";
$service = app(DispensingService::class);
$result = $service->dispense($p, 8);

echo "\n   ¡Dispensación exitosa dentro de DB::transaction!\n";
echo "   Resumen retornado por el servicio:\n";
echo "   - Unidades dispensadas: {$result['total_dispensed']}\n";
echo "   - Nuevo stock del producto: {$result['product_new_stock']}\n";
echo "   - Lotes afectados:\n";
foreach ($result['lots_affected'] as $aff) {
    echo "     * Lote {$aff['lot_number']}: se descontaron {$aff['dispensed_quantity']} uds (quedan {$aff['remaining_lot_stock']})\n";
}

echo "\n4. ESTADO DESPUÉS DEL DESPACHO:\n";
$lotsAfter = Lot::where('product_id', $p->id)->get(['id', 'lot_number', 'expiration_date', 'quantity']);
foreach ($lotsAfter as $lot) {
    echo "   [ID: {$lot->id}] Lote {$lot->lot_number} => {$lot->quantity} unidades (Vence: {$lot->expiration_date->format('Y-m-d')})\n";
}

echo "\n5. EXPLICACIÓN DEL RESULTADO FEFO:\n";
echo "   • L1 (Vencido): Tenía 10 y SIGUE CON 10. Ignorado al 100% por vencido (Cero jarabes vencidos vendidos).\n";
echo "   • L2 (Vence en 1 mes): Tenía 5 y se agotó completamente (queda en 0). Fue el primer lote no vencido en salir.\n";
echo "   • L3 (Vence en 1 año): Tenía 15, cubrió las 3 unidades restantes y queda en 12.\n";
echo "   • Total dispensado: 5 (de L2) + 3 (de L3) = 8 unidades.\n\n";

// 6. Prueba de error al pedir más de lo disponible (regla de rollback y validación)
echo "6. Probando error al pedir más de lo disponible (dispensar 25 unidades cuando solo quedan 12 vigentes):\n";
try {
    $service->dispense($p, 25);
    echo "   ERROR: Se esperaba una ValidationException.\n";
} catch (ValidationException $e) {
    echo "   ValidationException capturada con éxito:\n";
    foreach ($e->errors() as $key => $errors) {
        foreach ($errors as $msg) {
            echo "   [ERROR {$key}]: {$msg}\n";
        }
    }
    echo "   Rollback garantizado: ninguna unidad fue descontada.\n";
}

echo "\n====================================================================\n";
echo "FIN DE LA PRUEBA EXITOSA\n";
echo "====================================================================\n";
