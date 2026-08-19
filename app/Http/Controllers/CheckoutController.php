<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\User;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Muestra la pantalla de Checkout / Continuar con el pago.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío. Agrega productos antes de pagar.');
        }

        // Sincronizar y validar stock actual
        $cartModificado = false;
        foreach ($cart as $id => &$item) {
            $producto = Producto::find($item['id'] ?? $id);
            if (!$producto || (int) $producto->stock <= 0) {
                unset($cart[$id]);
                $cartModificado = true;
                continue;
            }

            $stockActual = (int) $producto->stock;
            $item['stock'] = $stockActual;

            if ($item['cantidad'] > $stockActual) {
                $item['cantidad'] = $stockActual;
                $cartModificado = true;
            }
        }
        unset($item);

        if ($cartModificado) {
            session()->put('cart', $cart);
            if (empty($cart)) {
                return redirect()->route('carrito.index')->with('error', 'Los productos en tu carrito ya no tienen stock disponible.');
            }
        }

        $subtotal = 0;
        $descuentoTotal = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
            if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
                $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
            }
            $totalItems += $item['cantidad'];
        }

        $costoEnvio = ($subtotal - $descuentoTotal > 150000) ? 0 : 12000;
        $total = ($subtotal - $descuentoTotal) + $costoEnvio;

        // Información precargada del usuario autenticado si existe
        $user = Auth::user();

        // Obtener métodos de pago activos configurados por el administrador
        $metodosPago = MetodoPago::where('estado', 1)->get();

        return view('cliente.checkout.Checkout', compact(
            'cart',
            'subtotal',
            'descuentoTotal',
            'costoEnvio',
            'total',
            'totalItems',
            'user',
            'metodosPago'
        ));
    }

    /**
     * Procesa la compra y registra la transacción en la base de datos.
     */
    public function procesar(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        // Validar stock antes de procesar el pago
        foreach ($cart as $item) {
            $producto = Producto::find($item['id']);
            if (!$producto || (int) $producto->stock < $item['cantidad']) {
                $disponible = $producto ? (int) $producto->stock : 0;
                return redirect()->route('carrito.index')->with('error', "No hay suficiente stock para \"{$item['nombre']}\". Stock disponible: {$disponible}. Por favor actualiza tu carrito.");
            }
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'documento' => 'nullable|string|max:50',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'metodo_pago_id' => 'required|exists:metodos_pagos,id',
            'notas' => 'nullable|string|max:500',
        ]);

        try {
            $pedido = DB::transaction(function () use ($validated, $cart, $request) {
                // 1. Obtener o Crear Persona y Cliente
                $user = Auth::user();
                $persona = null;

                if ($user && $user->persona_id) {
                    $persona = Persona::find($user->persona_id);
                }

                if (!$persona) {
                    $persona = Persona::create([
                        'nombre_persona' => $validated['nombre'],
                        'telefono' => $validated['telefono'],
                        'documento' => $validated['documento'] ?? null,
                    ]);

                    if ($user) {
                        $user->persona_id = $persona->id;
                        $user->save();
                    }
                } else {
                    $persona->update([
                        'nombre_persona' => $validated['nombre'],
                        'telefono' => $validated['telefono'],
                        'documento' => $validated['documento'] ?? $persona->documento,
                    ]);
                }

                $cliente = Cliente::where('persona_id', $persona->id)->first();
                if (!$cliente) {
                    $cliente = Cliente::create([
                        'direccion' => $validated['direccion'] . ', ' . $validated['ciudad'] . ', ' . $validated['departamento'],
                        'persona_id' => $persona->id,
                    ]);
                } else {
                    $cliente->update([
                        'direccion' => $validated['direccion'] . ', ' . $validated['ciudad'] . ', ' . $validated['departamento'],
                    ]);
                }

                // 2. Calcular Totales
                $subtotal = 0;
                $descuentoTotal = 0;
                foreach ($cart as $item) {
                    $subtotal += $item['precio'] * $item['cantidad'];
                    if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
                        $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
                    }
                }

                $costoEnvio = ($subtotal - $descuentoTotal > 150000) ? 0 : 12000;
                $total = ($subtotal - $descuentoTotal) + $costoEnvio;

                // 3. Crear Pedido
                $pedido = Pedido::create([
                    'fecha_pedido' => now(),
                    'total_pedido' => $total,
                    'estado_pedido' => 'En preparación',
                    'cliente_id' => $cliente->id,
                ]);

                // 4. Crear Detalles del Pedido y Actualizar Stock
                foreach ($cart as $id => $item) {
                    $precioUnitario = $item['precio_oferta'] ?? $item['precio'];
                    $subtotalItem = $precioUnitario * $item['cantidad'];

                    DetallePedido::create([
                        'precio_unitario' => $precioUnitario,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $subtotalItem,
                        'pedido_id' => $pedido->id,
                        'producto_id' => $item['id'],
                    ]);

                    // Descontar Stock
                    $producto = Producto::find($item['id']);
                    if ($producto) {
                        $nuevoStock = max(0, ($producto->stock ?? 10) - $item['cantidad']);
                        $producto->stock = $nuevoStock;
                        if ($nuevoStock == 0) {
                            $producto->disponibilidad = 0;
                        }
                        $producto->save();
                    }
                }

                // 5. Crear Registro de Envío
                $direccionCompleta = $validated['direccion'] . ', ' . $validated['ciudad'] . ' (' . $validated['departamento'] . ')';
                if (!empty($validated['notas'])) {
                    $direccionCompleta .= ' - Ref: ' . $validated['notas'];
                }

                Envio::create([
                    'empresa_envios' => 'Servientrega Express',
                    'estado' => 'En preparación',
                    'costo' => $costoEnvio,
                    'fecha_hora' => now(),
                    'direccion_envio' => $direccionCompleta,
                    'pedido_id' => $pedido->id,
                ]);

                // 6. Crear Registro de Pago
                $metodoObj = MetodoPago::find($validated['metodo_pago_id']);
                $facturaNumero = 'FAC-' . date('Ymd') . '-' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT);
                $estadoPago = ($metodoObj && $metodoObj->tipo === 'contraentrega') ? 'Pendiente al entregar' : 'Aprobado';

                Pago::create([
                    'monto' => $total,
                    'metodo_pago' => $metodoObj ? $metodoObj->nombre : 'Pago Electrónico',
                    'fecha_pago' => now(),
                    'factura' => $facturaNumero,
                    'estado_pago' => $estadoPago,
                    'pedido_id' => $pedido->id,
                ]);

                // 7. Vaciar Carrito de la Sesión
                session()->forget('cart');

                return $pedido;
            });

            return redirect()->route('checkout.confirmacion', $pedido->id)
                             ->with('compra_exitosa', '¡Tu compra ha sido procesada con éxito!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ocurrió un error al procesar tu pedido: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la pantalla de confirmación con el resumen de la compra.
     */
    public function confirmacion($id)
    {
        $pedido = Pedido::with([
            'cliente.persona',
            'detalles.producto.imagenes',
            'envio',
            'pago'
        ])->findOrFail($id);

        return view('cliente.checkout.Confirmacion', compact('pedido'));
    }

    /**
     * Genera la Factura en Formato Tirilla / Ticket POS para imprimir o guardar PDF.
     */
    public function facturaPos($id)
    {
        $pedido = Pedido::with([
            'cliente.persona',
            'detalles.producto',
            'envio',
            'pago'
        ])->findOrFail($id);

        return view('cliente.checkout.FacturaPos', compact('pedido'));
    }
}
