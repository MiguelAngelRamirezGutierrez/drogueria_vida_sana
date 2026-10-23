# Droguería Vida Sana &bull; Sistema de Gestión Farmacéutica y ERP
## Corporación de Estudios Tecnológicos del Norte del Valle — COTECNOVA
### Seminario de Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD (2026)
### Evaluación Práctica: Segundo Parcial — Software de Gestión Empresarial (SGE)

---

**Empresa:** Droguería Vida Sana  
**Ubicación:** Cartago, Valle del Cauca  
**Giro del Negocio:** Venta minorista de medicamentos genéricos, éticos/comerciales, de control especial, productos OTC, dermocosmética y dispositivos médicos con servicio de dispensación regulada e inyectología bajo normativa del INVIMA y Ministerio de Salud y Protección Social (Resolución 1403 de 2007).  
**Repositorio GitHub:** [https://github.com/MiguelAngelRamirezGutierrez/drogueria_vida_sana.git](https://github.com/MiguelAngelRamirezGutierrez/drogueria_vida_sana.git)  
**Ramas de entrega:** `main` y `parcial2`  
**Stack Tecnológico:** Laravel 11/12, PHP 8.3/8.4, MySQL 8.4, Docker Desktop (Laravel Sail), Eloquent ORM, Spatie Laravel Permission, Pest/PHPUnit.  
**Estudiantes:**
* **Miguel Ángel Ramírez Gutiérrez** &bull; [GitHub: @MiguelAngelRamirezGutierrez](https://github.com/MiguelAngelRamirezGutierrez)
* **Andrés Felipe Mejía Suaza** &bull; [GitHub: @NiquelAwol](https://github.com/NiquelAwol)

---

## 📋 Índice General de Documentos y Entregables del Parcial

Todos los requisitos teóricos, de modelado, de seguridad y evidencias solicitadas en el examen se encuentran organizados en el directorio `docs/parcial2/`:

1. **[Análisis Integral de Procesos, Reglas de Negocio, Estados y Seguridad](docs/parcial2/analisis_drogueria.md)**:
   - **Punto 1:** Flujo integrado de procesos de compras a ventas (8 pasos secuenciales).
   - **Punto 2:** 7 Reglas de negocio críticas con ubicación técnica en código Laravel.
   - **Punto 3:** Ciclo de vida y diagramas de estados de Órdenes de Compra y Ventas con impacto contable e inventario.
   - **Punto 4:** 4 Indicadores Clave de Desempeño (KPIs) con fórmulas, utilidad e impacto en toma de decisiones.
   - **Punto 5:** Diccionario de datos técnico exhaustivo (`lots`, `purchase_details`, `sale_details`) y justificación de trazabilidad sanitaria.
   - **Punto 6:** Matriz de seguridad Rol × Módulo × Acción (Admin, Regente, Cajero, Auxiliar) y justificación de segregación de funciones.
2. **[Modelo Entidad-Relación y Diagrama de Base de Datos](docs/parcial2/er_drogueria.md)**:
   - Diagrama Mermaid con cardinalidades y claves foráneas.
   - Justificación de trazabilidad por lote y fecha de expiración.
   - Imagen en alta definición: **[`docs/parcial2/er_drogueria.png`](docs/parcial2/er_drogueria.png)**.
3. **[Evidencias y Capturas de Ejecución](docs/parcial2/capturas/README.md)**:
   - `01_describe_lots.png`: Estructura e índices de la tabla `lots` en MySQL.
   - `02_codigo_modelo_seeder_servicio.png`: Código fuente del Modelo `Lot`, Seeder de roles y `DispensingService`.
   - `03_tinker_antes_despues_dispensing.png`: Simulación Tinker con aplicación del algoritmo FEFO y discriminación de lotes.
   - `04_tinker_error_stock_insuficiente.png`: Simulación Tinker de excepción y rollback automático ante stock insuficiente o vencido.

---

## 🏥 Contexto Empresarial: Droguería Vida Sana

Droguería Vida Sana opera en Cartago (Valle del Cauca) y requiere un ERP robusto que no solo registre transacciones comerciales, sino que asegure el cumplimiento de las **Buenas Prácticas de Dispensación y Almacenamiento Farmacéutico**. 

### 1. El Reto de la Caducidad: Algoritmo FEFO (*First Expired, First Out*)
A diferencia de un negocio comercial común que emplea FIFO (PEPS: primero en entrar, primero en salir), en el sector farmacéutico el orden de despacho está dictado **estrictamente por la fecha de vencimiento** (`expiration_date`):
1. El sistema **ignora de forma automática** cualquier lote cuya fecha de caducidad sea inferior o igual a la fecha del sistema (`expiration_date <= TODAY`), clasificándolo como merma/cuarentena sanitaria.
2. Los lotes vigentes se ordenan cronológicamente en orden ascendente (`ORDER BY expiration_date ASC`).
3. El despacho consume primero el lote más próximo a caducar. Si la cantidad solicitada excede la disponibilidad de dicho lote, consume el remanente del siguiente lote vigente en la fila cronológica.
4. Si la sumatoria de stock no vencido es insuficiente para cubrir la solicitud, el sistema **aborta la transacción de forma atómica** (Rollback), protegiendo la integridad del inventario.

---

## 🗄️ Modelo de Datos y Trazabilidad Sanitaria

### Tabla `lots`
Almacena los lotes recibidos por producto, permitiendo trazabilidad por registro INVIMA y retiro preventivo de mercado:

| Campo | Tipo | Nulo | Restricciones / Índices | Propósito |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | NO | `PRIMARY KEY AUTO_INCREMENT` | Identificador único del lote en el sistema. |
| `product_id` | `BIGINT UNSIGNED` | NO | `FOREIGN KEY` &rarr; `productos(id)` ON DELETE CASCADE | Vínculo directo con el producto farmacéutico. |
| `lot_number` | `VARCHAR(60)` | NO | `UNIQUE INDEX (product_id, lot_number)` | Número de lote asignado por el laboratorio fabricante. |
| `expiration_date` | `DATE` | NO | `INDEX (expiration_date)` | Fecha límite de caducidad; indexada para acelerar consultas FEFO. |
| `quantity` | `INT` | NO | `DEFAULT 0` | Cantidad física disponible del lote para dispensación. |
| `created_at` | `TIMESTAMP` | SÍ | Auditoría | Fecha y hora de ingreso al inventario. |
| `updated_at` | `TIMESTAMP` | SÍ | Auditoría | Fecha y hora de la última modificación o despacho. |
| `deleted_at` | `TIMESTAMP` | SÍ | `SoftDeletes` | Borrado lógico sin pérdida de histórico para auditorías INVIMA. |

```sql
-- Índice de unicidad para evitar duplicados del mismo lote por laboratorio:
UNIQUE KEY unique_product_lot (product_id, lot_number);

-- Índice optimizado para dispensación FEFO:
INDEX lots_expiration_date_index (expiration_date);
```

### Justificación de `lot_id` en `sale_details`
Guardar el `lot_id` en el detalle de la venta es un requisito regulatorio no negociable en Colombia. Si un laboratorio farmacéutico o el INVIMA emiten una alerta sanitaria (ej. retiro de lote por contaminación o falla terapéutica), la droguería debe ser capaz de consultar en segundos:
- ¿A qué pacientes/clientes se les dispensó ese lote específico?
- ¿En qué fecha y factura se entregó?
- ¿Qué cantidad física queda en cuarentena dentro de los anaqueles?

---

## 🔐 Matriz de Seguridad y Roles (Spatie Laravel Permission)

Se definieron 4 roles con segregación estricta de funciones:

| Módulo / Acción | `admin` (Gerente) | `regente` (Dir. Técnico) | `cajero` (Punto Venta) | `auxiliar-bodega` |
| :--- | :---: | :---: | :---: | :---: |
| **Órdenes de Compra (Crear/Aprobar)** | ✅ | ✅ | ❌ | ❌ |
| **Recepción Técnica y Cuarentena** | ✅ | ✅ | ❌ | ✅ |
| **Punto de Venta / Facturación** | ✅ | ⚠️ *(Temporal)* | ✅ | ❌ |
| **Ajustes de Stock / Mermas Vencidas** | ✅ | ✅ | ❌ | ❌ |
| **Gestión de Lotes y Fechas Caducidad** | ✅ | ✅ | ❌ | ❌ |
| **Informes Financieros y Rentabilidad** | ✅ | ❌ | ❌ | ❌ |

> **Nota sobre el Regente cubriendo Cajas:**  
> Por principio contable de segregación de funciones, quien recepciona y ajusta inventario no debe operar la caja registradora en el día a día para evitar fraudes u ocultamiento de faltantes. En caso de requerir cobertura extraordinaria (ej. sábados), se implementa mediante asignación temporal del rol `cajero` con trazabilidad completa de sesión y arqueo ciego obligatorio.

---

## ⚙️ Servicio Transaccional de Dispensación (`DispensingService`)

El servicio [`app/Services/DispensingService.php`](app/Services/DispensingService.php) implementa la lógica FEFO dentro de una transacción de base de datos con bloqueo pesimista (`lockForUpdate`):

```php
// Ejemplo de invocación en controlador o Tinker:
use App\Models\Product;
use App\Services\DispensingService;

$product = Product::findOrFail(1);
$dispensingService = new DispensingService();

$result = $dispensingService->dispense($product, 8);
// Retorna arreglo con:
// - cantidad solicitada y dispensada
// - detalle de lotes afectados y cantidades descontadas
// - stock restante del producto
```

### Comportamiento del Algoritmo:
1. **Filtro de Lotes No Vencidos:**
   ```php
   $lots = $product->lots()
       ->where('expiration_date', '>', now()->toDateString())
       ->where('quantity', '>', 0)
       ->orderBy('expiration_date', 'asc')
       ->lockForUpdate()
       ->get();
   ```
2. **Validación de Stock Útil:**
   Si `$lots->sum('quantity') < $quantity`, lanza `Illuminate\Validation\ValidationException` indicando el faltante y realiza `DB::rollBack()`.
3. **Consumo y Actualización Atómica:**
   Itera cronológicamente sobre los lotes vigentes, descuenta de cada lote (`$lot->decrement('quantity', $deduct)`) y descuenta del stock total del producto.

---

## 🚀 Puesta en Marcha con Docker y Laravel Sail

### 1. Clonar el Repositorio
```bash
git clone https://github.com/MiguelAngelRamirezGutierrez/drogueria_vida_sana.git
cd drogueria_vida_sana
```

### 2. Configurar Variables de Entorno
El proyecto está preconfigurado para evitar colisiones de puertos con otros entornos:
- Puerto Web: `APP_PORT=8095` (`http://localhost:8095`)
- Puerto MySQL: `FORWARD_DB_PORT=3316`

```bash
cp .env.example .env
```

### 3. Iniciar Contenedores Docker
```bash
./vendor/bin/sail up -d
```

### 4. Ejecutar Migraciones y Seeders
```bash
# Migrar la base de datos (incluyendo la tabla lots):
./vendor/bin/sail artisan migrate

# Poblar roles, permisos, usuarios de prueba y catálogo farmacéutico:
./vendor/bin/sail artisan db:seed --class=DrogueriaRolesSeeder
./vendor/bin/sail artisan db:seed --class=DrogueriaProductSeeder
```

### 5. Usuarios de Prueba Preconfigurados
- **Administrador:** `admin@vidasana.com` | `password`
- **Regente de Farmacia:** `regente@vidasana.com` | `password`
- **Cajero:** `cajero@vidasana.com` | `password`
- **Auxiliar de Bodega:** `bodega@vidasana.com` | `password`

---

## 🧪 Pruebas Automatizadas

El proyecto incluye un conjunto de pruebas automatizadas con PHPUnit/Pest en [`tests/Feature/SegundoParcialDrogueriaTest.php`](tests/Feature/SegundoParcialDrogueriaTest.php) que validan el 100% de los requerimientos:

```bash
./vendor/bin/sail artisan test --filter=SegundoParcialDrogueriaTest
```

**Resultado de ejecución:**
```
   PASS  Tests\Feature\SegundoParcialDrogueriaTest
  ✓ lots table and relationships work properly                           0.64s  
  ✓ drogueria roles and permissions seeder                               0.40s  
  ✓ dispensing service ignores expired lots and applies fefo             0.46s  
  ✓ dispensing service fails when insufficient unexpired stock and roll… 0.40s  

  Tests:    4 passed (34 assertions)
  Duration: 2.30s
```

---

## 📸 Evidencias Fotográficas

Todas las capturas de terminal, base de datos y Tinker se encuentran documentadas en [`docs/parcial2/capturas/README.md`](docs/parcial2/capturas/README.md):

| Captura | Archivo | Descripción |
| :--- | :--- | :--- |
| **01** | [`01_describe_lots.png`](docs/parcial2/capturas/01_describe_lots.png) | Estructura formal de `lots` en MySQL y sus índices. |
| **02** | [`02_codigo_modelo_seeder_servicio.png`](docs/parcial2/capturas/02_codigo_modelo_seeder_servicio.png) | Estructura de archivos y clases desarrolladas. |
| **03** | [`03_tinker_antes_despues_dispensing.png`](docs/parcial2/capturas/03_tinker_antes_despues_dispensing.png) | Prueba Tinker demostrando FEFO (lote vencido intacto, lote próximo a vencer consumido primero). |
| **04** | [`04_tinker_error_stock_insuficiente.png`](docs/parcial2/capturas/04_tinker_error_stock_insuficiente.png) | Prueba Tinker demostrando excepción y reversión ante stock insuficiente. |

---

## 📦 Estructura del Proyecto

```
drogueria_vida_sana/
├── app/
│   ├── Models/
│   │   ├── Lot.php                # Modelo de Lotes con scopes vigentes y FEFO
│   │   ├── Product.php            # Modelo Producto con relación hasMany a lots
│   │   └── Producto.php           # Alias en español para compatibilidad
│   └── Services/
│       └── DispensingService.php  # Servicio de dispensación con algoritmo FEFO y transacción ACID
├── database/
│   ├── migrations/
│   │   └── 2026_10_22_000001_create_lots_table.php  # Migración con UNIQUE(product_id, lot_number)
│   └── seeders/
│       ├── DrogueriaRolesSeeder.php    # Roles, permisos y usuarios de prueba
│       └── DrogueriaProductSeeder.php  # Categorías y productos farmacéuticos
├── docs/
│   └── parcial2/
│       ├── analisis_drogueria.md       # Desarrollo de los 6 puntos teóricos y de análisis
│       ├── er_drogueria.md             # Diagrama MER Mermaid y cardinalidad
│       ├── er_drogueria.png            # Diagrama MER gráfico en alta resolución
│       └── capturas/                   # Evidencias en capturas PNG de terminal y Tinker
│           ├── 01_describe_lots.png
│           ├── 02_codigo_modelo_seeder_servicio.png
│           ├── 03_tinker_antes_despues_dispensing.png
│           ├── 04_tinker_error_stock_insuficiente.png
│           └── README.md
├── tests/
│   └── Feature/
│       └── SegundoParcialDrogueriaTest.php  # 4 Pruebas de integración del examen
├── compose.yaml                        # Docker Compose con puertos 8095 / 3316
└── README.md                           # Documentación principal del sistema
```

---
**Desarrollado para:** Corporación de Estudios Tecnológicos del Norte del Valle — COTECNOVA (2026)  
**Estudiantes:**  
* **Miguel Ángel Ramírez Gutiérrez** &bull; [GitHub: @MiguelAngelRamirezGutierrez](https://github.com/MiguelAngelRamirezGutierrez)  
* **Andrés Felipe Mejía Suaza** &bull; [GitHub: @NiquelAwol](https://github.com/NiquelAwol)
