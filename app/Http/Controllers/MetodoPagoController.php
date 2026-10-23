<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index(Request $request)
    {
        $query = MetodoPago::withCount('pedidos')->latest();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        $metodos = $query->paginate(10)->withQueryString();

        return view('metodos_pago.index', compact('metodos'));
    }

    public function create()
    {
        return view('metodos_pago.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);

        MetodoPago::create($request->all());

        return redirect()->route('metodos-pago.index')
            ->with('success', 'Método de pago registrado exitosamente.');
    }

    public function edit(MetodoPago $metodos_pago)
    {
        return view('metodos_pago.edit', ['metodo' => $metodos_pago]);
    }

    public function update(Request $request, MetodoPago $metodos_pago)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);

        $metodos_pago->update($request->all());

        return redirect()->route('metodos-pago.index')
            ->with('success', 'Método de pago actualizado.');
    }

    public function destroy(MetodoPago $metodos_pago)
    {
        if ($metodos_pago->pedidos()->exists()) {
            return redirect()->route('metodos-pago.index')
                ->with('error', 'No se puede eliminar el método de pago porque tiene transacciones registradas.');
        }

        $metodos_pago->delete();

        return redirect()->route('metodos-pago.index')
            ->with('success', 'Método de pago eliminado.');
    }
}
