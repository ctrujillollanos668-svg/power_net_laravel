<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class ClienteMetodoPagoController extends Controller
{
    /**
     * Muestra la página informativa y de canales oficiales de pago para los clientes.
     */
    public function index()
    {
        $metodos = MetodoPago::where('estado', 1)->get();

        return view('cliente.metodospago.index', compact('metodos'));
    }
}
