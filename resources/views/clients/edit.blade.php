@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('page_title', 'Modificar Información de Cliente')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('clients.partials.form')

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('clients.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
                    Actualizar Cliente
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
