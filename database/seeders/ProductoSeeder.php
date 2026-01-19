<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Ramos (Categoría 1)
            ['categoria_id' => 1, 'nombre' => 'Ramo Love', 'descripcion' => 'Ramo de fresas decoradas estilo Love.', 'precio' => 32.00, 'stock' => 20, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772862/ramo-love_xlluxm.jpg', 'alt_imagen' => 'Ramo Love'],
            ['categoria_id' => 1, 'nombre' => 'Ramo Colette', 'descripcion' => 'Ramo delicado con fresas decoradas.', 'precio' => 28.00, 'stock' => 20, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772854/ramo-cloe_whwfrr.jpg', 'alt_imagen' => 'Ramo Colette'],
            ['categoria_id' => 1, 'nombre' => 'Ramo Camille', 'descripcion' => 'Ramo dulce con acabado elegante.', 'precio' => 25.00, 'stock' => 20, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772861/ramo-camille_fn97tr.jpg', 'alt_imagen' => 'Ramo Camille'],
            ['categoria_id' => 1, 'nombre' => 'Ramo Cloé', 'descripcion' => 'Versión compacta y coqueta.', 'precio' => 15.00, 'stock' => 20, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772863/ramo-colette_coi7qd.jpg', 'alt_imagen' => 'Ramo Cloé'],

            // Cajas de Fresas (Categoría 2)
            ['categoria_id' => 2, 'nombre' => 'Caja 12 fresas', 'descripcion' => 'Docena de fresas decoradas.', 'precio' => 10.00, 'stock' => 30, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772855/caja-12-fresas_apftew.jpg', 'alt_imagen' => 'Caja 12 fresas'],
            ['categoria_id' => 2, 'nombre' => 'Caja de 9 fresas', 'descripcion' => 'Nueve fresas finamente decoradas.', 'precio' => 8.00, 'stock' => 30, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772853/caja-de-9-fresas_pssgry.jpg', 'alt_imagen' => 'Caja 9 fresas'],

            // Rosas y Fresas (Categoría 3)
            ['categoria_id' => 3, 'nombre' => 'Amelie', 'descripcion' => 'Caja con rosas y fresas.', 'precio' => 30.00, 'stock' => 15, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772860/amelie_zrsmt0.jpg', 'alt_imagen' => 'Amelie'],
            ['categoria_id' => 3, 'nombre' => 'Julieta', 'descripcion' => 'Combinación romántica de rosas y fresas.', 'precio' => 35.00, 'stock' => 15, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772853/julieta_nuduc5.jpg', 'alt_imagen' => 'Julieta'],
            ['categoria_id' => 3, 'nombre' => 'Jolie', 'descripcion' => 'Presentación premium.', 'precio' => 38.00, 'stock' => 10, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772858/jolie_ktnfen.jpg', 'alt_imagen' => 'Jolie'],
            ['categoria_id' => 3, 'nombre' => 'Giselle', 'descripcion' => 'Delicada y elegante.', 'precio' => 28.00, 'stock' => 15, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772862/giselle_flgixi.jpg', 'alt_imagen' => 'Giselle'],

            // Fresas con Licor (Categoría 4)
            ['categoria_id' => 4, 'nombre' => 'Damien', 'descripcion' => 'Fresas con licor selecto.', 'precio' => 60.00, 'stock' => 10, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772857/damien_vjxe02.jpg', 'alt_imagen' => 'Damien'],
            ['categoria_id' => 4, 'nombre' => 'Sofía', 'descripcion' => 'Caja con fresas y toque de licor.', 'precio' => 35.00, 'stock' => 12, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772855/sofia_s6auyb.jpg', 'alt_imagen' => 'Sofía'],
            ['categoria_id' => 4, 'nombre' => 'Box Corona', 'descripcion' => 'Fresas + cerveza Corona.', 'precio' => 35.00, 'stock' => 12, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772864/box-corona_wfoxde.jpg', 'alt_imagen' => 'Box Corona'],
            ['categoria_id' => 4, 'nombre' => 'Elliot', 'descripcion' => 'Combo fresco con licor.', 'precio' => 28.00, 'stock' => 12, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772852/elliot_kfiyyh.jpg', 'alt_imagen' => 'Elliot'],
            ['categoria_id' => 4, 'nombre' => 'Box Johnny Rojo', 'descripcion' => 'Fresas con whisky etiqueta roja.', 'precio' => 50.00, 'stock' => 8, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772856/box-johnny-rojo_kehqcw.jpg', 'alt_imagen' => 'Box Johnny Rojo'],

            // Pasteles (Categoría 5)
            ['categoria_id' => 5, 'nombre' => 'Pastel Kit Kat', 'descripcion' => 'Pastel decorado con barras Kit Kat.', 'precio' => 18.00, 'stock' => 20, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772858/pastel-kit-kat_p7dzsz.jpg', 'alt_imagen' => 'Pastel Kit Kat'],
            ['categoria_id' => 5, 'nombre' => 'Pastel Love', 'descripcion' => 'Pastel temático amor.', 'precio' => 25.00, 'stock' => 15, 'imagen_url' => 'https://res.cloudinary.com/dzxkufyfp/image/upload/v1768772859/pastel-love_ybuv2h.jpg', 'alt_imagen' => 'Pastel Love'],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
