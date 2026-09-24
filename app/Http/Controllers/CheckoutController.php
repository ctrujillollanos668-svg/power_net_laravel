<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\Inventario;
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
        $persona = null;
        $cliente = null;
        $direccionGuardada = null;
        $direccionCalle = null;
        $telefonoGuardado = null;
        $documentoGuardado = null;
        $ciudadGuardada = 'Bogotá D.C.';
        $departamentoGuardado = 'Cundinamarca';

        $direccionesGuardadas = collect();

        if ($user) {
            if ($user->persona_id) {
                $persona = Persona::find($user->persona_id);
            }
            if (!$persona) {
                $persona = Persona::where('nombre_persona', $user->name)->latest('id')->first();
                if ($persona) {
                    $user->persona_id = $persona->id;
                    $user->save();
                }
            }

            if ($persona) {
                $telefonoGuardado = $persona->telefono;
                $documentoGuardado = $persona->documento;
                $clientes = Cliente::where('persona_id', $persona->id)->latest('id')->get();
                
                $direccionesGuardadas = $clientes->map(function ($c) {
                    $partes = array_map('trim', explode(',', $c->direccion));
                    $calle = $c->direccion;
                    $ciudad = 'Bogotá D.C.';
                    $depto = 'Cundinamarca';
                    if (count($partes) >= 3) {
                        $depto = array_pop($partes);
                        $ciudad = array_pop($partes);
                        $calle = implode(', ', $partes);
                    } elseif (count($partes) == 2) {
                        $ciudad = array_pop($partes);
                        $calle = implode(', ', $partes);
                    }
                    return [
                        'id' => $c->id,
                        'alias' => $c->alias ?: 'Dirección',
                        'direccion_completa' => $c->direccion,
                        'direccion' => $calle,
                        'ciudad' => $ciudad,
                        'departamento' => $depto,
                    ];
                });

                $cliente = $clientes->first();
                if ($cliente && !empty($cliente->direccion)) {
                    $direccionGuardada = $cliente->direccion;
                    $primerDir = $direccionesGuardadas->first();
                    $direccionCalle = $primerDir['direccion'];
                    $ciudadGuardada = $primerDir['ciudad'];
                    $departamentoGuardado = $primerDir['departamento'];
                }
            }
        }

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
            'persona',
            'cliente',
            'metodosPago',
            'direccionesGuardadas',
            'direccionGuardada',
            'direccionCalle',
            'telefonoGuardado',
            'documentoGuardado',
            'ciudadGuardada',
            'departamentoGuardado'
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

                $fullDireccion = $validated['direccion'] . ', ' . $validated['ciudad'] . ', ' . $validated['departamento'];
                $cliente = null;
                if (!empty($request->cliente_id)) {
                    $cliente = Cliente::where('persona_id', $persona->id)->find($request->cliente_id);
                }
                if (!$cliente) {
                    $cliente = Cliente::where('persona_id', $persona->id)->where('direccion', $fullDireccion)->first();
                }
                if (!$cliente) {
                    $num = Cliente::where('persona_id', $persona->id)->count() + 1;
                    $cliente = Cliente::create([
                        'direccion' => $fullDireccion,
                        'alias' => $request->alias ?: ('Dirección ' . $num),
                        'persona_id' => $persona->id,
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

                    // Descontar Stock y registrar en Kardex de inventario
                    $producto = Producto::find($item['id']);
                    if ($producto) {
                        $stockAnterior = (int)($producto->stock ?? 0);
                        $nuevoStock = max(0, $stockAnterior - (int)$item['cantidad']);
                        $producto->stock = $nuevoStock;
                        if ($nuevoStock == 0) {
                            $producto->disponibilidad = 0;
                        }
                        $producto->save();

                        Inventario::create([
                            'producto_id' => $producto->id,
                            'tipo' => 'salida',
                            'cantidad' => (int)$item['cantidad'],
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $nuevoStock,
                            'motivo' => "Venta en Pedido #{$pedido->id}",
                            'pedido_id' => $pedido->id,
                        ]);
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
     * Guarda o actualiza una dirección del cliente vía AJAX desde la pantalla de Checkout.
     */
    public function guardarDireccion(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'alias' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'documento' => 'nullable|string|max:50',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para gestionar tus direcciones.'
            ], 401);
        }

        try {
            $persona = null;
            if ($user->persona_id) {
                $persona = Persona::find($user->persona_id);
            }
            if (!$persona) {
                $persona = Persona::where('nombre_persona', $user->name)->latest('id')->first();
            }

            if (!$persona) {
                $persona = Persona::create([
                    'nombre_persona' => $validated['nombre'],
                    'telefono' => $validated['telefono'],
                    'documento' => $validated['documento'] ?? null,
                ]);
                $user->persona_id = $persona->id;
                $user->save();
            } else {
                $persona->update([
                    'nombre_persona' => $validated['nombre'],
                    'telefono' => $validated['telefono'],
                    'documento' => $validated['documento'] ?? $persona->documento,
                ]);
            }

            $fullDireccion = trim($validated['direccion']) . ', ' . trim($validated['ciudad']) . ', ' . trim($validated['departamento']);
            $cliente = null;
            
            if (!empty($validated['id'])) {
                $cliente = Cliente::where('persona_id', $persona->id)->find($validated['id']);
            }

            $alias = !empty($validated['alias']) ? trim($validated['alias']) : 'Dirección ' . (Cliente::where('persona_id', $persona->id)->count() + 1);

            if ($cliente) {
                $cliente->update([
                    'direccion' => $fullDireccion,
                    'alias' => !empty($validated['alias']) ? trim($validated['alias']) : ($cliente->alias ?: $alias),
                ]);
            } else {
                $cliente = Cliente::create([
                    'direccion' => $fullDireccion,
                    'alias' => $alias,
                    'persona_id' => $persona->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '¡Dirección guardada exitosamente!',
                'data' => [
                    'id' => $cliente->id,
                    'alias' => $cliente->alias ?: 'Dirección',
                    'direccion_completa' => $cliente->direccion,
                    'direccion' => $validated['direccion'],
                    'ciudad' => $validated['ciudad'],
                    'departamento' => $validated['departamento'],
                    'nombre' => $persona->nombre_persona,
                    'telefono' => $persona->telefono,
                    'documento' => $persona->documento,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo guardar la dirección: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una dirección guardada del cliente.
     */
    public function eliminarDireccion($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
        }

        try {
            $personaId = $user->persona_id;
            if (!$personaId) {
                $persona = Persona::where('nombre_persona', $user->name)->latest('id')->first();
                $personaId = $persona ? $persona->id : null;
            }

            if (!$personaId) {
                return response()->json(['success' => false, 'message' => 'Perfil no encontrado.'], 404);
            }

            $cliente = Cliente::where('persona_id', $personaId)->find($id);
            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'Dirección no encontrada.'], 404);
            }

            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => '¡Dirección eliminada correctamente!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la dirección: ' . $e->getMessage()
            ], 500);
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
