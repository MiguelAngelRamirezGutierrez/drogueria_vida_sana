<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar la caché de permisos de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Productos
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            // Categorías
            'ver-categorias', 'crear-categorias', 'editar-categorias', 'eliminar-categorias',
            // Clientes (Paso 1 Guía Clase 9)
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            // Ventas (Paso 1 Guía Clase 9)
            'ver-ventas', 'crear-ventas', 'editar-ventas', 'eliminar-ventas',
            // Compras
            'ver-compras', 'crear-compras',
            // Pedidos (ERP QuickFood / Dark Kitchen)
            'ver-pedidos', 'crear-pedidos', 'editar-pedidos', 'eliminar-pedidos', 'cambiar-estado-pedidos',
            // Domiciliarios
            'ver-domiciliarios', 'crear-domiciliarios', 'editar-domiciliarios', 'eliminar-domiciliarios',
            // Papelera y Reportes
            'ver-papelera', 'ver-reportes',
        ];

        // findOrCreate: si el permiso ya existe no lo duplica
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Rol Administrador: acceso total
        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // Rol Vendedor (según Paso 1 de la Guía Clase 9)
        Role::findOrCreate('vendedor')->syncPermissions([
            'ver-productos',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-ventas', 'crear-ventas',
            'ver-pedidos', 'crear-pedidos', 'cambiar-estado-pedidos',
        ]);

        // Rol Almacenista (según Paso 1 de la Guía Clase 9)
        Role::findOrCreate('almacenista')->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-categorias',
            'ver-compras', 'crear-compras',
            'ver-papelera',
        ]);

        // Usuarios de prueba con roles asignados (contraseña: password)
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Administrador QuickFood', 'password' => 'password']
        );
        $admin->syncRoles(['admin']);

        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@test.com'],
            ['name' => 'Vendedor QuickFood', 'password' => 'password']
        );
        $vendedor->syncRoles(['vendedor']);

        $almacenista = User::firstOrCreate(
            ['email' => 'almacenista@test.com'],
            ['name' => 'Almacenista QuickFood', 'password' => 'password']
        );
        $almacenista->syncRoles(['almacenista']);
    }
}
