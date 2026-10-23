<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoriaController extends Controller implements HasMiddleware
{
    /**
     * Permiso de Spatie que exige cada acción.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-categorias', only: ['index', 'show']),
            new Middleware('permission:crear-categorias', only: ['create', 'store']),
            new Middleware('permission:editar-categorias', only: ['edit', 'update']),
            new Middleware('permission:eliminar-categorias', only: ['destroy', 'restore']),
        ];
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed') && ($request->user() ? $request->user()->can('eliminar-categorias') : true);

        $query = Categoria::query()
            ->withCount('productos');

        if ($showTrashed) {
            $query->onlyTrashed();
        }

        if ($request->filled('buscar')) {
            $buscar = $request->string('buscar');
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado') === 'activo');
        }

        $categorias = $query->latest($showTrashed ? 'deleted_at' : 'created_at')
            ->paginate(10)
            ->withQueryString();

        $trashedCount = Categoria::onlyTrashed()->count();

        return view('categorias.index', compact('categorias', 'showTrashed', 'trashedCount'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada con éxito.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada con éxito.');
    }

    /**
     * Soft delete: no se permite eliminar una categoría con productos asignados.
     */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error', "No se puede eliminar «{$categoria->nombre}»: tiene productos asociados.");
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', "Categoría «{$categoria->nombre}» enviada a la papelera.");
    }

    /**
     * Restaurar una categoría desde la papelera.
     */
    public function restore(Categoria $categoria)
    {
        $categoria->restore();

        return redirect()->route('categorias.index', ['trashed' => 1])
            ->with('success', "Categoría «{$categoria->nombre}» restaurada exitosamente.");
    }
}
