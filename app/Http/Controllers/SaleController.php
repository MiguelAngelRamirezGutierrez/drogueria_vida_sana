<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-ventas', only: ['index', 'show']),
            new Middleware('permission:crear-ventas', only: ['create', 'store']),
            new Middleware('permission:eliminar-ventas', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $sales = Sale::query()
            ->with(['client', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', $search)
                      ->orWhereHas('client', fn ($q) => $q->where('name', 'like', $search));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('sale_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('sale_date', '<=', $request->input('to')))
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'totalMonth' => Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('total'),
        ]);
    }

    public function create()
    {
        return view('sales.create', [
            'clients' => Client::where('active', true)->orderBy('name')->get(['id', 'name', 'document_number']),
            'products' => Product::where('active', true)->where('stock', '>', 0)->orderBy('name')->get(['id', 'name', 'price', 'stock']),
        ]);
    }

    public function store(StoreSaleRequest $request, SaleService $service)
    {
        try {
            $sale = $service->register($request->validated(), $request->user()->id);

            return redirect()->route('sales.show', $sale)
                ->with('success', "Venta «{$sale->invoice_number}» registrada correctamente.");
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['client', 'user', 'details.product']);

        return view('sales.show', ['sale' => $sale]);
    }

    public function destroy(Sale $sale)
    {
        // Regla de negocio: al anular, se devuelve el stock a los productos
        DB::transaction(function () use ($sale) {
            foreach ($sale->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stock', $detail->quantity);
                }
            }

            $sale->update(['status' => 'anulada']);
            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', "Venta «{$sale->invoice_number}» anulada y stock devuelto.");
    }
}
