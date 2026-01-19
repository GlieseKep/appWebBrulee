<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Ramos', 'descripcion' => 'Ramos de fresas decoradas para toda ocasión.'],
            ['nombre' => 'Cajas de Fresas', 'descripcion' => 'Cajas con fresas decoradas de distintos tamaños.'],
            ['nombre' => 'Rosas y Fresas', 'descripcion' => 'Combinaciones románticas de fresas y rosas.'],
            ['nombre' => 'Fresas con Licor', 'descripcion' => 'Fresas bañadas en chocolate con un toque de licor.'],
            ['nombre' => 'Pasteles', 'descripcion' => 'Pasteles decorativos y temáticos.'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
