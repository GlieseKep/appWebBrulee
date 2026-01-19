<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar catálogo completo agrupado por categorías
     */
    public function index()
    {
        $categorias = Categoria::with('productos')
            ->whereHas('productos')
            ->get();

        return view('productos.index', compact('categorias'));
    }

    /**
     * Mostrar detalle de un producto específico
     */
    public function show($id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);

        // Obtener otros productos de la misma categoría
        $productosSimilares = Producto::where('categoria_id', $producto->categoria_id)
            ->where('id', '!=', $id)
            ->activos()
            ->get();

        return view('productos.show', compact('producto', 'productosSimilares'));
    }

    /**
     * Productos por categoría
     */
    public function porCategoria($categoriaId)
    {
        $categoria = Categoria::with('productos')->findOrFail($categoriaId);

        return view('productos.categoria', compact('categoria'));
    }
}
