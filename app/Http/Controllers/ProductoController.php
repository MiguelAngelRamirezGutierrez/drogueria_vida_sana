<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductoController extends Controller implements HasMiddleware
{
    /**
     * Permiso de Spatie que exige cada acción.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-productos', only: ['index', 'show']),
            new Middleware('permission:crear-productos', only: ['create', 'store']),
            new Middleware('permission:editar-productos', only: ['edit', 'update']),
            new Middleware('permission:eliminar-productos', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        // La papelera solo la ve quien tiene permiso para eliminar
        $showTrashed = $request->boolean('trashed') && ($request->user() ? $request->user()->can('eliminar-productos') : true);

        $query = Producto::query()
            ->with(['categoria' => fn ($q) => $q->withTrashed()]);

        if ($showTrashed) {
            $query->onlyTrashed();
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->string('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado') === 'activo');
        }

        $productos = $query->latest($showTrashed ? 'deleted_at' : 'created_at')
            ->paginate(12)
            ->withQueryString();

        $categorias = Categoria::withTrashed()->orderBy('nombre')->get();
        $trashedCount = Producto::onlyTrashed()->count();

        return view('productos.index', compact('productos', 'categorias', 'showTrashed', 'trashedCount'));
    }

    public function create()
    {
        $categorias = Categoria::activos()->orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request)
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::activos()->orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Soft delete: el producto va a la papelera (se llena deleted_at).
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', "Producto «{$producto->nombre}» enviado a la papelera.");
    }

    /**
     * Restaurar un producto desde la papelera.
     */
    public function restore(Producto $producto)
    {
        if ($producto->categoria && $producto->categoria->trashed()) {
            return back()->with('error', "Restaura primero la categoría «{$producto->categoria->nombre}».");
        }

        $producto->restore();

        return redirect()->route('productos.index', ['trashed' => 1])
            ->with('success', "Producto «{$producto->nombre}» restaurado exitosamente.");
    }
}
