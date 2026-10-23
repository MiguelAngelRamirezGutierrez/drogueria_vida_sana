<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends FormRequest
{
    /**
     * Los permisos se validan en el controlador (middleware de Spatie).
     */
    public function authorize(): bool
    {
        return true;
    }

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
            'nombre' => [
                'required', 'string', 'max:100',
                // Nombre único entre las categorías que no están en la papelera
                Rule::unique('categorias', 'nombre')->withoutTrashed()->ignore($this->route('categoria')),
            ],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'estado' => 'estado',
        ];
    }
}
