<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    /**
     * Muestra la vista principal del carrito de compras.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
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
        }
        
        $subtotal = 0;
        $descuentoTotal = 0;
        $totalItems = 0;

        foreach ($cart as $id => $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
            
            if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
                $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
            }
            
            $totalItems += $item['cantidad'];
        }

        $costoEnvio = ($subtotal - $descuentoTotal > 150000 || empty($cart)) ? 0 : 12000;
        $total = ($subtotal - $descuentoTotal) + $costoEnvio;

        return view('cliente.carrito.Carrito', compact(
            'cart',
            'subtotal',
            'descuentoTotal',
            'costoEnvio',
            'total',
            'totalItems'
        ));
    }

    /**
     * Agrega un producto al carrito con una cantidad determinada respetando el stock disponible.
     */
    public function agregar(Request $request, $id)
    {
        $producto = Producto::with(['imagenes', 'categoria', 'ofertaActiva'])->findOrFail($id);
        $cantidad = max(1, (int) $request->input('cantidad', 1));

        $stockDisponible = (int) ($producto->stock ?? 0);
        if ($stockDisponible <= 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lo sentimos, este producto está agotado.',
                ], 422);
            }
            return back()->with('error', 'El producto está agotado.');
        }

        $cart = session()->get('cart', []);

        $foto = $producto->imagenes->first() ? $producto->imagenes->first()->imagen : null;
        $tieneOferta = $producto->ofertaActiva;
        $precioOferta = $tieneOferta ? (float) $producto->ofertaActiva->precio_oferta : null;
        $descuentoPorcentaje = $tieneOferta ? (int) $producto->ofertaActiva->descuento : null;

        $cantidadActualEnCarrito = isset($cart[$id]) ? (int) $cart[$id]['cantidad'] : 0;
        $nuevaCantidad = $cantidadActualEnCarrito + $cantidad;
        $mensaje = '¡' . $producto->nombre . ' añadido al carrito!';

        if ($nuevaCantidad > $stockDisponible) {
            $nuevaCantidad = $stockDisponible;
            if ($cantidadActualEnCarrito >= $stockDisponible) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Ya tienes en el carrito el stock máximo disponible ({$stockDisponible} unidades).",
                    ], 422);
                }
                return back()->with('error', "Ya tienes en el carrito el stock máximo disponible ({$stockDisponible} unidades).");
            }
            $mensaje = "Se añadieron unidades hasta el límite disponible ({$stockDisponible} unidades).";
        }

        $cart[$id] = [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'categoria' => $producto->categoria->nombre_categoria ?? 'Material Eléctrico',
            'precio' => (float) $producto->precio,
            'precio_oferta' => $precioOferta,
            'descuento' => $descuentoPorcentaje,
            'imagen' => $foto,
            'cantidad' => $nuevaCantidad,
            'stock' => $stockDisponible,
        ];

        session()->put('cart', $cart);

        $totalCount = array_sum(array_column($cart, 'cantidad'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'cart_count' => $totalCount,
                'cart' => $cart,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', $mensaje);
    }

    /**
     * Actualiza la cantidad de un producto en el carrito validando el stock en tiempo real.
     */
    public function actualizar(Request $request, $id)
    {
        $cantidad = (int) $request->input('cantidad', 1);
        $cart = session()->get('cart', []);
        $warning = null;
        $itemCantidad = 0;
        $stockMaximo = 0;

        if (isset($cart[$id])) {
            $producto = Producto::find($cart[$id]['id'] ?? $id);

            if (!$producto || (int) $producto->stock <= 0) {
                unset($cart[$id]);
                session()->put('cart', $cart);

                $subtotal = 0;
                $descuentoTotal = 0;
                foreach ($cart as $item) {
                    $subtotal += $item['precio'] * $item['cantidad'];
                    if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
                        $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
                    }
                }
                $costoEnvio = ($subtotal - $descuentoTotal > 150000 || empty($cart)) ? 0 : 12000;
                $total = ($subtotal - $descuentoTotal) + $costoEnvio;

                return response()->json([
                    'success' => true,
                    'eliminado' => true,
                    'message' => 'El producto se ha agotado y fue retirado del carrito.',
                    'cart_count' => array_sum(array_column($cart, 'cantidad')),
                    'subtotal' => $subtotal,
                    'descuentoTotal' => $descuentoTotal,
                    'costoEnvio' => $costoEnvio,
                    'total' => $total,
                ]);
            }

            $stockReal = (int) $producto->stock;
            $stockMaximo = $stockReal;
            $cart[$id]['stock'] = $stockReal;

            if ($cantidad <= 0) {
                unset($cart[$id]);
            } else {
                if ($cantidad > $stockReal) {
                    $itemCantidad = $stockReal;
                    $warning = "Solo hay {$stockReal} unidades disponibles de {$producto->nombre}.";
                } else {
                    $itemCantidad = $cantidad;
                }
                $cart[$id]['cantidad'] = $itemCantidad;
            }

            session()->put('cart', $cart);
        }

        if ($request->wantsJson() || $request->ajax()) {
            $subtotal = 0;
            $descuentoTotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
                if (isset($item['precio_oferta']) && $item['precio_oferta'] < $item['precio']) {
                    $descuentoTotal += ($item['precio'] - $item['precio_oferta']) * $item['cantidad'];
                }
            }
            $costoEnvio = ($subtotal - $descuentoTotal > 150000 || empty($cart)) ? 0 : 12000;
            $total = ($subtotal - $descuentoTotal) + $costoEnvio;
            $totalItems = array_sum(array_column($cart, 'cantidad'));

            return response()->json([
                'success' => true,
                'warning' => $warning,
                'item_cantidad' => $itemCantidad,
                'stock_maximo' => $stockMaximo,
                'cart_count' => $totalItems,
                'subtotal' => $subtotal,
                'descuentoTotal' => $descuentoTotal,
                'costoEnvio' => $costoEnvio,
                'total' => $total,
                'item_total' => isset($cart[$id]) ? (($cart[$id]['precio_oferta'] ?? $cart[$id]['precio']) * $cart[$id]['cantidad']) : 0,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Carrito actualizado');
    }

    /**
     * Elimina un producto del carrito.
     */
    public function eliminar(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $nombre = $cart[$id]['nombre'];
            unset($cart[$id]);
            session()->put('cart', $cart);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado del carrito.',
                    'cart_count' => array_sum(array_column($cart, 'cantidad')),
                ]);
            }

            return redirect()->route('carrito.index')->with('success', 'Se eliminó ' . $nombre . ' del carrito.');
        }

        return redirect()->route('carrito.index');
    }

    /**
     * Vacía completamente el carrito de compras.
     */
    public function vaciar(Request $request)
    {
        session()->forget('cart');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado.',
                'cart_count' => 0,
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'El carrito ha sido vaciado.');
    }

    /**
     * Retorna el resumen del carrito en JSON para el contador del navbar.
     */
    public function resumen()
    {
        $cart = session()->get('cart', []);
        $totalItems = array_sum(array_column($cart, 'cantidad'));

        return response()->json([
            'cart_count' => $totalItems,
            'items_count' => count($cart),
        ]);
    }
}
