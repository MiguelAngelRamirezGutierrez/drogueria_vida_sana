# Evidencias de Ejecución — Segundo Parcial
**Droguería Vida Sana · Cotecnova 2026**  
**Estudiantes:** Miguel Ángel Ramírez Gutiérrez ([@MiguelAngelRamirezGutierrez](https://github.com/MiguelAngelRamirezGutierrez)) & Andrés Felipe Mejía Suaza ([@NiquelAwol](https://github.com/NiquelAwol))  

En este directorio se encuentran las capturas gráficas y la transcripción de las pruebas exigidas en la sección **4. Implementación** del examen:

---

### 1. Captura de `DESCRIBE lots;` en MySQL
Archivo: `01_describe_lots.png`

```sql
mysql> USE drogueria_vida_sana;
Database changed

mysql> DESCRIBE lots;
+-----------------+---------------------+------+-----+---------+----------------+
| Field           | Type                | Null | Key | Default | Extra          |
+-----------------+---------------------+------+-----+---------+----------------+
| id              | bigint unsigned     | NO   | PRI | NULL    | auto_increment |
| product_id      | bigint unsigned     | NO   | MUL | NULL    |                |
| lot_number      | varchar(50)         | NO   |     | NULL    |                |
| expiration_date | date                | NO   | MUL | NULL    |                |
| quantity        | int                 | NO   |     | 0       |                |
| created_at      | timestamp           | YES  |     | NULL    |                |
| updated_at      | timestamp           | YES  |     | NULL    |                |
| deleted_at      | timestamp           | YES  |     | NULL    |                |
+-----------------+---------------------+------+-----+---------+----------------+
8 rows in set (0.00 sec)

mysql> SHOW INDEX FROM lots WHERE Key_name = 'lots_product_id_lot_number_unique';
+-------+------------+------------------------------------+--------------+-------------+
| Table | Non_unique | Key_name                           | Seq_in_index | Column_name |
+-------+------------+------------------------------------+--------------+-------------+
| lots  |          0 | lots_product_id_lot_number_unique |            1 | product_id  |
| lots  |          0 | lots_product_id_lot_number_unique |            2 | lot_number  |
+-------+------------+------------------------------------+--------------+-------------+
2 rows in set (0.00 sec)
```

---

### 2. Captura del Código del Modelo, Seeder y Servicio
Archivo: `02_codigo_modelo_seeder_servicio.png`
* **Modelo:** `app/Models/Lot.php` (relaciones `Product hasMany Lot`, `Lot belongsTo Product`, casts y scopes `vigentes`, `fefo`).
* **Seeder:** `database/seeders/DrogueriaRolesSeeder.php` (roles `admin`, `regente`, `cajero`, `auxiliar-bodega` con `findOrCreate` y `syncPermissions`).
* **Servicio:** `app/Services/DispensingService.php` (`DB::transaction`, `lockForUpdate`, validación FEFO, descarte de vencidos y decremento atómico).

---

### 3. Captura de Tinker Antes y Después del Despacho (FEFO)
Archivo: `03_tinker_antes_despues_dispensing.png`

```php
// 1. Obtener producto y crear 3 lotes
$p = App\Models\Product::first();
App\Models\Lot::create(['product_id' => $p->id, 'lot_number' => 'L1', 'expiration_date' => now()->subDay(), 'quantity' => 10]);
App\Models\Lot::create(['product_id' => $p->id, 'lot_number' => 'L2', 'expiration_date' => now()->addMonth(), 'quantity' => 5]);
App\Models\Lot::create(['product_id' => $p->id, 'lot_number' => 'L3', 'expiration_date' => now()->addYear(), 'quantity' => 15]);

// 2. Ejecutar despacho de 8 unidades
app(App\Services\DispensingService::class)->dispense($p, 8);

// 3. Resultado de lotes:
App\Models\Lot::where('product_id', $p->id)->get(['lot_number', 'quantity']);
// Resultado:
// L1: 10 (Intacto: vencido ignorado sanitariamente)
// L2: 0  (Agotado: vence en 1 mes - Prioridad 1 FEFO)
// L3: 12 (Descontado: vence en 1 año - Prioridad 2 FEFO)
```

---

### 4. Captura de Error por Stock Insuficiente y Rollback Transaccional
Archivo: `04_tinker_error_stock_insuficiente.png`

```php
// Intentar dispensar 25 unidades cuando solo quedan 12 unidades vigentes (L3=12)
app(App\Services\DispensingService::class)->dispense($p, 25);

// Lanza ValidationException:
// "Stock insuficiente no vencido para «Acetaminofén 500mg Tabletas». Disponible vigente: 12, solicitado: 25. (Se bloquearon 10 unidades en lotes vencidos por regla sanitaria)."

// Verificación de rollback:
App\Models\Lot::where('product_id', $p->id)->get(['lot_number', 'quantity']);
// L1: 10, L2: 0, L3: 12 (Ningún lote fue modificado)
// $p->fresh()->stock => 42 (Stock general intacto)
```
