<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            // Cat 1: Hamburguesas Clásicas
            [
                'categoria_id' => 1,
                'nombre' => 'Hamburguesa Clásica Sencilla',
                'descripcion' => 'Carne 150g artesanal, queso mozzarella, tomate, lechuga y salsas de la casa en pan brioche.',
                'precio' => 18000.00,
                'stock' => 50,
                'estado' => true,
            ],
            [
                'categoria_id' => 1,
                'nombre' => 'Hamburguesa Clásica Doble Carne',
                'descripcion' => 'Doble porción de carne 150g (300g total), doble queso americano, vegetales frescos y tocineta.',
                'precio' => 24000.00,
                'stock' => 40,
                'estado' => true,
            ],
            // Cat 2: Hamburguesas Especiales
            [
                'categoria_id' => 2,
                'nombre' => 'Hamburguesa QuickFood Deluxe BBQ',
                'descripcion' => 'Carne angus 200g, aro de cebolla crocante, tocineta ahumada al barril, queso cheddar fundido y salsa BBQ artesanal.',
                'precio' => 28000.00,
                'stock' => 35,
                'estado' => true,
            ],
            [
                'categoria_id' => 2,
                'nombre' => 'Hamburguesa Costeña Monstruosa',
                'descripcion' => 'Carne de res 180g, tajada de queso costeño asado a la plancha, plátano maduro melado y ripio crocante.',
                'precio' => 32000.00,
                'stock' => 25,
                'estado' => true,
            ],
            // Cat 3: Perros Calientes
            [
                'categoria_id' => 3,
                'nombre' => 'Perro Especial Colombiano',
                'descripcion' => 'Salchicha zenú premium, tocineta picada crocante, papa fosforito, queso costeño rallado, piña y tártara.',
                'precio' => 16000.00,
                'stock' => 45,
                'estado' => true,
            ],
            [
                'categoria_id' => 3,
                'nombre' => 'Perro Suizo Doble Queso',
                'descripcion' => 'Salchicha suiza ahumada con corte mariposa, doble queso mozzarella gratinado y tocineta premium.',
                'precio' => 20000.00,
                'stock' => 30,
                'estado' => true,
            ],
            // Cat 4: Salchipapas Gourmet
            [
                'categoria_id' => 4,
                'nombre' => 'Salchipapa Tradicional Quick',
                'descripcion' => 'Papas a la francesa doradas, salchicha manguera en rodajas, salsa rosada, piña y queso costeño.',
                'precio' => 22000.00,
                'stock' => 35,
                'estado' => true,
            ],
            [
                'categoria_id' => 4,
                'nombre' => 'Salchipapa Mixta Familiar (Especial)',
                'descripcion' => 'Cama gigante de papas francesas y criollas, pollo desmechado, carne desmechada, salchicha y huevos de codorniz.',
                'precio' => 38000.00,
                'stock' => 20,
                'estado' => true,
            ],
            // Cat 5: Mazorcadas y Desgranados
            [
                'categoria_id' => 5,
                'nombre' => 'Desgranado Mixto Pollo y Lomo',
                'descripcion' => 'Mazorca tierna dulce desgranada, pechuga a la plancha, lomo de res, tocineta, queso mozzarella y tártara.',
                'precio' => 26000.00,
                'stock' => 25,
                'estado' => true,
            ],
            [
                'categoria_id' => 5,
                'nombre' => 'Mazorcada Especial Gratinada',
                'descripcion' => 'Mazorca tierna cubierta de generosa capa de queso mozzarella derretido, papa ripio y salsa de ajo suave.',
                'precio' => 27000.00,
                'stock' => 25,
                'estado' => true,
            ],
            // Cat 6: Acompañamientos y Entradas
            [
                'categoria_id' => 6,
                'nombre' => 'Papas Rústicas con Cheddar y Tocineta',
                'descripcion' => 'Papas rústicas sazonadas con finas hierbas, bañadas en queso cheddar caliente y lluvia de tocineta crocante.',
                'precio' => 15000.00,
                'stock' => 40,
                'estado' => true,
            ],
            [
                'categoria_id' => 6,
                'nombre' => 'Alitas BBQ Crispy x8 piezas',
                'descripcion' => '8 piezas de alitas de pollo apanadas y bañadas en salsa BBQ miel de la casa, acompañadas de apio y salsa ranch.',
                'precio' => 25000.00,
                'stock' => 30,
                'estado' => true,
            ],
            [
                'categoria_id' => 6,
                'nombre' => 'Deditos de Queso x6 unidades',
                'descripcion' => '6 deditos de masa hojaldrada rellenos de abundante queso blanco derretido, servidos con salsa tártara.',
                'precio' => 14000.00,
                'stock' => 40,
                'estado' => true,
            ],
            // Cat 7: Bebidas Frías y Jugos
            [
                'categoria_id' => 7,
                'nombre' => 'Jugo Natural en Agua (Mora / Maracuyá)',
                'descripcion' => 'Jugo natural 16oz preparado con fruta 100% natural a elección del cliente.',
                'precio' => 7000.00,
                'stock' => 60,
                'estado' => true,
            ],
            [
                'categoria_id' => 7,
                'nombre' => 'Jugo Natural en Leche (Fresa / Mango)',
                'descripcion' => 'Jugo frappé en leche entera o deslactosada, cremosa textura 16oz.',
                'precio' => 9000.00,
                'stock' => 50,
                'estado' => true,
            ],
            // Cat 8: Gaseosas y Cervezas
            [
                'categoria_id' => 8,
                'nombre' => 'Coca-Cola Original 400ml Botella',
                'descripcion' => 'Botella personal bien fría de Coca-Cola original.',
                'precio' => 6000.00,
                'stock' => 80,
                'estado' => true,
            ],
            [
                'categoria_id' => 8,
                'nombre' => 'Cerveza Corona Extra 355ml',
                'descripcion' => 'Cerveza rubia tipo pilsner importada fría, servida con rodaja de limón.',
                'precio' => 10000.00,
                'stock' => 40,
                'estado' => true,
            ],
            // Cat 9: Postres y Batidos
            [
                'categoria_id' => 9,
                'nombre' => 'Malteada de Vainilla y Caramelo',
                'descripcion' => 'Malteada espesa de helado artesanal de vainilla con chantilly y salsa de caramelo toffee.',
                'precio' => 12000.00,
                'stock' => 30,
                'estado' => true,
            ],
            [
                'categoria_id' => 9,
                'nombre' => 'Brownie Caliente con Helado',
                'descripcion' => 'Brownie húmedo con nueces servido caliente con bola de helado de vainilla y fudge de chocolate.',
                'precio' => 13000.00,
                'stock' => 25,
                'estado' => true,
            ],
            // Cat 10: Combos Familiares
            [
                'categoria_id' => 10,
                'nombre' => 'Combo Fiesta QuickFood 4 Personas',
                'descripcion' => '4 Hamburguesas Clásicas + Porción familiar de papas francesas + 4 Gaseosas 400ml.',
                'precio' => 85000.00,
                'stock' => 15,
                'estado' => true,
            ],
        ];

        foreach ($productos as $prod) {
            Producto::create($prod);
        }
    }
}
