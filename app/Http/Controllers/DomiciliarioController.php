<?php

namespace App\Http\Controllers;

use App\Models\Domiciliario;
use Illuminate\Http\Request;

class DomiciliarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Domiciliario::withCount('pedidos')->latest();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%")
                  ->orWhere('documento', 'like', "%{$buscar}%")
                  ->orWhere('vehiculo', 'like', "%{$buscar}%");
            });
        }

        $domiciliarios = $query->paginate(10)->withQueryString();

        return view('domiciliarios.index', compact('domiciliarios'));
    }

    public function create()
    {
        return view('domiciliarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'telefono' => 'required|string|max:30',
            'documento' => 'required|string|max:50',
            'vehiculo' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);

        Domiciliario::create($request->all());

        return redirect()->route('domiciliarios.index')
            ->with('success', 'Domiciliario registrado exitosamente.');
    }

    public function edit(Domiciliario $domiciliario)
    {
        return view('domiciliarios.edit', compact('domiciliario'));
    }

    public function update(Request $request, Domiciliario $domiciliario)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'telefono' => 'required|string|max:30',
            'documento' => 'required|string|max:50',
            'vehiculo' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);

        $domiciliario->update($request->all());

        return redirect()->route('domiciliarios.index')
            ->with('success', 'Datos del domiciliario actualizados.');
    }

    public function destroy(Domiciliario $domiciliario)
    {
        if ($domiciliario->pedidos()->exists()) {
            return redirect()->route('domiciliarios.index')
                ->with('error', 'No se puede eliminar el domiciliario porque tiene entregas registradas en el sistema.');
        }

        $domiciliario->delete();

        return redirect()->route('domiciliarios.index')
            ->with('success', 'Domiciliario eliminado.');
    }
}
