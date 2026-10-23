<?php

namespace Tests\Feature;

use App\Models\Lot;
use App\Models\Product;
use App\Models\User;
use App\Services\DispensingService;
use Database\Seeders\DrogueriaProductSeeder;
use Database\Seeders\DrogueriaRolesSeeder;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SegundoParcialDrogueriaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DrogueriaRolesSeeder::class);
        $this->seed(DrogueriaProductSeeder::class);
    }

    /**
     * Prueba 1: Verifica la migración de lotes, unicidad y relaciones Eloquent.
     */
    public function test_lots_table_and_relationships_work_properly(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        // Crear lote
        $lot = Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'LOT-TEST-001',
            'expiration_date' => now()->addMonths(6),
            'quantity' => 25,
        ]);

        $this->assertDatabaseHas('lots', [
            'id' => $lot->id,
            'product_id' => $product->id,
            'lot_number' => 'LOT-TEST-001',
            'quantity' => 25,
        ]);

        // Verificar relación bidireccional
        $this->assertEquals($product->id, $lot->product->id);
        $this->assertTrue($product->lots->contains($lot));

        // Verificar intento de duplicar lote para el mismo producto (índice único)
        $this->expectException(\Illuminate\Database\QueryException::class);
        Lot::create([
            'product_id' => $product->id,
            'lot_number' => 'LOT-TEST-001',
            'expiration_date' => now()->addMonths(12),
            'quantity' => 10,
        ]);
    }

    /**
     * Prueba 2: Verifica la matriz de seguridad con Spatie (Roles, Permisos y Segregación).
     */
    public function test_drogueria_roles_and_permissions_seeder(): void
    {
        // 1. Roles existentes
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'regente')->exists());
        $this->assertTrue(Role::where('name', 'cajero')->exists());
        $this->assertTrue(Role::where('name', 'auxiliar-bodega')->exists());

        // 2. Usuarios con roles asignados
        $admin = User::where('email', 'admin@vidasana.com')->first();
        $regente = User::where('email', 'regente@vidasana.com')->first();
        $cajero = User::where('email', 'cajero@vidasana.com')->first();
        $bodega = User::where('email', 'bodega@vidasana.com')->first();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($regente->hasRole('regente'));
        $this->assertTrue($cajero->hasRole('cajero'));
        $this->assertTrue($bodega->hasRole('auxiliar-bodega'));

        // 3. Regente: tiene recepción técnica y control especial, pero no crea compras
        $this->assertTrue($regente->can('recibir-lotes'));
        $this->assertTrue($regente->can('autorizar-control-especial'));
        $this->assertFalse($regente->can('crear-compras'));

        // 4. Cajero: tiene ventas, pero no recibe lotes ni autoriza control especial
        $this->assertTrue($cajero->can('crear-ventas'));
        $this->assertFalse($cajero->can('recibir-lotes'));
        $this->assertFalse($cajero->can('autorizar-control-especial'));

        // 5. Auxiliar de bodega: tiene descarga física, pero no ventas ni compras
        $this->assertTrue($bodega->can('descargar-mercancia'));
        $this->assertFalse($bodega->can('crear-ventas'));
        $this->assertFalse($bodega->can('crear-compras'));
    }

    /**
     * Prueba 3: Simulación obligatoria del examen en DispensingService:
     * 3 lotes (L1 vencido=10, L2 vence 1 mes=5, L3 vence 1 año=15), dispensar 8 unidades.
     */
    public function test_dispensing_service_ignores_expired_lots_and_applies_fefo(): void
    {
        $p = Product::first();
        Lot::where('product_id', $p->id)->forceDelete();
        $initialProductStock = $p->stock;

        // Crear los 3 lotes requeridos por el examen
        $l1 = Lot::create([
            'product_id' => $p->id,
            'lot_number' => 'L1',
            'expiration_date' => now()->subDay(), // Vencido ayer
            'quantity' => 10,
        ]);

        $l2 = Lot::create([
            'product_id' => $p->id,
            'lot_number' => 'L2',
            'expiration_date' => now()->addMonth(), // Vence en 1 mes
            'quantity' => 5,
        ]);

        $l3 = Lot::create([
            'product_id' => $p->id,
            'lot_number' => 'L3',
            'expiration_date' => now()->addYear(), // Vence en 1 año
            'quantity' => 15,
        ]);

        // Ejecutar servicio
        $service = app(DispensingService::class);
        $result = $service->dispense($p, 8);

        // Aserciones de la respuesta
        $this->assertEquals(8, $result['total_dispensed']);
        $this->assertEquals($initialProductStock - 8, $result['product_new_stock']);

        // Aserciones sobre los lotes:
        // L1 (Vencido): DEBE CONTINUAR CON 10 (cero jarabes vencidos vendidos)
        $this->assertEquals(10, $l1->fresh()->quantity);

        // L2 (Vence en 1 mes): DEBE HABERSE AGOTADO (prioridad 1 FEFO)
        $this->assertEquals(0, $l2->fresh()->quantity);

        // L3 (Vence en 1 año): DEBE HABER CUBIERTO LAS 3 RESTANTES (15 - 3 = 12)
        $this->assertEquals(12, $l3->fresh()->quantity);

        // Stock general del producto disminuyó en 8
        $this->assertEquals($initialProductStock - 8, $p->fresh()->stock);
    }

    /**
     * Prueba 4: Validación de stock no vencido insuficiente y verificación del Rollback.
     */
    public function test_dispensing_service_fails_when_insufficient_unexpired_stock_and_rolls_back(): void
    {
        $p = Product::first();
        Lot::where('product_id', $p->id)->forceDelete();

        $l1 = Lot::create([
            'product_id' => $p->id,
            'lot_number' => 'L1',
            'expiration_date' => now()->subDay(), // Vencido: 10 uds
            'quantity' => 10,
        ]);

        $l2 = Lot::create([
            'product_id' => $p->id,
            'lot_number' => 'L2',
            'expiration_date' => now()->addMonth(), // Vigente: 5 uds
            'quantity' => 5,
        ]);

        $initialProductStock = $p->stock;
        $service = app(DispensingService::class);

        // Intentar dispensar 8 unidades (hay 15 en total en la BD, pero solo 5 vigentes)
        try {
            $service->dispense($p, 8);
            $this->fail('Se esperaba una ValidationException debido a que el stock vigente no alcanza.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('stock', $e->errors());
            $this->assertStringContainsString('Stock insuficiente no vencido', $e->errors()['stock'][0]);
            $this->assertStringContainsString('Se bloquearon 10 unidades en lotes vencidos', $e->errors()['stock'][0]);
        }

        // Verificar Rollback completo
        $this->assertEquals(10, $l1->fresh()->quantity);
        $this->assertEquals(5, $l2->fresh()->quantity);
        $this->assertEquals($initialProductStock, $p->fresh()->stock);
    }
}
