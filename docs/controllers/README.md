# 📑 Índice General de Controladores - PowerNet Laravel

Bienvenido a la documentación técnica modular de controladores para el proyecto **PowerNet**. A continuación encontrarás el enlace directo al archivo `.md` de cada controlador con su explicación detallada, rutas, métodos y modelos utilizados.

---

## 👑 Módulo de Administración (`Admin`)

| Controlador | Archivo Markdown | Propósito Principal |
|---|---|---|
| **dashboardController** | [dashboardController.md](./dashboardController.md) | Panel de control con métricas generales y administración de roles. |
| **ProductoController** | [ProductoController.md](./ProductoController.md) | CRUD de productos, galería de imágenes y control de precios/stock. |
| **CategoriaController** | [CategoriaController.md](./CategoriaController.md) | Gestión y estados de categorías de productos. |
| **ProveedorController** | [ProveedorController.md](./ProveedorController.md) | Directorio y contacto de proveedores. |
| **OfertaController** | [OfertaController.md](./OfertaController.md) | Creación y programación de promociones y descuentos. |
| **AdminInventarioController** | [AdminInventarioController.md](./AdminInventarioController.md) | Kardex, ajustes de stock y control de existencias. |
| **AdminPedidoController** | [AdminPedidoController.md](./AdminPedidoController.md) | Monitoreo y actualización de estados de pedidos. |
| **AdminVentaController** | [AdminVentaController.md](./AdminVentaController.md) | Reportes financieros y ranking de productos más vendidos. |
| **AdminPagoController** | [AdminPagoController.md](./AdminPagoController.md) | Conciliación y verificación de pagos y comprobantes. |
| **AdminEnvioController** | [AdminEnvioController.md](./AdminEnvioController.md) | Asignación de transportadoras y tracking de envíos. |
| **AdminDevolucionController** | [AdminDevolucionController.md](./AdminDevolucionController.md) | Gestión y resolución de devoluciones y garantías. |
| **MetodoPagoController** | [MetodoPagoController.md](./MetodoPagoController.md) | Parametrización de canales y cuentas bancarias de recaudo. |

---

## 🛍️ Módulo de Tienda y Clientes (`E-commerce`)

| Controlador | Archivo Markdown | Propósito Principal |
|---|---|---|
| **TiendaController** | [TiendaController.md](./TiendaController.md) | Inicio (`welcome`), catálogo con filtros y ficha de producto. |
| **CarritoController** | [CarritoController.md](./CarritoController.md) | Manejo de sesión del carrito, cálculo de totales y stock. |
| **CheckoutController** | [CheckoutController.md](./CheckoutController.md) | Procesamiento transaccional de compra y creación de órdenes. |
| **PedidoController** | [PedidoController.md](./PedidoController.md) | Historial de compras y comprobantes del cliente. |
| **ClienteDevolucionController** | [ClienteDevolucionController.md](./ClienteDevolucionController.md) | Radicación de solicitudes de garantía por el comprador. |
| **FavoritoController** | [FavoritoController.md](./FavoritoController.md) | Lista de deseos (*Wishlist*) con soporte AJAX. |
| **ClienteMetodoPagoController** | [ClienteMetodoPagoController.md](./ClienteMetodoPagoController.md) | Información de cuentas oficiales para el cliente. |
| **ProfileController** | [ProfileController.md](./ProfileController.md) | Actualización de perfil de usuario y contraseña. |

---

## 🔐 Módulo de Autenticación (`Auth`)

| Controlador | Archivo Markdown | Propósito Principal |
|---|---|---|
| **Auth Suite** | [AuthControllers.md](./AuthControllers.md) | Login, registro de usuarios, reseteo de contraseñas y verificación de email. |

---

> 💡 *Para consultar un resumen unificado en un solo archivo, revisa también [CONTROLADORES.md](../CONTROLADORES.md).*
