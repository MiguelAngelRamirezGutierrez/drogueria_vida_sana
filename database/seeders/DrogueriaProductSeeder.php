<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Lot;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DrogueriaProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías de la Droguería Vida Sana
        $catVentaLibre = Category::firstOrCreate(
            ['nombre' => 'Medicamentos de Venta Libre'],
            ['descripcion' => 'Fármacos de venta libre sin fórmula médica obligatoria', 'estado' => true]
        );

        $catFormula = Category::firstOrCreate(
            ['nombre' => 'Medicamentos con Fórmula Médica'],
            ['descripcion' => 'Antibióticos y tratamientos que requieren prescripción médica', 'estado' => true]
        );

        $catControl = Category::firstOrCreate(
            ['nombre' => 'Medicamentos de Control Especial'],
            ['descripcion' => 'Estupefacientes y psicotrópicos sujetos a fiscalización y retención de receta', 'estado' => true]
        );

        $catCuidado = Category::firstOrCreate(
            ['nombre' => 'Cuidado Personal y Dispositivos'],
            ['descripcion' => 'Insumos hospitalarios, jeringas, gasas y aseo personal', 'estado' => true]
        );

        // Medicamentos con especificaciones farmacéuticas
        $products = [
            [
                'nombre' => 'Acetaminofén 500mg Tabletas',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Analgésico y antipirético. Caja x 100 tabletas. INVIMA 2018M-0008451-R1',
                'precio' => 12500,
                'stock' => 55,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-ACT-2024-VENCIDO', 'expiration_date' => now()->subDays(10)->toDateString(), 'quantity' => 10], // Vencido
                    ['lot_number' => 'L-ACT-2026-PRIORIDAD1', 'expiration_date' => now()->addDays(28)->toDateString(), 'quantity' => 15], // Vence en 28 días
                    ['lot_number' => 'L-ACT-2027-PRIORIDAD2', 'expiration_date' => now()->addMonths(14)->toDateString(), 'quantity' => 30], // Vence en 14 meses
                ]
            ],
            [
                'nombre' => 'Amoxicilina 500mg Cápsulas',
                'categoria_id' => $catFormula->id,
                'descripcion' => 'Antibiótico betalactámico bactericida. Caja x 30. INVIMA 2019M-0012345',
                'precio' => 24000,
                'stock' => 45,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-AMX-2026-LOTE1', 'expiration_date' => now()->addDays(45)->toDateString(), 'quantity' => 20],
                    ['lot_number' => 'L-AMX-2027-LOTE2', 'expiration_date' => now()->addMonths(18)->toDateString(), 'quantity' => 25],
                ]
            ],
            [
                'nombre' => 'Tramadol Clorhidrato 50mg Ampollas',
                'categoria_id' => $catControl->id,
                'descripcion' => 'Analgésico opioide de control especial. Requiere autorización del Regente. INVIMA 2020M-0015987',
                'precio' => 38000,
                'stock' => 25,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-TRM-CTRL-88', 'expiration_date' => now()->addMonths(8)->toDateString(), 'quantity' => 25],
                ]
            ],
            [
                'nombre' => 'Jarabe Antitusivo Expectorante 120ml',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Jarabe para tos seca y con flema. Frasco 120ml. INVIMA 2017M-0009876',
                'precio' => 18500,
                'stock' => 30,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-JAR-2026-01', 'expiration_date' => now()->addMonths(6)->toDateString(), 'quantity' => 30],
                ]
            ],
            [
                'nombre' => 'Ibuprofeno 800mg Tabletas',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Antiinflamatorio no esteroideo (AINE). Caja x 50. INVIMA 2018M-0007412',
                'precio' => 16000,
                'stock' => 40,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-IBU-VENCIDO-99', 'expiration_date' => now()->subDays(30)->toDateString(), 'quantity' => 5], // Vencido
                    ['lot_number' => 'L-IBU-2027-A', 'expiration_date' => now()->addMonths(10)->toDateString(), 'quantity' => 35],
                ]
            ],
            [
                'nombre' => 'Loratadina 10mg Tabletas',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Antihistamínico antialérgico no sedante. Caja x 20. INVIMA 2021M-0018520',
                'precio' => 9500,
                'stock' => 50,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-LOR-2026-33', 'expiration_date' => now()->addDays(75)->toDateString(), 'quantity' => 50],
                ]
            ],
            [
                'nombre' => 'Glucómetro Digital Accu-Chek',
                'categoria_id' => $catCuidado->id,
                'descripcion' => 'Dispositivo médico para monitoreo de glucosa capilar. INVIMA 2022DM-0021458',
                'precio' => 85000,
                'stock' => 12,
                'estado' => true,
                'lots' => [
                    ['lot_number' => 'L-DM-ACCU-2028', 'expiration_date' => now()->addMonths(24)->toDateString(), 'quantity' => 12],
                ]
            ],
        ];

        foreach ($products as $prodData) {
            $lots = $prodData['lots'] ?? [];
            unset($prodData['lots']);

            $product = Product::firstOrCreate(
                ['nombre' => $prodData['nombre']],
                $prodData
            );

            foreach ($lots as $lotData) {
                Lot::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'lot_number' => $lotData['lot_number'],
                    ],
                    [
                        'expiration_date' => $lotData['expiration_date'],
                        'quantity' => $lotData['quantity'],
                    ]
                );
            }
        }
    }
}
