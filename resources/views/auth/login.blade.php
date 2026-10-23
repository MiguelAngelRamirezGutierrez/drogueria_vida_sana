<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión & Roles | QuickFood ERP</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-6">
        <!-- Logo y Cabecera -->
        <div class="text-center">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 items-center justify-center text-white shadow-xl shadow-orange-500/30 mb-3">
                <i class="fa-solid fa-burger text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">QuickFood ERP</h2>
            <p class="text-xs text-orange-400 font-semibold uppercase tracking-widest mt-0.5">Control de Acceso & Roles Spatie</p>
            <p class="text-xs text-slate-400 mt-1">COTECNOVA &bull; Clase 7: Roles, Permisos y CRUD Completo</p>
        </div>

        <!-- Alertas -->
        @if (session('success'))
            <div class="p-3 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 text-xs flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Tarjeta de Login -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-2xl p-6 shadow-2xl backdrop-blur-sm space-y-5">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="admin@test.com"
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-lg pl-9 pr-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        <i class="fa-solid fa-envelope absolute left-3 top-3 text-slate-500 text-xs"></i>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            placeholder="password"
                            class="w-full bg-slate-900/80 border border-slate-700 rounded-lg pl-9 pr-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        <i class="fa-solid fa-lock absolute left-3 top-3 text-slate-500 text-xs"></i>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-orange-600 focus:ring-orange-500">
                        <span>Recordarme</span>
                    </label>
                    <span class="text-slate-500">Clave por defecto: <code class="text-orange-400">password</code></span>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white font-bold py-2.5 px-4 rounded-lg shadow-lg shadow-orange-500/25 transition flex items-center justify-center space-x-2 text-sm">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Iniciar Sesión</span>
                </button>
            </form>

            <!-- Sección de Acceso Rápido por Roles para Sustentación / Pruebas -->
            <div class="pt-4 border-t border-slate-700/60">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                        <i class="fa-solid fa-bolt text-amber-400 mr-1"></i> Acceso Rápido por Rol
                    </span>
                    <span class="text-[10px] bg-slate-700 text-slate-300 px-2 py-0.5 rounded">Para pruebas</span>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    @foreach ($usuariosPrueba as $userPrueba)
                        @php
                            $rolName = $userPrueba->roles->first()?->name ?? 'Sin rol';
                            $rolColor = match($rolName) {
                                'admin' => 'border-orange-500/40 bg-orange-500/10 hover:bg-orange-500/20 text-orange-300',
                                'almacenista' => 'border-purple-500/40 bg-purple-500/10 hover:bg-purple-500/20 text-purple-300',
                                'vendedor' => 'border-blue-500/40 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300',
                                default => 'border-slate-600 bg-slate-700/30 hover:bg-slate-700/60 text-slate-300',
                            };
                            $badgeColor = match($rolName) {
                                'admin' => 'bg-orange-500 text-slate-950',
                                'almacenista' => 'bg-purple-500 text-white',
                                'vendedor' => 'bg-blue-500 text-white',
                                default => 'bg-slate-600 text-white',
                            };
                        @endphp
                        <a href="{{ route('quick-login', $userPrueba) }}" 
                           class="flex items-center justify-between p-2.5 rounded-xl border {{ $rolColor }} transition group">
                            <div class="flex items-center space-x-2.5 truncate">
                                <div class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center shrink-0 text-xs">
                                    @if ($rolName === 'admin') 👑
                                    @elseif ($rolName === 'almacenista') 📦
                                    @elseif ($rolName === 'vendedor') 🛒
                                    @else 👤
                                    @endif
                                </div>
                                <div class="truncate text-left">
                                    <p class="text-xs font-bold text-white group-hover:text-orange-400 transition truncate">{{ $userPrueba->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $userPrueba->email }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md {{ $badgeColor }} shrink-0">
                                {{ $rolName }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-300 transition">
                ← Continuar como invitado al Dashboard
            </a>
        </p>
    </div>

</body>
</html>
