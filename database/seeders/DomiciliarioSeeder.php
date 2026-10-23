<?php

namespace Database\Seeders;

use App\Models\Domiciliario;
use Illuminate\Database\Seeder;

class DomiciliarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domiciliarios = [
            [
                'nombre' => 'Diego Fernando Rojas',
                'telefono' => '3117894561',
                'documento' => '1113548920',
                'vehiculo' => 'Moto AKT NKD 125 (Placa QWE-45F)',
                'estado' => true,
            ],
            [
                'nombre' => 'Jhonatan Alexis Castro',
                'telefono' => '3128945612',
                'documento' => '1114789562',
                'vehiculo' => 'Moto Yamaha YBR 125 (Placa ASD-89G)',
                'estado' => true,
            ],
            [
                'nombre' => 'Cristian Camilo Pineda',
                'telefono' => '3139056723',
                'documento' => '1115896321',
                'vehiculo' => 'Moto Honda CB 125F (Placa ZXC-12E)',
                'estado' => true,
            ],
            [
                'nombre' => 'Brayan Stiven Ortiz',
                'telefono' => '3140167834',
                'documento' => '1116907432',
                'vehiculo' => 'Moto Suzuki GN 125 (Placa RTY-78D)',
                'estado' => true,
            ],
            [
                'nombre' => 'Yeison Daniel Guerrero',
                'telefono' => '3151278945',
                'documento' => '1117018543',
                'vehiculo' => 'Moto Bajaj Boxer CT 100 (Placa UIO-34C)',
                'estado' => true,
            ],
            [
                'nombre' => 'Edwin Alfonso Moreno',
                'telefono' => '3162389056',
                'documento' => '1118129654',
                'vehiculo' => 'Bicicleta Eléctrica Starker (Sin placa)',
                'estado' => true,
            ],
            [
                'nombre' => 'Kevin Mauricio Guzmán',
                'telefono' => '3173490167',
                'documento' => '1119230765',
                'vehiculo' => 'Moto TVS Apache 160 (Placa PAS-56B)',
                'estado' => true,
            ],
            [
                'nombre' => 'Sebastián Ramos Cruz',
                'telefono' => '3184501278',
                'documento' => '1120341876',
                'vehiculo' => 'Moto Hero Eco 100 (Placa DFH-90A)',
                'estado' => true,
            ],
            [
                'nombre' => 'Miller David Valencia',
                'telefono' => '3195612389',
                'documento' => '1121452987',
                'vehiculo' => 'Moto AKT Special 110 (Placa JKL-23M)',
                'estado' => true,
            ],
            [
                'nombre' => 'Víctor Manuel Zapata',
                'telefono' => '3206723490',
                'documento' => '1122563098',
                'vehiculo' => 'Moto Yamaha Crypton 115 (Placa BNM-67N)',
                'estado' => true,
            ],
        ];

        foreach ($domiciliarios as $domiciliario) {
            Domiciliario::create($domiciliario);
        }
    }
}
