<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Droguería Vida Sana') }} - @yield('title', 'Gestión Farmacéutica')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
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
            <!-- Logo / Brand Farmacia -->
            <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-notes-medical text-lg"></i>
                    </div>
                    <div>
                        <span class="text-base font-black tracking-tight text-white block leading-tight">Vida Sana</span>
                        <span class="text-[10px] uppercase tracking-widest text-emerald-400 font-bold">ERP Farmacéutico</span>
                    </div>
                </a>
            </div>

            <!-- Navegación con Protección por Permisos de Spatie -->
            <nav class="p-4 space-y-1.5 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-emerald-400"></i>
                    <span>Dashboard & KPIs</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Trazabilidad & FEFO</div>

                <a href="{{ route('lots.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('lots.*') || request()->routeIs('lotes.*') ? 'bg-emerald-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center text-teal-400"></i>
                        <span>Lotes & Caducidades</span>
                    </div>
                    <span class="bg-emerald-900 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-700">
                        FEFO
                    </span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Gestión Farmacéutica</div>

                @can('ver-productos')
                    <a href="{{ route('productos.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('productos.*') || request()->routeIs('products.*') ? 'bg-emerald-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-pills w-5 text-center text-emerald-400"></i>
                        <span>Medicamentos</span>
                    </a>
                @endcan

                @can('ver-categorias')
                    <a href="{{ route('categorias.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('categorias.*') || request()->routeIs('categories.*') ? 'bg-emerald-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center text-slate-400"></i>
                        <span>Categorías</span>
                    </a>
                @endcan

                @can('ver-clientes')
                    <a href="{{ route('clients.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('clients.*') || request()->routeIs('clientes.*') ? 'bg-emerald-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center text-slate-400"></i>
                        <span>Pacientes / Clientes</span>
                    </a>
                @endcan

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Dispensación & Caja</div>

                @can('ver-ventas')
                    <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('sales.*') || request()->routeIs('ventas.*') ? 'bg-emerald-600 text-white font-semibold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-cash-register w-5 text-center text-amber-400"></i>
                        <span>Dispensación / Ventas</span>
                    </a>
                    @can('crear-ventas')
                        <a href="{{ route('sales.create') }}" class="flex items-center space-x-3 px-3.5 py-2 rounded-lg transition text-slate-400 hover:bg-slate-800 hover:text-emerald-400 text-xs ml-4">
                            <i class="fa-solid fa-plus-circle w-4"></i>
                            <span>Nueva Dispensación</span>
                        </a>
                    @endcan
                @endcan
            </nav>
        </div>

        <!-- Footer Sidebar con Usuario & Roles -->
        <div class="p-4 border-t border-slate-800 text-xs">
            @auth
                <div class="bg-slate-800/80 rounded-lg p-3 mb-3 border border-slate-700/60">
                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold mb-0.5">Usuario Activo</div>
                    <div class="flex items-center justify-between">
                        <span class="text-white font-bold truncate max-w-[140px]">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] bg-emerald-600 text-white px-1.5 py-0.5 rounded font-bold uppercase">
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
                <a href="{{ route('login') }}" class="block text-center text-xs bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg font-semibold transition mb-3">
                    <i class="fa-solid fa-right-to-bracket mr-1"></i> Iniciar Sesión / Conmutar Rol
                </a>
            @endauth

            <div class="flex items-center justify-between text-slate-300">
                <span class="font-medium">COTECNOVA SGE</span>
                <span class="text-[10px] bg-slate-800 text-emerald-400 px-2 py-0.5 rounded font-mono">PARCIAL 2</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Droguería Vida Sana &bull; Cartago</p>
            <div class="mt-2 pt-2 border-t border-slate-800 text-[10px] text-slate-400 leading-tight">
                <span class="font-bold text-slate-300 block mb-0.5">Estudiantes:</span>
                <div>&bull; Miguel Ángel Ramírez G.</div>
                <div>&bull; Andrés Felipe Mejía S.</div>
            </div>
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
                <a href="{{ route('lots.index') }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold px-3 py-2 rounded-lg transition">
                    <i class="fa-solid fa-boxes-stacked text-emerald-600"></i>
                    <span>Consola FEFO</span>
                </a>

                @can('crear-ventas')
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow-sm transition">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Dispensar Medicamento</span>
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
                        <p class="font-semibold">Atención / Error de Validación</p>
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
