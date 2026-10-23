<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Clase7RolesPermisosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Asegurar que el seeder de roles y permisos esté ejecutado
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * 1. Valida que existan los 3 roles exigidos en la rúbrica y sus permisos.
     */
    public function test_roles_y_permisos_existen(): void
    {
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'vendedor')->exists());
        $this->assertTrue(Role::where('name', 'almacenista')->exists());

        $adminRole = Role::findByName('admin');
        $this->assertTrue($adminRole->hasPermissionTo('eliminar-productos'));

        $vendedorRole = Role::findByName('vendedor');
        $this->assertTrue($vendedorRole->hasPermissionTo('ver-productos'));
        $this->assertFalse($vendedorRole->hasPermissionTo('eliminar-productos'));
        $this->assertFalse($vendedorRole->hasPermissionTo('ver-categorias'));
    }

    /**
     * 2. Valida la asignación de roles a los usuarios de prueba.
     */
    public function test_usuarios_de_prueba_tienen_roles_asignados(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->can('eliminar-productos'));

        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->assertNotNull($vendedor);
        $this->assertTrue($vendedor->hasRole('vendedor'));
        $this->assertTrue($vendedor->can('ver-productos'));
        $this->assertFalse($vendedor->can('eliminar-productos'));

        $almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->assertNotNull($almacenista);
        $this->assertTrue($almacenista->hasRole('almacenista'));
        $this->assertTrue($almacenista->can('crear-productos'));
        $this->assertFalse($almacenista->can('eliminar-productos'));
    }

    /**
     * 3. Valida el funcionamiento de Soft Delete y restauración en Productos.
     */
    public function test_soft_delete_y_restauracion_en_productos(): void
    {
        $categoria = Categoria::firstOrCreate(
            ['nombre' => 'Categoría Test SoftDelete'],
            ['descripcion' => 'Prueba', 'estado' => true]
        );

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Hamburguesa Test Papelera',
            'descripcion' => 'Prueba de soft delete',
            'precio' => 25000,
            'stock' => 10,
            'estado' => true,
        ]);

        $initialCount = Producto::count();
        $productoId = $producto->id;

        // Eliminación lógica (Soft Delete)
        $producto->delete();

        // Count normal disminuye en 1
        $this->assertEquals($initialCount - 1, Producto::count());

        // withTrashed() aún lo encuentra
        $this->assertNotNull(Producto::withTrashed()->find($productoId));
        $this->assertNotNull(Producto::withTrashed()->find($productoId)->deleted_at);

        // Restauración
        Producto::onlyTrashed()->find($productoId)->restore();
        $this->assertEquals($initialCount, Producto::count());
        $this->assertNull(Producto::find($productoId)->deleted_at);
    }

    /**
     * 4. Valida que una categoría con productos asociados NO se pueda eliminar.
     */
    public function test_categoria_con_productos_no_se_puede_eliminar(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $categoria = Categoria::whereHas('productos')->first();
        $this->assertNotNull($categoria);

        $response = $this->actingAs($admin)->delete(route('categorias.destroy', $categoria));
        $response->assertSessionHas('error');
        $this->assertNull($categoria->fresh()->deleted_at);
    }

    /**
     * 5. Valida control de acceso: Vendedor NO puede eliminar productos (recibe 403 o no autorizado).
     */
    public function test_vendedor_no_puede_eliminar_productos_error_403(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $producto = Producto::first();

        $response = $this->actingAs($vendedor)->delete(route('productos.destroy', $producto));
        $response->assertStatus(403);
    }

    /**
     * 6. Valida control de acceso: Vendedor NO puede ver categorías (recibe 403).
     */
    public function test_vendedor_no_puede_ver_categorias_error_403(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();

        $response = $this->actingAs($vendedor)->get(route('categorias.index'));
        $response->assertStatus(403);
    }

    /**
     * 7. Valida que Admin tiene acceso total a productos y categorías.
     */
    public function test_admin_tiene_acceso_total(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('productos.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('categorias.index'));
        $response->assertStatus(200);
    }
}
