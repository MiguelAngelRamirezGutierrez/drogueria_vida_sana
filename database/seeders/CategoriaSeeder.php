<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Hamburguesas Clásicas',
                'descripcion' => 'Hamburguesas tradicionales de res y pollo con queso americano, lechuga fresca y salsas artesanales de la casa.',
                'estado' => true,
            ],
            [
                'nombre' => 'Hamburguesas Especiales',
                'descripcion' => 'Recetas gourmet de autor con doble carne angus, tocineta ahumada, cebolla caramelizada y queso costeño asado.',
                'estado' => true,
            ],
            [
                'nombre' => 'Perros Calientes',
                'descripcion' => 'Hot dogs estilo colombiano con salchicha zenú o suiza, tocineta crocante, papa fosforito y queso mozzarella fundido.',
                'estado' => true,
            ],
            [
                'nombre' => 'Salchipapas Gourmet',
                'descripcion' => 'Papas a la francesa seleccionadas con variedad de carnes, salchicha manguera, queso costeño rallado y huevo de codorniz.',
                'estado' => true,
            ],
            [
                'nombre' => 'Mazorcadas y Desgranados',
                'descripcion' => 'Mazorca tierna desgranada con pollo y lomo de res a la plancha, tártara de la casa, tocineta y abundante queso gratinado.',
                'estado' => true,
            ],
            [
                'nombre' => 'Acompañamientos y Entradas',
                'descripcion' => 'Porciones perfectas para compartir: aros de cebolla crocantes, papas rústicas con cheddar, deditos de queso y alitas.',
                'estado' => true,
            ],
            [
                'nombre' => 'Bebidas Frías y Jugos',
                'descripcion' => 'Jugos naturales preparados al instante en agua o en leche con pulpa 100% de frutas tropicales seleccionadas.',
                'estado' => true,
            ],
            [
                'nombre' => 'Gaseosas y Cervezas',
                'descripcion' => 'Bebidas carbonatadas frías de línea Coca-Cola y Postobón, además de cervezas nacionales e importadas.',
                'estado' => true,
            ],
            [
                'nombre' => 'Postres y Batidos',
                'descripcion' => 'Batidos y malteadas cremosas con helado artesanal, brownies calientes y postres para finalizar la comida.',
                'estado' => true,
            ],
            [
                'nombre' => 'Combos Familiares',
                'descripcion' => 'Opciones combinadas de alta economía con hamburguesas, porciones gigantes de papas y bebidas para 2 a 5 personas.',
                'estado' => true,
            ],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }
    }
}
