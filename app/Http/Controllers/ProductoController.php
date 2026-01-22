<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductoController extends Controller
{
    /**
     * Mostrar catálogo completo agrupado por categorías
     */
    public function index()
    {
        try {
            // Flujo normal (si CATEGORIA está poblada en esta conexión)
            $categorias = Categoria::with('productos')
                ->whereHas('productos')
                ->get();

            // Fallback: si por distribución CATEGORIA está vacía pero PRODUCTO sí tiene datos
            if ($categorias->count() === 0) {
                $productos = Producto::query()->get();


                if ($productos->count() === 0) {
                    return view('productos.index', ['categorias' => collect()]);
                }

                $catCodigos = $productos->pluck('CAT_CODIGO')->filter()->unique()->values();

                // Intentar traer descripciones si existen en esta conexión (si no, igual funciona)
                $mapCat = Categoria::query()
                    ->whereIn('CAT_CODIGO', $catCodigos)
                    ->get()
                    ->keyBy('CAT_CODIGO');

                $agrupado = $productos->groupBy('CAT_CODIGO');

                $categorias = $agrupado->map(function (Collection $prods, $catCodigo) use ($mapCat) {
                    $cat = $mapCat->get($catCodigo);

                    // Objeto simple compatible con tu blade:
                    // $categoria->nombre, $categoria->descripcion, $categoria->productos
                    return (object) [
                        'id' => (string) $catCodigo,
                        'nombre' => $cat ? $cat->nombre : (string) $catCodigo,
                        'descripcion' => $cat ? $cat->descripcion : null,
                        'productos' => $prods->values(),
                    ];
                })->values();
            }

            return view('productos.index', compact('categorias'));
        } catch (\Throwable $e) {
            \Log::error('Error cargando /productos: ' . $e->getMessage());

            // Mensaje simple (sin stacktrace al usuario)
            return view('productos.index', [
                'categorias' => collect(),
                'general_error' => 'No hay productos para mostrar.'
            ]);
        }
    }

    /**
     * Mostrar detalle de un producto específico
     */
    public function show($id)
    {
        try {
            $producto = Producto::with('categoria')->findOrFail($id);

            $productosSimilares = Producto::where('CAT_CODIGO', $producto->CAT_CODIGO)
                ->where('PRO_CODIGO', '!=', $producto->PRO_CODIGO)
                ->activos()
                ->get();

            return view('productos.show', compact('producto', 'productosSimilares'));
        } catch (\Throwable $e) {
            \Log::error('Error show producto: ' . $e->getMessage());
            return redirect()->route('productos.index')
                ->withErrors(['general' => 'No se pudo cargar el producto.']);
        }
    }

    /**
     * Productos por categoría
     */
    public function porCategoria($categoriaId)
    {
        try {
            // Intento normal
            $categoria = Categoria::with('productos')->find($categoriaId);

            if ($categoria) {
                return view('productos.categoria', compact('categoria'));
            }

            // Fallback: si CATEGORIA no existe aquí, pero PRODUCTO sí
            $productos = Producto::where('CAT_CODIGO', (string) $categoriaId)->activos()->get();

            if ($productos->count() === 0) {
                return redirect()->route('productos.index')
                    ->withErrors(['general' => 'No hay productos para mostrar.']);
            }

            $categoria = (object) [
                'id' => (string) $categoriaId,
                'nombre' => (string) $categoriaId,
                'descripcion' => null,
                'productos' => $productos,
            ];

            return view('productos.categoria', compact('categoria'));
        } catch (\Throwable $e) {
            \Log::error('Error porCategoria: ' . $e->getMessage());
            return redirect()->route('productos.index')
                ->withErrors(['general' => 'No hay productos para mostrar.']);
        }
    }
}
