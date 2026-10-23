<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Carlos Andrés Gómez Pérez',
                'telefono' => '3104567890',
                'direccion' => 'Carrera 15 # 45-20 Apto 302, Barrio El Bosque',
                'email' => 'carlos.gomez@gmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Valentina Salazar Ríos',
                'telefono' => '3123456781',
                'direccion' => 'Calle 28 # 12-40, Barrio San Fernando',
                'email' => 'valentina.salazar@outlook.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Juan Camilo Morales Henao',
                'telefono' => '3156789012',
                'direccion' => 'Avenida Las Palmas # 10-55 Casa 4, Conjunto Los Álamos',
                'email' => 'jcamilo.morales@hotmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Laura Sofía Betancur López',
                'telefono' => '3189012345',
                'direccion' => 'Manzana B Casa 12, Urbanización Los Sauces',
                'email' => 'laura.betancur@gmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Mateo Jaramillo Osorio',
                'telefono' => '3001234567',
                'direccion' => 'Transversal 5 # 18-90, Barrio El Jardín',
                'email' => 'mateo.jaramillo@gmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Daniela Restrepo Cardona',
                'telefono' => '3112345678',
                'direccion' => 'Calle 40 # 25-14 Interior 2, Barrio Santa Clara',
                'email' => 'daniela.restrepo@gmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Santiago Duque Montoya',
                'telefono' => '3145678901',
                'direccion' => 'Carrera 8 # 22-31, Sector Centro Comercial',
                'email' => 'santi.duque@hotmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Mariana Vargas Echeverri',
                'telefono' => '3167890123',
                'direccion' => 'Diagonal 14 # 33-08, Barrio La Pradera',
                'email' => 'mariana.vargas@outlook.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Felipe Arango Quintero',
                'telefono' => '3178901234',
                'direccion' => 'Carrera 19 # 50-12 Piso 2, Barrio Bolívar',
                'email' => 'felipe.arango@gmail.com',
                'estado' => true,
            ],
            [
                'nombre' => 'Juliana Méndez Castro',
                'telefono' => '3209876543',
                'direccion' => 'Calle 11 # 16-25, Barrio San Jorge',
                'email' => 'juliana.mendez@gmail.com',
                'estado' => true,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
