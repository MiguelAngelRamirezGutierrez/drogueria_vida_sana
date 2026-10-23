<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar columnas virtuales para compatibilidad transparente en productos y categorias
        if (Schema::hasTable('productos')) {
            if (!Schema::hasColumn('productos', 'name')) {
                DB::statement('ALTER TABLE productos ADD COLUMN name VARCHAR(150) GENERATED ALWAYS AS (nombre) VIRTUAL');
            }
            if (!Schema::hasColumn('productos', 'price')) {
                DB::statement('ALTER TABLE productos ADD COLUMN price DECIMAL(12,2) GENERATED ALWAYS AS (precio) VIRTUAL');
            }
            if (!Schema::hasColumn('productos', 'active')) {
                DB::statement('ALTER TABLE productos ADD COLUMN active TINYINT(1) GENERATED ALWAYS AS (estado) VIRTUAL');
            }
        }

        if (Schema::hasTable('categorias')) {
            if (!Schema::hasColumn('categorias', 'name')) {
                DB::statement('ALTER TABLE categorias ADD COLUMN name VARCHAR(100) GENERATED ALWAYS AS (nombre) VIRTUAL');
            }
            if (!Schema::hasColumn('categorias', 'active')) {
                DB::statement('ALTER TABLE categorias ADD COLUMN active TINYINT(1) GENERATED ALWAYS AS (estado) VIRTUAL');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('productos')) {
            if (Schema::hasColumn('productos', 'name')) {
                DB::statement('ALTER TABLE productos DROP COLUMN name');
            }
            if (Schema::hasColumn('productos', 'price')) {
                DB::statement('ALTER TABLE productos DROP COLUMN price');
            }
            if (Schema::hasColumn('productos', 'active')) {
                DB::statement('ALTER TABLE productos DROP COLUMN active');
            }
        }

        if (Schema::hasTable('categorias')) {
            if (Schema::hasColumn('categorias', 'name')) {
                DB::statement('ALTER TABLE categorias DROP COLUMN name');
            }
            if (Schema::hasColumn('categorias', 'active')) {
                DB::statement('ALTER TABLE categorias DROP COLUMN active');
            }
        }
    }
};
