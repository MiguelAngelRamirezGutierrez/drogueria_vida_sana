<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
{
    /**
     * Los permisos se validan en el controlador (middleware de Spatie).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Si el checkbox "estado" o "active" no se marca, el navegador no lo envía: lo dejamos en false.
     */
    protected function prepareForValidation(): void
    {
        $estado = $this->has('estado') ? $this->boolean('estado') : ($this->has('active') ? $this->boolean('active') : false);
        $this->merge([
            'estado' => $estado,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            // La categoría debe existir y NO estar en la papelera
            'categoria_id' => ['required', Rule::exists('categorias', 'id')->whereNull('deleted_at')],
            'estado' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'precio' => 'precio',
            'stock' => 'stock',
            'categoria_id' => 'categoría',
            'estado' => 'estado',
        ];
    }
}
