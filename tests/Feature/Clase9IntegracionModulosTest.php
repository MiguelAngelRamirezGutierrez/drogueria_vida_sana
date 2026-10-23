<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class Clase9IntegracionModulosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /**
     * Prueba 1: Transacción completa - Registro de venta, cálculo de IVA (19%) y descuento atómico de stock.
     */
    public function test_sale_service_transaction_creates_sale_and_decrements_stock(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $client = Client::first();
        $product = Product::where('stock', '>=', 5)->first();

        $initialStock = $product->stock;
        $quantity = 2;
        $expectedSubtotal = (float) $product->price * $quantity;
        $expectedTax = round($expectedSubtotal * 0.19, 2);
        $expectedTotal = round($expectedSubtotal + $expectedTax, 2);

        $service = app(SaleService::class);
        $sale = $service->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'notes' => 'Venta de prueba automatizada',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]
            ],
        ], $admin->id);

        // Verificaciones de la cabecera
        $this->assertNotNull($sale);
        $this->assertStringStartsWith('FV-', $sale->invoice_number);
        $this->assertEquals('pagada', $sale->status);
        $this->assertEquals($expectedSubtotal, (float) $sale->subtotal);
        $this->assertEquals($expectedTax, (float) $sale->tax);
        $this->assertEquals($expectedTotal, (float) $sale->total);

        // Verificaciones del detalle
        $this->assertCount(1, $sale->details);
        $detail = $sale->details->first();
        $this->assertEquals($product->id, $detail->product_id);
        $this->assertEquals($quantity, $detail->quantity);
        $this->assertEquals($expectedSubtotal, (float) $detail->subtotal);

        // Verificación del descuento de stock
        $this->assertEquals($initialStock - $quantity, $product->fresh()->stock);
    }

    /**
     * Prueba 2: Regla de negocio y Rollback - Si el stock es insuficiente, la transacción se revierte.
     */
    public function test_sale_service_fails_when_insufficient_stock_and_rolls_back(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $client = Client::first();
        $product = Product::where('estado', true)->where('stock', '>', 0)->first();

        $initialStock = $product->stock;
        $requestedQuantity = $initialStock + 10;

        $service = app(SaleService::class);

        try {
            $service->register([
                'client_id' => $client->id,
                'sale_date' => now()->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => $requestedQuantity,
                    ]
                ],
            ], $admin->id);

            $this->fail('Se esperaba una ValidationException por stock insuficiente.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('items', $e->errors());
            $this->assertStringContainsString('Stock insuficiente', $e->errors()['items'][0]);
        }

        // El stock no debió cambiar
        $this->assertEquals($initialStock, $product->fresh()->stock);
    }

    /**
     * Prueba 3: Regla de negocio - No se puede vender un producto inactivo.
     */
    public function test_sale_service_fails_for_inactive_product(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $client = Client::first();
        $product = Product::first();

        // Desactivar producto
        $product->update(['estado' => false, 'active' => false]);

        $service = app(SaleService::class);

        $this->expectException(ValidationException::class);
        $service->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ],
        ], $admin->id);
    }

    /**
     * Prueba 4: Regla de integridad - No se puede eliminar un cliente que tiene ventas asociadas.
     */
    public function test_client_with_sales_cannot_be_deleted(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $client = Client::first();
        $product = Product::where('stock', '>', 0)->where('estado', true)->first();

        // Crear una venta para este cliente
        $service = app(SaleService::class);
        $service->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ],
        ], $admin->id);

        $response = $this->actingAs($admin)->delete(route('clients.destroy', $client));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // El cliente sigue existiendo sin deleted_at
        $this->assertNull($client->fresh()->deleted_at);
    }

    /**
     * Prueba 5: Regla inversa - Al anular una venta se devuelve el stock a los productos y se marca como anulada.
     */
    public function test_anular_sale_restores_product_stock_and_soft_deletes_sale(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $client = Client::first();
        $product = Product::where('stock', '>=', 5)->where('estado', true)->first();

        $originalStock = $product->stock;
        $quantity = 3;

        $service = app(SaleService::class);
        $sale = $service->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $product->id, 'quantity' => $quantity]
            ],
        ], $admin->id);

        $this->assertEquals($originalStock - $quantity, $product->fresh()->stock);

        // Anular la venta
        $response = $this->actingAs($admin)->delete(route('sales.destroy', $sale));
        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('success');

        // El stock regresó a su valor original
        $this->assertEquals($originalStock, $product->fresh()->stock);

        // La venta queda con status = anulada y soft-deleted
        $saleActualizada = Sale::withTrashed()->find($sale->id);
        $this->assertEquals('anulada', $saleActualizada->status);
        $this->assertNotNull($saleActualizada->deleted_at);
    }

    /**
     * Prueba 6: Permisos de Vendedor - Puede crear ventas y ver clientes, pero no eliminar productos ni ver categorías.
     */
    public function test_vendedor_permissions_and_restrictions(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $product = Product::first();

        // Vendedor puede ver el formulario de nueva venta
        $response = $this->actingAs($vendedor)->get(route('sales.create'));
        $response->assertStatus(200);

        // Vendedor puede ver el listado de clientes
        $responseClients = $this->actingAs($vendedor)->get(route('clients.index'));
        $responseClients->assertStatus(200);

        // Vendedor NO puede eliminar productos (403)
        $responseDelete = $this->actingAs($vendedor)->delete(route('productos.destroy', $product));
        $responseDelete->assertStatus(403);

        // Vendedor NO puede acceder a categorías (403)
        $responseCategorias = $this->actingAs($vendedor)->get(route('categorias.index'));
        $responseCategorias->assertStatus(403);
    }

    /**
     * Prueba 7: Permisos de Almacenista - No puede acceder al módulo de ventas (403).
     */
    public function test_almacenista_cannot_access_sales(): void
    {
        $almacenista = User::where('email', 'almacenista@test.com')->first();

        $response = $this->actingAs($almacenista)->get(route('sales.create'));
        $response->assertStatus(403);

        $responseStore = $this->actingAs($almacenista)->post(route('sales.store'), []);
        $responseStore->assertStatus(403);
    }

    /**
     * Prueba 8: Dashboard con KPIs y Top 5 productos más vendidos.
     */
    public function test_dashboard_displays_kpis_and_top_products(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Productos activos');
        $response->assertSee('Stock bajo');
        $response->assertSee('Valor del inventario');
        $response->assertSee('Clientes activos');
        $response->assertSee('Ventas del mes');
        $response->assertSee('Top 5 Productos más Vendidos');
    }
}
