<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DatosCliFactura;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.index');
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'cedula' => 'required|numeric|digits:10',
            'direccion' => 'required|string|max:200',
            'metodo_pago' => 'required|string',
            'numero_tarjeta' => 'required|numeric|digits:16',
            'mes_caducidad' => 'required|numeric|min:1|max:12',
            'year_caducidad' => 'required|numeric|min:' . date('Y'),
            'codigo_seguridad' => 'required|numeric|digits:3',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'numeric' => 'El campo :attribute debe ser solo números.',
            'digits' => 'El campo :attribute debe tener exactamente :digits dígitos.',
            'min' => 'El valor de :attribute no es válido.',
            'max' => 'El valor de :attribute es muy largo.',
        ]);

        $aceptaTerminos = $request->has('acepta_terminos') ? 'S' : 'N';

        if ($aceptaTerminos === 'S') {
            // Guardar o actualizar datos de facturación
            DatosCliFactura::updateOrCreate(
                ['CLI_CEDULA' => $request->cedula],
                [
                    'DCF_CODIGO' => (string) Str::uuid(),
                    'DCF_NOMBRE' => $request->nombre,
                    'DCF_DIRECCION' => $request->direccion,
                    'DCF_METODO_PAGO' => $request->metodo_pago,
                    'DCF_NUMERO_TARJETA' => $request->numero_tarjeta,
                    'DCF_MES_CADUCIDAD' => $request->mes_caducidad,
                    'DCF_YEAR_CADUCIDAD' => $request->year_caducidad,
                    'DCF_CODIGO_SEGURIDAD' => $request->codigo_seguridad,
                    'DCF_ACEPTA_TERMINOS' => 'S'
                ]
            );
        }

        // Simular éxito de compra
        return redirect()->route('home')->with('purchase_success', '¡Compra realizada con éxito! Muchas gracias por su preferencia.');
    }
}
