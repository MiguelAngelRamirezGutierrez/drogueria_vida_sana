<?php

namespace Database\Seeders;

use App\Models\Category;
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
                'stock' => 50,
                'estado' => true,
            ],
            [
                'nombre' => 'Amoxicilina 500mg Cápsulas',
                'categoria_id' => $catFormula->id,
                'descripcion' => 'Antibiótico betalactámico bactericida. Caja x 30. INVIMA 2019M-0012345',
                'precio' => 24000,
                'stock' => 40,
                'estado' => true,
            ],
            [
                'nombre' => 'Tramadol Clorhidrato 50mg Ampollas',
                'categoria_id' => $catControl->id,
                'descripcion' => 'Analgésico opioide de control especial. Requiere autorización del Regente. INVIMA 2020M-0015987',
                'precio' => 38000,
                'stock' => 25,
                'estado' => true,
            ],
            [
                'nombre' => 'Jarabe Antitusivo Expectorante 120ml',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Jarabe para tos seca y con flema. Frasco 120ml. INVIMA 2017M-0009876',
                'precio' => 18500,
                'stock' => 30,
                'estado' => true,
            ],
            [
                'nombre' => 'Ibuprofeno 800mg Tabletas',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Antiinflamatorio no esteroideo (AINE). Caja x 50. INVIMA 2018M-0007412',
                'precio' => 16000,
                'stock' => 35,
                'estado' => true,
            ],
            [
                'nombre' => 'Loratadina 10mg Tabletas',
                'categoria_id' => $catVentaLibre->id,
                'descripcion' => 'Antihistamínico antialérgico no sedante. Caja x 20. INVIMA 2021M-0018520',
                'precio' => 9500,
                'stock' => 45,
                'estado' => true,
            ],
            [
                'nombre' => 'Glucómetro Digital Accu-Chek',
                'categoria_id' => $catCuidado->id,
                'descripcion' => 'Dispositivo médico para monitoreo de glucosa capilar. INVIMA 2022DM-0021458',
                'precio' => 85000,
                'stock' => 12,
                'estado' => true,
            ],
        ];

        foreach ($products as $prodData) {
            Product::firstOrCreate(
                ['nombre' => $prodData['nombre']],
                $prodData
            );
        }
    }
}
