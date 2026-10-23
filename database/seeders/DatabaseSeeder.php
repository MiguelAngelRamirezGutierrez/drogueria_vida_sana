<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario administrador del ERP
        User::firstOrCreate(
            ['email' => 'admin@quickfood.local'],
            [
                'name' => 'Administrador QuickFood',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Llamar a los seeders en orden de dependencia
        $this->call([
            RolePermissionSeeder::class,
            CategoriaSeeder::class,
            ProductoSeeder::class,
            ClienteSeeder::class,
            ClientSeeder::class,
            DomiciliarioSeeder::class,
            MetodoPagoSeeder::class,
            PedidoSeeder::class,
        ]);

        $admin = User::where('email', 'admin@quickfood.local')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }
    }
}
