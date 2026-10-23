<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'QuickFood ERP') }} - @yield('title', 'Gestión Operativa')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="h-full text-slate-800 flex flex-col md:flex-row antialiased">

    <!-- Sidebar Navegación Lateral -->
    <aside class="w-full md:w-64 bg-slate-900 text-slate-200 flex flex-col justify-between shrink-0 no-print min-h-screen">
        <div>
            <!-- Logo / Brand -->
            <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                        <i class="fa-solid fa-burger text-lg"></i>
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-white block leading-tight">QuickFood</span>
                        <span class="text-xs uppercase tracking-widest text-orange-400 font-semibold">ERP Dark Kitchen</span>
                    </div>
                </a>
            </div>

            <!-- Navegación con Protección por Permisos de Spatie (Paso 10 de la Guía) -->
            <nav class="p-4 space-y-1.5 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                @can('ver-pedidos')
                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Operaciones</div>

                    <a href="{{ route('pedidos.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('pedidos.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-receipt w-5 text-center"></i>
                            <span>Pedidos</span>
                        </div>
                        @php
                            $pendientesCount = \App\Models\Pedido::pendientes()->count();
                        @endphp
                        @if($pendientesCount > 0)
                            <span class="bg-amber-500 text-slate-950 text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ $pendientesCount }}
                            </span>
                        @endif
                    </a>

                    @can('crear-pedidos')
                        <a href="{{ route('pedidos.create') }}" class="flex items-center space-x-3 px-3.5 py-2 rounded-lg transition text-slate-400 hover:bg-slate-800 hover:text-orange-400 text-xs ml-4">
                            <i class="fa-solid fa-plus-circle w-4"></i>
                            <span>Crear Nuevo Pedido</span>
                        </a>
                    @endcan
                @endcan

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Catálogo & Clientes</div>

                @can('ver-productos')
                    <a href="{{ route('productos.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('productos.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-utensils w-5 text-center"></i>
                        <span>Productos</span>
                    </a>
                @endcan

                @can('ver-categorias')
                    <a href="{{ route('categorias.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('categorias.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center"></i>
                        <span>Categorías</span>
                    </a>
                @endcan

                @can('ver-clientes')
                    <a href="{{ route('clients.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('clients.*') || request()->routeIs('clientes.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Clientes</span>
                    </a>
                @endcan

                @can('ver-ventas')
                    <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('sales.*') || request()->routeIs('ventas.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-cash-register w-5 text-center"></i>
                        <span>Ventas & Facturas</span>
                    </a>
                @endcan

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Logística & Finanzas</div>

                @can('ver-domiciliarios')
                    <a href="{{ route('domiciliarios.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('domiciliarios.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-motorcycle w-5 text-center"></i>
                        <span>Domiciliarios</span>
                    </a>
                @endcan

                <a href="{{ route('metodos-pago.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('metodos-pago.*') ? 'bg-orange-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-credit-card w-5 text-center"></i>
                    <span>Métodos de Pago</span>
                </a>
            </nav>
        </div>

        <!-- Footer del Sidebar con Usuario Autenticado -->
        <div class="p-4 border-t border-slate-800 text-xs text-slate-400 space-y-2">
            @auth
                <div class="bg-slate-800/80 rounded-lg p-2.5 border border-slate-700/60 mb-2">
                    <div class="flex items-center justify-between">
                        <span class="text-white font-bold truncate max-w-[140px]">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] bg-orange-600 text-white px-1.5 py-0.5 rounded font-bold uppercase">
                            {{ auth()->user()->roles->first()?->name ?? 'Sin rol' }}
                        </span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full text-center text-xs bg-slate-700 hover:bg-rose-600 text-slate-200 hover:text-white py-1 rounded transition flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="block text-center text-xs bg-orange-600 hover:bg-orange-700 text-white py-1.5 rounded-lg font-semibold transition mb-2">
                    <i class="fa-solid fa-right-to-bracket mr-1"></i> Iniciar Sesión / Roles
                </a>
            @endauth

            <div class="flex items-center justify-between text-slate-300">
                <span class="font-medium">COTECNOVA SGE</span>
                <span class="text-[10px] bg-slate-800 text-orange-400 px-2 py-0.5 rounded">RAD 2026</span>
            </div>
            <p class="text-[11px] text-slate-400">QuickFood ERP &bull; Dark Kitchen</p>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar -->
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between no-print shadow-sm">
            <div class="flex items-center space-x-3">
                <h1 class="text-xl font-bold text-slate-800">@yield('page_title', 'Dashboard')</h1>
                <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-medium">
                    <i class="fa-regular fa-clock mr-1 text-slate-400"></i> {{ now()->isoFormat('D [de] MMMM, YYYY') }}
                </span>
            </div>

            <div class="flex items-center space-x-3">
                @can('crear-pedidos')
                    <a href="{{ route('pedidos.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm transition">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tomar Pedido</span>
                    </a>
                @endcan
            </div>
        </header>

        <!-- Mensajes Flash de Sesión -->
        <div class="px-6 pt-4 no-print">
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-start space-x-3 text-emerald-800 mb-4">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Operación Exitosa</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-sm flex items-start space-x-3 text-rose-800 mb-4">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Atención</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm text-amber-900 mb-4">
                    <div class="flex items-center space-x-2 mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                        <span class="font-bold text-sm">Por favor verifique los siguientes campos:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 ml-4 text-amber-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Contenedor del Cuerpo de la Vista -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
