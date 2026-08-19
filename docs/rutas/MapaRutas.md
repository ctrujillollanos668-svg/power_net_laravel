# 🗺️ Mapa General de Rutas de PowerNet Laravel

Bienvenido a la documentación técnica del sistema de enrutamiento (`Routing`) de **PowerNet**. El proyecto organiza sus rutas en dos archivos principales: `routes/web.php` (tienda, cliente y panel de administración) y `routes/auth.php` (seguridad y autenticación).

---

## 📂 Módulos de Rutas Documentados

| Documento Markdown | Archivo Fuente | Descripción |
|---|---|---|
| 🛍️ **[RutasTienda.md](./RutasTienda.md)** | `routes/web.php` | Rutas públicas del catálogo, fichas de producto, carrito de compra en sesión y proceso de checkout transaccional. |
| 👤 **[RutasCliente.md](./RutasCliente.md)** | `routes/web.php` | Rutas protegidas por `auth` para clientes: panel de Mis Pedidos, radicación de garantías/devoluciones, perfil y favoritos. |
| 👑 **[RutasAdmin.md](./RutasAdmin.md)** | `routes/web.php` | Rutas protegidas para administración: Dashboard, gestión de productos, inventario/Kardex, proveedores, pedidos, ventas, envíos y finanzas. |
| 🔐 **[RutasAuth.md](./RutasAuth.md)** | `routes/auth.php` | Flujo de seguridad con middlewares `guest` y `auth`: Login, Registro, Recuperación de contraseña y Cierre de sesión. |

---

## 💡 Comandos Útiles de Rutas en Consola

```bash
# Ver el listado completo de rutas registradas en el sistema
php artisan route:list

# Filtrar rutas de un módulo específico (ej. admin o pedidos)
php artisan route:list --name=admin
php artisan route:list --path=carrito

# Limpiar caché de rutas tras modificar routes/web.php
php artisan route:clear
```
