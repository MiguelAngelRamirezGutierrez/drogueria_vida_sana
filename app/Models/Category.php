<?php

namespace App\Models;

class Category extends Categoria
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'name',
        'description',
        'active',
    ];

    public function getNameAttribute()
    {
        return $this->attributes['nombre'] ?? $this->attributes['name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nombre'] = $value;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['descripcion'] ?? $this->attributes['description'] ?? null;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['descripcion'] = $value;
    }

    public function getActiveAttribute()
    {
        return (bool) ($this->attributes['estado'] ?? $this->attributes['active'] ?? true);
    }

    public function setActiveAttribute($value)
    {
        $this->attributes['estado'] = (bool) $value;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'categoria_id');
    }
}
