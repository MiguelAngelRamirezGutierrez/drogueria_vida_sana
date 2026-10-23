<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Domiciliario;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pedidosData = [
            // 1. Recibido - Recién entrado a cocina
            [
                'cliente_id' => 1,
                'domiciliario_id' => null,
                'metodo_pago_id' => 2, // Nequi
                'direccion_entrega' => 'Carrera 15 # 45-20 Apto 302, Barrio El Bosque',
                'estado' => 'Recibido',
                'observaciones' => 'Timbrar en portería y anunciar que es de QuickFood.',
                'created_at' => Carbon::now()->subMinutes(10),
                'items' => [
                    ['producto_id' => 1, 'cantidad' => 2], // 2 x 18000 = 36000
                    ['producto_id' => 16, 'cantidad' => 2], // 2 x 6000 = 12000
                ], // Total = 48000
            ],
            // 2. Preparando - En plancha
            [
                'cliente_id' => 2,
                'domiciliario_id' => null,
                'metodo_pago_id' => 1, // Efectivo
                'direccion_entrega' => 'Calle 28 # 12-40, Barrio San Fernando',
                'estado' => 'Preparando',
                'observaciones' => 'Sin cebolla en la hamburguesa y salsas aparte.',
                'created_at' => Carbon::now()->subMinutes(25),
                'items' => [
                    ['producto_id' => 3, 'cantidad' => 1], // 1 x 28000 = 28000
                    ['producto_id' => 11, 'cantidad' => 1], // 1 x 15000 = 15000
                    ['producto_id' => 14, 'cantidad' => 1], // 1 x 7000 = 7000
                ], // Total = 50000
            ],
            // 3. Preparando - Pedido con combo
            [
                'cliente_id' => 3,
                'domiciliario_id' => null,
                'metodo_pago_id' => 4, // Bancolombia
                'direccion_entrega' => 'Avenida Las Palmas # 10-55 Casa 4, Conjunto Los Álamos',
                'estado' => 'Preparando',
                'observaciones' => 'Casa esquinera blanca con portón café.',
                'created_at' => Carbon::now()->subMinutes(35),
                'items' => [
                    ['producto_id' => 20, 'cantidad' => 1], // 1 x 85000 = 85000
                ], // Total = 85000
            ],
            // 4. Listo - Empacado esperando recogida de domiciliario
            [
                'cliente_id' => 4,
                'domiciliario_id' => 1, // Diego Rojas
                'metodo_pago_id' => 3, // Daviplata
                'direccion_entrega' => 'Manzana B Casa 12, Urbanización Los Sauces',
                'estado' => 'Listo',
                'observaciones' => 'Pedido empacado en bolsa térmica biodegradable.',
                'created_at' => Carbon::now()->subMinutes(45),
                'items' => [
                    ['producto_id' => 5, 'cantidad' => 2], // 2 x 16000 = 32000
                    ['producto_id' => 12, 'cantidad' => 1], // 1 x 25000 = 25000
                    ['producto_id' => 16, 'cantidad' => 2], // 2 x 6000 = 12000
                ], // Total = 69000
            ],
            // 5. Listo - Despacho asignado
            [
                'cliente_id' => 5,
                'domiciliario_id' => 2, // Jhonatan Castro
                'metodo_pago_id' => 2, // Nequi
                'direccion_entrega' => 'Transversal 5 # 18-90, Barrio El Jardín',
                'estado' => 'Listo',
                'observaciones' => 'Cliente pagó por Nequi anticipadamente. Comprobante verificado.',
                'created_at' => Carbon::now()->subMinutes(50),
                'items' => [
                    ['producto_id' => 9, 'cantidad' => 1], // 1 x 26000 = 26000
                    ['producto_id' => 15, 'cantidad' => 1], // 1 x 9000 = 9000
                ], // Total = 35000
            ],
            // 6. En camino - Domiciliario en ruta
            [
                'cliente_id' => 6,
                'domiciliario_id' => 3, // Cristian Pineda
                'metodo_pago_id' => 1, // Efectivo
                'direccion_entrega' => 'Calle 40 # 25-14 Interior 2, Barrio Santa Clara',
                'estado' => 'En camino',
                'observaciones' => 'Llevar cambio de $50.000.',
                'created_at' => Carbon::now()->subHours(1),
                'items' => [
                    ['producto_id' => 4, 'cantidad' => 1], // 1 x 32000 = 32000
                    ['producto_id' => 13, 'cantidad' => 1], // 1 x 14000 = 14000
                ], // Total = 46000
            ],
            // 7. En camino - Ruta rápida
            [
                'cliente_id' => 7,
                'domiciliario_id' => 4, // Brayan Ortiz
                'metodo_pago_id' => 5, // Tarjeta Débito Datáfono
                'direccion_entrega' => 'Carrera 8 # 22-31, Sector Centro Comercial',
                'estado' => 'En camino',
                'observaciones' => 'Llevar datáfono inalámbrico cargado.',
                'created_at' => Carbon::now()->subHours(1)->subMinutes(15),
                'items' => [
                    ['producto_id' => 7, 'cantidad' => 2], // 2 x 22000 = 44000
                    ['producto_id' => 17, 'cantidad' => 2], // 2 x 10000 = 20000
                ], // Total = 64000
            ],
            // 8. Entregado - Servicio finalizado hoy
            [
                'cliente_id' => 8,
                'domiciliario_id' => 5, // Yeison Guerrero
                'metodo_pago_id' => 1, // Efectivo
                'direccion_entrega' => 'Diagonal 14 # 33-08, Barrio La Pradera',
                'estado' => 'Entregado',
                'observaciones' => 'Entregado a satisfacción del cliente.',
                'created_at' => Carbon::now()->subHours(2),
                'items' => [
                    ['producto_id' => 2, 'cantidad' => 1], // 1 x 24000 = 24000
                    ['producto_id' => 18, 'cantidad' => 1], // 1 x 12000 = 12000
                ], // Total = 36000
            ],
            // 9. Entregado - Servicio finalizado hoy
            [
                'cliente_id' => 9,
                'domiciliario_id' => 6, // Edwin Moreno
                'metodo_pago_id' => 2, // Nequi
                'direccion_entrega' => 'Carrera 19 # 50-12 Piso 2, Barrio Bolívar',
                'estado' => 'Entregado',
                'observaciones' => 'Entrega sin novedades.',
                'created_at' => Carbon::now()->subHours(3),
                'items' => [
                    ['producto_id' => 10, 'cantidad' => 1], // 1 x 27000 = 27000
                    ['producto_id' => 19, 'cantidad' => 1], // 1 x 13000 = 13000
                ], // Total = 40000
            ],
            // 10. Entregado - Servicio finalizado ayer
            [
                'cliente_id' => 10,
                'domiciliario_id' => 7, // Kevin Guzmán
                'metodo_pago_id' => 4, // Bancolombia
                'direccion_entrega' => 'Calle 11 # 16-25, Barrio San Jorge',
                'estado' => 'Entregado',
                'observaciones' => 'Cliente frecuente de la zona.',
                'created_at' => Carbon::now()->subDay()->subHours(4),
                'items' => [
                    ['producto_id' => 8, 'cantidad' => 1], // 1 x 38000 = 38000
                    ['producto_id' => 16, 'cantidad' => 3], // 3 x 6000 = 18000
                ], // Total = 56000
            ],
            // 11. Entregado - Servicio finalizado ayer
            [
                'cliente_id' => 1,
                'domiciliario_id' => 8, // Sebastián Ramos
                'metodo_pago_id' => 6, // Tarjeta Crédito
                'direccion_entrega' => 'Carrera 15 # 45-20 Apto 302, Barrio El Bosque',
                'estado' => 'Entregado',
                'observaciones' => 'Pago aprobado exitosamente mediante datáfono.',
                'created_at' => Carbon::now()->subDays(2),
                'items' => [
                    ['producto_id' => 6, 'cantidad' => 2], // 2 x 20000 = 40000
                    ['producto_id' => 11, 'cantidad' => 1], // 1 x 15000 = 15000
                ], // Total = 55000
            ],
            // 12. Cancelado - Incidencia
            [
                'cliente_id' => 2,
                'domiciliario_id' => null,
                'metodo_pago_id' => 1, // Efectivo
                'direccion_entrega' => 'Calle 28 # 12-40, Barrio San Fernando',
                'estado' => 'Cancelado',
                'observaciones' => 'Cancelado por solicitud del cliente antes de ingresar a preparación.',
                'created_at' => Carbon::now()->subDays(3),
                'items' => [
                    ['producto_id' => 1, 'cantidad' => 1], // 1 x 18000 = 18000
                ], // Total = 18000
            ],
        ];

        foreach ($pedidosData as $data) {
            $items = $data['items'];
            unset($data['items']);

            // Crear cabecera de pedido temporal con total 0
            $pedido = Pedido::create($data);

            $totalCalculado = 0;

            foreach ($items as $item) {
                $producto = Producto::find($item['producto_id']);
                $subtotal = $producto->precio * $item['cantidad'];

                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ]);

                $totalCalculado += $subtotal;

                // Disminuir stock si el pedido no está cancelado
                if ($pedido->estado !== 'Cancelado') {
                    $producto->decrement('stock', $item['cantidad']);
                }
            }

            // Actualizar el total exacto del pedido
            $pedido->update(['total' => $totalCalculado]);
        }
    }
}
