<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo contra entrega', 'estado' => true],
            ['nombre' => 'Nequi', 'estado' => true],
            ['nombre' => 'Daviplata', 'estado' => true],
            ['nombre' => 'Transferencia Bancolombia', 'estado' => true],
            ['nombre' => 'Tarjeta Débito (Datáfono)', 'estado' => true],
            ['nombre' => 'Tarjeta Crédito (Datáfono)', 'estado' => true],
            ['nombre' => 'Billetera Dale!', 'estado' => true],
            ['nombre' => 'Movii', 'estado' => true],
            ['nombre' => 'Enlace de Pago Bold (PSE)', 'estado' => true],
            ['nombre' => 'Puntos y Cupones QuickFood', 'estado' => true],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::create($metodo);
        }
    }
}
