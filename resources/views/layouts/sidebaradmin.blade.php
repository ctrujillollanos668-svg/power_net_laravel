<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel de Control') - PowerNet Admin</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Anti-flicker Dark Mode Script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Slim Scrollbar for Sidebar & Page */
        .sidebar-scroll::-webkit-scrollbar,
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track,
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* ==================== MODO OSCURO GLOBAL PARA TODOS LOS MÓDULOS ADMIN ==================== */
        .dark main {
            background-color: #0b0f19 !important;
            color: #cbd5e1 !important;
        }
        
        /* Tarjetas, Contenedores y Modales */
        .dark main .bg-white,
        .dark .fixed .bg-white,
        .dark .absolute .bg-white:not([data-theme-ignore]) {
            background-color: #131b2e !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
            color: #cbd5e1 !important;
        }
        
        /* Fondos secundarios, subpaneles y pies de tarjeta */
        .dark main .bg-gray-50,
        .dark main .bg-slate-50,
        .dark main .bg-zinc-50,
        .dark main [class*="bg-slate-50"],
        .dark main [class*="bg-gray-50"],
        .dark .fixed .bg-gray-50,
        .dark .fixed .bg-slate-50 {
            background-color: #0d1527 !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
        }
        
        .dark main .bg-gray-100,
        .dark main .bg-slate-100,
        .dark main .bg-zinc-100,
        .dark main [class*="bg-slate-100"],
        .dark main [class*="bg-gray-100"] {
            background-color: #1e293b !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
        }
        
        /* Hover de filas y botones */
        .dark main .hover\:bg-gray-50:hover,
        .dark main .hover\:bg-slate-50:hover,
        .dark main .hover\:bg-gray-100:hover,
        .dark main .hover\:bg-slate-100:hover {
            background-color: #1a233a !important;
        }
        
        /* Celdas de Tablas, Precios y Montos (Tonos suaves y descansados) */
        .dark main table td,
        .dark main table th,
        .dark main td {
            color: #cbd5e1 !important;
        }
        
        .dark main table td .font-bold,
        .dark main table td .font-black,
        .dark main table td .font-extrabold,
        .dark main table td strong,
        .dark main table td b {
            color: #e2e8f0 !important;
        }

        /* Fechas, Facturas, Teléfonos y textos secundarios en tablas */
        .dark main table td [class*="text-gray-400"],
        .dark main table td [class*="text-slate-400"],
        .dark main table td [class*="text-gray-500"],
        .dark main table td [class*="text-slate-500"],
        .dark main table td .text-xs:not(.font-bold):not(.font-black),
        .dark main table td .text-\[11px\],
        .dark main table td .text-\[10px\] {
            color: #94a3b8 !important;
        }
        
        /* Tipografías, Títulos, Números de KPI y Encabezados (Plata / Slate suave, NO blanco chillón) */
        .dark main .text-gray-900,
        .dark main .text-slate-900,
        .dark main .text-zinc-900,
        .dark main .text-gray-800,
        .dark main .text-slate-800,
        .dark main .text-zinc-800,
        .dark main .text-[#0f172a],
        .dark main [class*="text-slate-900"],
        .dark main [class*="text-gray-900"],
        .dark main [class*="text-slate-800"],
        .dark main [class*="text-gray-800"],
        .dark main .text-black,
        .dark main h1,
        .dark main h2,
        .dark main h3,
        .dark main h4 {
            color: #f1f5f9 !important;
        }
        
        .dark main .text-gray-700,
        .dark main .text-slate-700,
        .dark main .text-zinc-700,
        .dark main .text-gray-600,
        .dark main .text-slate-600,
        .dark main .text-zinc-600,
        .dark main [class*="text-slate-700"],
        .dark main [class*="text-gray-700"],
        .dark main [class*="text-slate-600"],
        .dark main [class*="text-gray-600"] {
            color: #cbd5e1 !important;
        }
        
        .dark main .text-gray-500,
        .dark main .text-slate-500,
        .dark main .text-gray-400,
        .dark main .text-slate-400,
        .dark main [class*="text-slate-500"],
        .dark main [class*="text-gray-500"],
        .dark main [class*="text-slate-400"],
        .dark main [class*="text-gray-400"] {
            color: #94a3b8 !important;
        }

        /* Colores de énfasis armónicos para Métricas, Iconos y Dinero */
        .dark main .text-emerald-600,
        .dark main .text-emerald-700,
        .dark main .text-emerald-800,
        .dark main [class*="text-[#10b981]"],
        .dark main [class*="text-[#059669]"],
        .dark main [class*="text-[#15803d]"] {
            color: #34d399 !important;
        }
        
        .dark main .text-blue-600,
        .dark main .text-blue-700,
        .dark main .text-blue-800,
        .dark main [class*="text-[#3b82f6]"] {
            color: #60a5fa !important;
        }
        
        .dark main .text-violet-600,
        .dark main .text-violet-700,
        .dark main .text-violet-800,
        .dark main [class*="text-[#7c3aed]"] {
            color: #a78bfa !important;
        }
        
        .dark main .text-amber-600,
        .dark main .text-amber-700,
        .dark main .text-amber-800,
        .dark main [class*="text-[#f59e0b]"],
        .dark main [class*="text-[#d97706]"] {
            color: #fbbf24 !important;
        }
        
        .dark main .text-red-600,
        .dark main .text-red-700,
        .dark main .text-red-800,
        .dark main [class*="text-[#ef4444]"],
        .dark main [class*="text-[#b91c1c]"] {
            color: #f87171 !important;
        }

        /* Badges e Insignias de Estado con diseño Dark Glass sofisticado */
        .dark main .bg-emerald-50,
        .dark main .bg-emerald-100,
        .dark main [class*="bg-[#ecfdf5]"],
        .dark main [class*="bg-[#dcfce7]"],
        .dark main [class*="bg-[#0f6848]"] {
            background-color: rgba(6, 78, 59, 0.4) !important;
            border-color: rgba(16, 185, 129, 0.35) !important;
            color: #6ee7b7 !important;
        }
        .dark main .bg-amber-50,
        .dark main .bg-amber-100,
        .dark main [class*="bg-[#fffbeb]"],
        .dark main [class*="bg-[#fef3c7]"],
        .dark main [class*="bg-[#d97706]"] {
            background-color: rgba(120, 53, 15, 0.4) !important;
            border-color: rgba(245, 158, 11, 0.35) !important;
            color: #fcd34d !important;
        }
        .dark main .bg-blue-50,
        .dark main .bg-blue-100,
        .dark main [class*="bg-[#eef4ff]"],
        .dark main [class*="bg-[#dbeafe]"] {
            background-color: rgba(30, 58, 138, 0.4) !important;
            border-color: rgba(59, 130, 246, 0.35) !important;
            color: #93c5fd !important;
        }
        .dark main .bg-violet-50,
        .dark main .bg-violet-100,
        .dark main [class*="bg-[#f5f3ff]"],
        .dark main [class*="bg-[#ede9fe]"] {
            background-color: rgba(76, 29, 149, 0.4) !important;
            border-color: rgba(139, 92, 246, 0.35) !important;
            color: #c4b5fd !important;
        }
        .dark main .bg-red-50,
        .dark main .bg-red-100,
        .dark main [class*="bg-[#fef2f2]"],
        .dark main [class*="bg-[#fee2e2]"],
        .dark main [class*="bg-[#dc2626]"] {
            background-color: rgba(127, 29, 29, 0.4) !important;
            border-color: rgba(239, 68, 68, 0.35) !important;
            color: #fca5a5 !important;
        }

        /* Botones Principales con fondo oscuro */
        .dark main a[class*="bg-[#0f172a]"],
        .dark main button[class*="bg-[#0f172a]"],
        .dark .fixed a[class*="bg-[#0f172a]"],
        .dark .fixed button[class*="bg-[#0f172a]"] {
            background-color: #6366f1 !important;
            color: #ffffff !important;
            border-color: #4f46e5 !important;
        }
        .dark main a[class*="bg-[#0f172a]"]:hover,
        .dark main button[class*="bg-[#0f172a]"]:hover,
        .dark .fixed a[class*="bg-[#0f172a]"]:hover,
        .dark .fixed button[class*="bg-[#0f172a]"]:hover {
            background-color: #4f46e5 !important;
        }

        /* Botones de acción rápida (+ / - / editar) */
        .dark main button.bg-emerald-50,
        .dark main button[class*="bg-emerald-50"] {
            background-color: rgba(6, 78, 59, 0.5) !important;
            color: #34d399 !important;
        }
        .dark main button.bg-amber-50,
        .dark main button[class*="bg-amber-50"] {
            background-color: rgba(120, 53, 15, 0.5) !important;
            color: #fbbf24 !important;
        }
        .dark main button.bg-slate-100,
        .dark main button[class*="bg-slate-100"] {
            background-color: #1e293b !important;
            color: #cbd5e1 !important;
        }
        
        /* Inputs, Selects y Textareas */
        .dark main input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="color"]):not([type="file"]),
        .dark main select,
        .dark main textarea,
        .dark .fixed input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="color"]):not([type="file"]),
        .dark .fixed select,
        .dark .fixed textarea {
            background-color: #0d1527 !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        .dark main input::placeholder,
        .dark main textarea::placeholder,
        .dark .fixed input::placeholder,
        .dark .fixed textarea::placeholder {
            color: #64748b !important;
        }
        
        /* Tablas */
        .dark main table thead,
        .dark main table thead tr,
        .dark main table thead th {
            background-color: #0d1527 !important;
            color: #64748b !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
        }
        
        .dark main table tbody tr {
            border-color: rgba(51, 65, 85, 0.4) !important;
        }
        
        .dark main table tbody tr:hover {
            background-color: rgba(30, 41, 59, 0.4) !important;
        }
        
        /* Bordes y Divisores */
        .dark main .border-gray-100,
        .dark main .border-gray-200,
        .dark main .border-gray-300,
        .dark main .border-slate-100,
        .dark main .border-slate-200,
        .dark main .border-slate-300,
        .dark .fixed .border-gray-100,
        .dark .fixed .border-gray-200 {
            border-color: rgba(51, 65, 85, 0.6) !important;
        }
        
        .dark main .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
        .dark main .divide-gray-200 > :not([hidden]) ~ :not([hidden]),
        .dark main .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
        .dark main .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: rgba(51, 65, 85, 0.5) !important;
        }
        
        /* ==================== ESTILOS EXHAUSTIVOS DE MODALES Y FORMULARIOS ==================== */
        .dark .fixed[class*="z-50"],
        .dark .fixed[class*="z-40"],
        .dark .fixed.inset-0,
        .dark [role="dialog"] {
            color: #cbd5e1 !important;
        }

        /* Fondo del diálogo modal */
        .dark .fixed .bg-white,
        .dark .fixed [class*="bg-white"],
        .dark [role="dialog"] .bg-white {
            background-color: #131b2e !important;
            border-color: rgba(51, 65, 85, 0.7) !important;
            color: #cbd5e1 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important;
        }

        /* Encabezados, títulos y textos de modal */
        .dark .fixed h1,
        .dark .fixed h2,
        .dark .fixed h3,
        .dark .fixed h4,
        .dark .fixed h5,
        .dark .fixed h1 span,
        .dark .fixed h2 span,
        .dark .fixed h3 span,
        .dark .fixed h4 span,
        .dark .fixed .font-black,
        .dark .fixed .font-extrabold,
        .dark .fixed .font-bold,
        .dark .fixed .text-gray-900,
        .dark .fixed .text-slate-900 {
            color: #f1f5f9 !important;
        }

        /* Botón cerrar modal (X) */
        .dark .fixed button[class*="text-gray-400"],
        .dark .fixed button[class*="text-slate-400"],
        .dark .fixed button[class*="text-gray-500"],
        .dark .fixed button[class*="text-slate-500"] {
            color: #94a3b8 !important;
        }
        .dark .fixed button[class*="text-gray-400"]:hover,
        .dark .fixed button[class*="text-slate-400"]:hover,
        .dark .fixed button[class*="text-gray-500"]:hover,
        .dark .fixed button[class*="text-slate-500"]:hover {
            color: #ffffff !important;
        }

        /* Etiquetas (labels) de formularios dentro y fuera de modales */
        .dark label,
        .dark .fixed label,
        .dark main label,
        .dark label[class*="text-gray-700"],
        .dark label[class*="text-slate-700"],
        .dark label[class*="text-gray-800"],
        .dark label[class*="text-slate-800"] {
            color: #e2e8f0 !important;
        }

        /* Inputs, Selects y Textareas dentro de modales */
        .dark .fixed input,
        .dark .fixed select,
        .dark .fixed textarea {
            background-color: #0d1527 !important;
            border-color: #334155 !important;
            color: #ffffff !important;
        }

        .dark .fixed select option,
        .dark main select option {
            background-color: #0d1527 !important;
            color: #ffffff !important;
        }

        /* Botones Cancelar / Secundarios de modales */
        .dark .fixed button[class*="border-gray-300"],
        .dark .fixed button[class*="border-slate-300"],
        .dark .fixed button[class*="text-gray-700"],
        .dark .fixed button[class*="text-slate-700"],
        .dark .fixed button[class*="hover:bg-gray-50"],
        .dark .fixed button[class*="hover:bg-slate-50"],
        .dark .fixed a[class*="border-gray-300"],
        .dark .fixed a[class*="border-slate-300"],
        .dark .fixed a[class*="text-gray-700"],
        .dark .fixed a[class*="text-slate-700"],
        .dark .fixed a[class*="hover:bg-gray-50"],
        .dark .fixed a[class*="hover:bg-slate-50"] {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        .dark .fixed button[class*="hover:bg-gray-50"]:hover,
        .dark .fixed button[class*="hover:bg-slate-50"]:hover,
        .dark .fixed a[class*="hover:bg-gray-50"]:hover,
        .dark .fixed a[class*="hover:bg-slate-50"]:hover {
            background-color: #334155 !important;
            color: #ffffff !important;
        }

        /* Bordes divisorios dentro del modal */
        .dark .fixed .border-b,
        .dark .fixed .border-t,
        .dark .fixed .border-gray-100,
        .dark .fixed .border-gray-200,
        .dark .fixed .border-slate-100,
        .dark .fixed .border-slate-200 {
            border-color: rgba(51, 65, 85, 0.6) !important;
        }

        /* Modales Overlays */
        .dark .bg-black\/50,
        .dark .bg-black\/40,
        .dark .bg-slate-900\/50,
        .dark .bg-gray-900\/50 {
            background-color: rgba(0, 0, 0, 0.8) !important;
        }
    </style>
</head>
<body class="bg-[#f8fafc] dark:bg-[#0b0f19] text-slate-800 dark:text-slate-100 antialiased font-sans transition-colors duration-200">
<div class="flex min-h-screen">

    {{-- ==================== SIDEBAR MODERNO Y ELEGANTE (100% FIJO) ==================== --}}
    <aside class="fixed inset-y-0 left-0 w-64 h-full bg-[#080d1a] border-r border-white/5 text-slate-300 flex flex-col z-30 shadow-2xl overflow-hidden">
        
        {{-- Brand & Logo Header --}}
        <div class="px-5 py-5 border-b border-white/5 flex items-center justify-between shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-slate-900 border border-white/10 p-1 flex items-center justify-center overflow-hidden shadow-lg shadow-amber-500/20 group-hover:scale-105 transition duration-300 shrink-0">
                    <img src="{{ asset('img/logo_powernet.jpg') }}" alt="PowerNet" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <div class="text-base font-black tracking-tight leading-none text-white flex items-center gap-1">
                        <span>Power</span><span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-yellow-300">Net</span>
                    </div>
                    <span class="text-[9px] font-extrabold text-amber-400/90 uppercase tracking-widest block mt-1">Iluminación & Bombillos</span>
                </div>
            </a>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-500/10 text-amber-300 border border-amber-500/20">
                PRO
            </span>
        </div>

        {{-- Navegación con Scroll Slim --}}
        <nav class="flex-1 sidebar-scroll overflow-y-auto px-3.5 py-5 space-y-6">
            
            {{-- SECCIÓN: PRINCIPAL --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span>Principal</span>
                </p>
                <ul class="space-y-1">
                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-amber-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-gauge-high text-xs"></i>
                            </div>
                            <span class="tracking-wide">Dashboard</span>
                        </a>
                    </li>

                    {{-- Pedidos --}}
                    <li>
                        <a href="{{ route('admin.pedidos.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.pedidos.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.pedidos.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-blue-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-clipboard-list text-xs"></i>
                            </div>
                            <span class="tracking-wide">Pedidos</span>
                        </a>
                    </li>

                    {{-- Envíos --}}
                    <li>
                        <a href="{{ route('admin.envios.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.envios.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.envios.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-cyan-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-truck-fast text-xs"></i>
                            </div>
                            <span class="tracking-wide">Envíos y Despachos</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- SECCIÓN: CATÁLOGO --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                    <span>Catálogo & Stock</span>
                </p>
                <ul class="space-y-1">
                    {{-- Productos --}}
                    <li>
                        <a href="{{ route('productos.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('productos.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('productos.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-violet-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-box text-xs"></i>
                            </div>
                            <span class="tracking-wide">Productos</span>
                        </a>
                    </li>

                    {{-- Categorías --}}
                    <li>
                        <a href="{{ route('categorias.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('categorias.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('categorias.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-fuchsia-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-tags text-xs"></i>
                            </div>
                            <span class="tracking-wide">Categorías</span>
                        </a>
                    </li>

                    {{-- Inventario --}}
                    <li>
                        <a href="{{ route('admin.inventario.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.inventario.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.inventario.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-sky-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-warehouse text-xs"></i>
                            </div>
                            <span class="tracking-wide">Inventario</span>
                        </a>
                    </li>

                    {{-- Ofertas --}}
                    <li>
                        <a href="{{ route('ofertas.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('ofertas.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('ofertas.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-yellow-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-percent text-xs"></i>
                            </div>
                            <span class="tracking-wide">Ofertas</span>
                        </a>
                    </li>

                    {{-- Proveedores --}}
                    <li>
                        <a href="{{ route('proveedores.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('proveedores.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('proveedores.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-indigo-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-industry text-xs"></i>
                            </div>
                            <span class="tracking-wide">Proveedores</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- SECCIÓN: FINANZAS --}}
            <div>
                <p class="px-3 mb-2 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Finanzas & Recaudo</span>
                </p>
                <ul class="space-y-1">
                    {{-- Métodos de Pago --}}
                    <li>
                        <a href="{{ route('metodospago.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('metodospago.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('metodospago.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-emerald-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-credit-card text-xs"></i>
                            </div>
                            <span class="tracking-wide">Métodos de Pago</span>
                        </a>
                    </li>

                    {{-- Pagos --}}
                    <li>
                        <a href="{{ route('admin.pagos.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.pagos.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.pagos.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-teal-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                            </div>
                            <span class="tracking-wide">Pagos</span>
                        </a>
                    </li>

                    {{-- Ventas --}}
                    <li>
                        <a href="{{ route('admin.ventas.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.ventas.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.ventas.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-amber-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-sack-dollar text-xs"></i>
                            </div>
                            <span class="tracking-wide">Ventas & Analítica</span>
                        </a>
                    </li>

                    {{-- Devoluciones --}}
                    <li>
                        <a href="{{ route('admin.devoluciones.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition duration-200 group {{ request()->routeIs('admin.devoluciones.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ request()->routeIs('admin.devoluciones.*') ? 'bg-white/20 text-white' : 'bg-slate-800/80 text-rose-400 group-hover:bg-slate-800' }}">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </div>
                            <span class="tracking-wide">Devoluciones</span>
                        </a>
                    </li>
                </ul>
            </div>

        </nav>

    </aside>

    {{-- ==================== CONTENIDO PRINCIPAL ==================== --}}
    <div class="flex-1 flex flex-col min-h-screen min-w-0 ml-64">

        {{-- Topbar Elegante --}}
        <header class="h-16 bg-white/90 dark:bg-[#0f172a]/90 border-b border-slate-200/80 dark:border-slate-800/80 px-6 sm:px-8 flex items-center justify-between shrink-0 sticky top-0 z-20 shadow-2xs backdrop-blur-md transition-colors duration-200">
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-400 dark:text-slate-500">PowerNet</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
                <h2 class="text-sm font-black text-slate-900 dark:text-white">@yield('title', 'Panel de Control')</h2>
            </div>

            <div class="flex items-center gap-3" x-data="{
                darkMode: document.documentElement.classList.contains('dark'),
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                }
            }">
                
                {{-- Botón Rápido Modo Oscuro / Claro --}}
                <button type="button" 
                        @click="toggleTheme()"
                        title="Cambiar entre modo claro y oscuro"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 dark:text-amber-400 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 transition cursor-pointer shadow-xs">
                    <i class="fa-solid fa-sun text-sm transition-transform duration-300" x-show="darkMode" x-cloak></i>
                    <i class="fa-solid fa-moon text-sm transition-transform duration-300 text-slate-600" x-show="!darkMode"></i>
                </button>
                
                {{-- Menú de Usuario en Topbar --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition cursor-pointer">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div class="hidden sm:block text-left leading-none">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{{ Auth::user()->name ?? 'Administrador' }}</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold block mt-0.5">En línea</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5 transition-transform" :class="open && 'rotate-180'"></i>
                    </button>

                    {{-- Dropdown Topbar --}}
                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-52 bg-white dark:bg-[#1e293b] border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl py-2 z-50 transition-colors">
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-black text-slate-900 dark:text-white">{{ Auth::user()->name ?? 'Administrador' }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition">
                            <i class="fa-solid fa-user-gear text-slate-400 dark:text-slate-400 w-4"></i>
                            <span>Mi Perfil</span>
                        </a>

                        {{-- Toggle dentro del dropdown --}}
                        <button type="button" @click="toggleTheme()" 
                                class="w-full flex items-center justify-between px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition text-left cursor-pointer">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid w-4 text-center" :class="darkMode ? 'fa-sun text-amber-400' : 'fa-moon text-indigo-400'"></i>
                                <span x-text="darkMode ? 'Modo Claro' : 'Modo Oscuro'"></span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400"
                                  x-text="darkMode ? 'Activo' : 'Inactivo'"></span>
                        </button>

                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition text-left">
                                <i class="fa-solid fa-right-from-bracket text-xs w-4"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Contenido de cada vista --}}
        <main class="flex-1 bg-[#f8fafc] dark:bg-[#0b0f19] p-6 sm:p-8 transition-colors duration-200">
            @yield('content')
        </main>
    </div>

</div>

@include('partials.alertas')
</body>
</html>