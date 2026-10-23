<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DrogueriaRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la caché de permisos de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Lista exhaustiva de permisos según la matriz de seguridad del Segundo Parcial
        $permissions = [
            // Productos / Medicamentos
            'ver-productos',
            'crear-productos',
            'editar-productos',
            'eliminar-productos',

            // Control Sanitario y Farmacovigilancia
            'autorizar-control-especial',
            'ver-recetas-retenidas',

            // Lotes e Inventario FEFO
            'ver-lotes',
            'recibir-lotes',          // Recepción técnica sanitaria exclusiva del Regente
            'descargar-mercancia',    // Descarga física en bodega
            'ubicar-lotes',           // Ubicación en estanterías
            'ajustar-lotes-merma',

            // Compras y Abastecimiento
            'ver-compras',
            'crear-compras',          // Generación orden de compra
            'aprobar-compras',        // Aprobación presupuestal

            // Ventas y Dispensación
            'ver-ventas',
            'crear-ventas',           // Dispensación en mostrador
            'anular-ventas',
            'dispensar-venta-libre',

            // Clientes / Pacientes
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',
            'eliminar-clientes',

            // Reportes e Indicadores INVIMA
            'ver-reportes',
            'exportar-informe-invima',
        ];

        // Crear permisos con findOrCreate para evitar duplicados
        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 1. Rol: Administrador (Dueño - Control Gerencial y Aprobación Comercial)
        $roleAdmin = Role::findOrCreate('admin', 'web');
        $roleAdmin->syncPermissions(Permission::all());

        // 2. Rol: Regente de Farmacia (Responsable Técnico Sanitario)
        $roleRegente = Role::findOrCreate('regente', 'web');
        $roleRegente->syncPermissions([
            'ver-productos',
            'crear-productos',
            'editar-productos',
            'autorizar-control-especial',
            'ver-recetas-retenidas',
            'ver-lotes',
            'recibir-lotes',
            'ajustar-lotes-merma',
            'ver-compras',
            'ver-ventas',
            'anular-ventas',
            'ver-clientes',
            'ver-reportes',
            'exportar-informe-invima',
        ]);

        // 3. Rol: Cajero (Atención y Cobro en Mostrador)
        $roleCajero = Role::findOrCreate('cajero', 'web');
        $roleCajero->syncPermissions([
            'ver-productos',
            'ver-lotes',
            'ver-ventas',
            'crear-ventas',
            'dispensar-venta-libre',
            'ver-clientes',
            'crear-clientes',
        ]);

        // 4. Rol: Auxiliar de Bodega (Recepción Física y Almacenamiento)
        $roleBodega = Role::findOrCreate('auxiliar-bodega', 'web');
        $roleBodega->syncPermissions([
            'ver-productos',
            'ver-lotes',
            'descargar-mercancia',
            'ubicar-lotes',
            'ver-compras',
        ]);

        // Creación de Usuarios de Prueba (Contraseña: password)
        $users = [
            [
                'email' => 'admin@vidasana.com',
                'name' => 'Carlos Administrador (Dueño)',
                'role' => 'admin',
            ],
            [
                'email' => 'regente@vidasana.com',
                'name' => 'Dra. Laura Gómez (Regente de Farmacia)',
                'role' => 'regente',
            ],
            [
                'email' => 'cajero@vidasana.com',
                'name' => 'Andrés Restrepo (Cajero Turno)',
                'role' => 'cajero',
            ],
            [
                'email' => 'bodega@vidasana.com',
                'name' => 'Mateo Valencia (Auxiliar de Bodega)',
                'role' => 'auxiliar-bodega',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}
