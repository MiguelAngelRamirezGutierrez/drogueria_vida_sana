# Análisis de la Droguería Vida Sana
**Seminario Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD**  
*Cotecnova 2026 · Segundo Parcial de Software de Gestión Empresarial*  
**Empresa:** Droguería Vida Sana — Cartago (Valle del Cauca)  
**Estudiantes / Autores:**  
* **Miguel Ángel Ramírez Gutiérrez** &bull; [GitHub: @MiguelAngelRamirezGutierrez](https://github.com/MiguelAngelRamirezGutierrez)  
* **Andrés Felipe Mejía Suaza** &bull; [GitHub: @NiquelAwol](https://github.com/NiquelAwol)  

---

## 1. Flujo Integrado del Negocio

El flujo operativo integral de la **Droguería Vida Sana** abarca desde la identificación de la necesidad de abastecimiento hasta el monitoreo continuo en el Dashboard gerencial, garantizando la trazabilidad sanitaria, el cumplimiento de la normatividad INVIMA y la política **FEFO** (*First Expired, First Out*).

```
[1. Detección Quiebre Stock]
            ↓
[2. Creación Orden de Compra (Admin)]
            ↓
[3. Recepción Física (Aux. Bodega) y Técnica (Regente)]
            ↓
[4. Ingreso de Lotes y Vencimientos a Inventario]
            ↓
[5. Solicitud de Dispensación en Caja]
            ↓
    ¿Es Control Especial?
     ├── SÍ → [Autorización Regente de Farmacia]
     └── NO → [Pasa directo a Facturación]
            ↓
[6. Algoritmo FEFO Automático (Descuento Lote más Próximo)]
            ↓
[7. Emisión de Factura con Lote Trazable]
            ↓
[8. Actualización de KPIs en Dashboard en Tiempo Real]
```

### Descripción Paso a Paso del Flujo:

1. **Detección de Necesidad de Abastecimiento:**
   El sistema evalúa de forma automatizada los niveles de inventario por producto frente al punto de reorden y stock de seguridad. Cuando la existencia total no vencida es insuficiente para cubrir la demanda estimada de 15 días, se emite una sugerencia de compra.

2. **Generación y Aprobación de Orden de Compra:**
   El **Administrador** elabora la Orden de Compra formal dirigida a los proveedores autorizados (por ejemplo, *Distribuidora Farmacéutica del Valle*), detallando principios activos, presentaciones comerciales, cantidades requeridas y condiciones de entrega.

3. **Recepción de Mercancía y Segregación de Funciones:**
   Al llegar el pedido a la sede en Cartago, interviene la segregación operativa:
   * El **Auxiliar de Bodega** realiza el descargue, conteo ciego de bultos y verificación de integridad física externa.
   * El **Regente de Farmacia** (responsable técnico ante la Secretaría de Salud) ejecuta la **Recepción Técnica**: inspecciona registro sanitario INVIMA, empaque hermético, lote asignado por el laboratorio fabricante y fecha de expiración (la cual debe superar un mínimo de 12 meses de vigencia).

4. **Incorporación a Inventario por Lote (`lots`):**
   El sistema registra cada lote individualmente asociándolo a su medicamento (`product_id`), registrando su código alfanumérico (`lot_number`), fecha de caducidad (`expiration_date`) y cantidad ingresada (`quantity`). El stock global del producto se incrementa atómicamente dentro de una transacción.

5. **Dispensación y Venta en Mostrador:**
   El cliente acude a la farmacia. El **Cajero** ingresa la solicitud:
   * **Medicamento de Venta Libre / Cuidado Personal:** El cajero selecciona el producto y cantidad.
   * **Medicamento con Fórmula Médica:** Se valida la orden médica y datos del prescriptor.
   * **Medicamento de Control Especial:** El sistema bloquea el despacho hasta que el **Regente de Farmacia** verifique la receta oficial foliada y autorice la transacción en el sistema mediante sus credenciales.

6. **Ejecución del Algoritmo FEFO (Servicio `DispensingService`):**
   Dentro de una transacción de base de datos (`DB::transaction`) y con bloqueo de fila (`lockForUpdate`), el sistema:
   * Filtra e ignora cualquier lote vencido (`expiration_date < today()`).
   * Valida que la suma de existencias vigentes cubra la cantidad solicitada.
   * Ordena los lotes vigentes de forma ascendente por vencimiento (`orderBy('expiration_date', 'asc')`).
   * Descuenta secuencialmente del lote que vence primero hasta agotarlo, pasando al siguiente lote más cercano a expirar.
   * Descuenta simultáneamente el stock maestro del producto.

7. **Emisión de Documento y Facturación:**
   Se imprime la factura legal que incluye el detalle del lote exacto dispensado para cada línea de producto. Esto garantiza que el paciente sepa qué lote adquirió y la farmacia conserve el registro histórico para farmacovigilancia.

8. **Actualización de Indicadores (Dashboard):**
   El sistema refresca de forma inmediata los indicadores gerenciales: valor del inventario, ventas del turno, rotación y alertas de semaforización de vencimientos (30/60 días).

---

## 2. Reglas de Negocio

A continuación se especifican las 7 reglas de negocio fundamentales del sistema farmacéutico:

| N° | Regla de Negocio | Descripción Funcional | Módulo | Dónde se Implementa | Justificación Técnica / Arquitectónica |
|:---|:---|:---|:---|:---|:---|
| **RN-01** | **FEFO Estricto** | La dispensación de medicamentos debe consumir primero las unidades del lote no vencido con la fecha de caducidad más cercana. | Ventas / Inventario | **Servicio (`DispensingService`)** | Encapsula la lógica transaccional y el ordenamiento `orderBy('expiration_date', 'asc')` garantizando que no se salte el protocolo sanitario. |
| **RN-02** | **Bloqueo Absoluto de Lotes Vencidos** | Bajo ninguna circunstancia se pueden vender unidades de un lote cuya fecha de caducidad sea menor o igual a la fecha actual (`<= now()`). | Ventas / Lotes | **Servicio y Scope Eloquent (`activeLots`, `vigentes`)** | La validación en el servicio rechaza cualquier intento de despacho y el Scope filtra las consultas del catálogo para evitar mostrarlos al cajero. |
| **RN-03** | **Autorización Obligatoria para Medicamentos de Control Especial** | Los medicamentos catalogados como estupefacientes o control especial requieren aprobación digital explícita del Regente de Farmacia. | Ventas / Seguridad | **Middleware (`EnsureRegenteAuthorized`) y Policy** | Intercepta la petición HTTP antes de llegar al controlador, comprobando que el usuario tenga el rol de Regente o un token de autorización técnica. |
| **RN-04** | **Segregación de Funciones en Adquisición** | El usuario que elabora o autoriza una orden de compra no puede ser el mismo usuario que registra la recepción técnica y física en bodega. | Compras / Inventario | **Policy de Laravel (`PurchasePolicy@receive`)** | Compara el `user_id` del creador de la orden con el `auth()->id()` del receptor, arrojando excepción de autorización si coinciden. |
| **RN-05** | **Unicidad e Integridad de Lote por Medicamento** | No puede duplicarse el identificador de lote para un mismo producto; las fechas de vencimiento al ingresar deben ser a futuro. | Lotes / Compras | **Form Request (`StoreLotRequest`) y Constraint de BD** | Valida `Rule::unique('lots')->where('product_id', ...)` y una restricción a nivel de base de datos con un índice único compuesto `(product_id, lot_number)`. |
| **RN-06** | **Atomicidad Transaccional y Devolución de Stock** | Si no hay stock suficiente no se crea la venta ni se descuenta ningún lote (Rollback). Si se anula una venta, las unidades regresan al lote específico de donde salieron. | Ventas / Inventario | **Servicio (`SaleService` / `DispensingService`)** | Uso de `DB::transaction()` y `lockForUpdate()`. Evita ventas huérfanas o descuentos de stock asincrónicos ante caídas del servidor. |
| **RN-07** | **Alerta Temprana y Semáforo de Vencimiento** | Todo lote a menos de 90 días de vencer entra en semáforo amarillo y a menos de 30 días en rojo, notificando en el Dashboard. | Dashboard / Alertas | **Servicio de KPIs y Vistas Blade** | Calcula dinámicamente `Carbon::now()->diffInDays($lot->expiration_date)` y aplica estilos visuales preventivos en la interfaz. |

---

## 3. Ciclo de Vida y Estados

### 3.1. Estados de una Orden de Compra (`purchases`)

```
[Borrador] ➔ [Aprobada] ➔ [En Tránsito] ➔ [Recepcionada Parcial] ➔ [Recepcionada Total]
    │             │                                                       ▲
    └─────────────┴───────────────── [Cancelada] ─────────────────────────┘
```

| Estado | Descripción Operativa | Efecto en el Inventario |
|:---|:---|:---|
| **Borrador** | La orden está siendo construida por el área de compras; se cotizan precios y cantidades. | **Sin efecto en inventario.** No altera existencias físicas ni lotes. |
| **Aprobada** | El Administrador autoriza el presupuesto y se envía el pedido formal al laboratorio. | **Inventario comprometido:** no altera stock físico, pero genera alerta de mercancía esperada. |
| **En Tránsito** | El distribuidor despachó la mercancía desde bodega externa hacia Cartago. | **Sin efecto físico.** Permite preparar espacio en bodega. |
| **Recepcionada Parcial** | Se recibe una entrega parcial del pedido; se verifican los lotes efectivamente entregados. | **Incremento inmediato:** se crean los lotes recibidos en la tabla `lots` y aumenta el stock general del medicamento proporcionalmente. |
| **Recepcionada Total** | Se completa la entrega del 100% de los ítems con aprobación técnica del Regente. | **Incremento final:** todos los lotes ingresan al inventario disponible para dispensación y la orden se cierra. |
| **Cancelada** | La orden fue revocada por quiebre de stock del distribuidor o acuerdo comercial. | **Sin efecto en inventario.** |

---

### 3.2. Estados de una Venta / Dispensación (`sales`)

```
[Pendiente / En Proceso] ➔ [Dispensada / Pagada]
          │
          └───> [Anulada]
```

| Estado | Descripción Operativa | Efecto en el Inventario |
|:---|:---|:---|
| **Pendiente** | La venta está registrada en el terminal; si contiene medicamentos de control especial, aguarda autorización del Regente. | **Bloqueo temporal:** el stock del lote queda apartado mediante transacción en curso. |
| **Dispensada / Pagada** | El cliente cancela el valor, el cajero entrega los medicamentos y se imprime la factura con el lote. | **Descuento atómico:** se reduce la columna `quantity` del lote respectivo en `lots` según el algoritmo FEFO y se decrementa el `stock` general en `products`. |
| **Anulada** | El cliente desiste de la compra en caja o se detecta un error de digitación previo a la entrega. | **Devolución de stock:** las unidades descontadas se reintegran exactamente al lote de donde salieron (`$lot->increment('quantity', $qty)`) y al stock global. |

---

## 4. Indicadores Clave de Desempeño (KPIs)

Para la toma de decisiones estratégicas del Administrador y el Regente técnico, se definen los siguientes 4 KPIs:

### KPI 1: Unidades en Riesgo Inminente de Caducidad ($\le 30$ y $\le 60$ días)
* **Objetivo:** Evitar pérdidas económicas por medicamentos vencidos en estantería y prevenir riesgos sanitarios a pacientes.
* **Fórmula:**
  $$\text{Unidades Próximas a Vencer} = \sum \text{lots.quantity} \quad \text{donde } \text{expiration\_date} \in [\text{hoy}, \text{hoy} + 30 \text{ días}]$$
* **Utilidad Gerencial:** Permite al regente gestionar con los distribuidores la devolución o cambio oportuno de lotes antes del vencimiento (política de canje farmacéutico), o coordinar con médicos y clientes la rotación preferencial.

### KPI 2: Valor Total Monetario del Inventario Disponible
* **Objetivo:** Conocer el capital de trabajo inmovilizado en mercancía y la valorización financiera de la farmacia.
* **Fórmula:**
  $$\text{Valor del Inventario} = \sum (\text{products.stock} \times \text{products.price})$$
* **Utilidad Gerencial:** Monitorea la liquidez de la empresa y sirve para conciliar balances contables y pólizas de seguro de la droguería.

### KPI 3: Tasa de Dispensación de Medicamentos de Control Especial
* **Objetivo:** Cumplir con los informes periódicos y auditorías ante el Fondo Nacional de Estupefacientes (FNE) y la Secretaría de Salud Departamental.
* **Fórmula:**
  $$\% \text{ Control Especial} = \left( \frac{\text{Ventas de Control Especial}}{\text{Total Ventas Realizadas}} \right) \times 100$$
* **Utilidad Gerencial:** Supervisa el volumen de recetas de control retenidas y asegura que el 100% cuenten con el acta de autorización del Regente.

### KPI 4: Índice de Eficiencia FEFO (% de Salidas en Orden Correcto)
* **Objetivo:** Medir el porcentaje de transacciones que respetaron de manera estricta el despacho del lote más antiguo.
* **Fórmula:**
  $$\text{Cumplimiento FEFO} = \left( \frac{\text{Dispensaciones según Lote Más Antiguo}}{\text{Total Dispensaciones Multilote}} \right) \times 100 = 100\%$$
* **Utilidad Gerencial:** Certifica que el sistema automatizado eliminó el error humano de entregar jarabes o tabletas viejas teniendo lotes nuevos.

---

## 5. Diseño del Modelo de Datos

### 5.1. Entidades Principales del Sistema
1. **`products` (Medicamentos):** Información general del fármaco, concentración, presentación comercial, registro INVIMA, tipo de medicamento (venta libre, control especial, fórmula médica), stock consolidado y precio.
2. **`lots` (Lotes de Medicamentos):** Lote de fábrica, fecha de caducidad y existencias físicas actuales de ese lote.
3. **`suppliers` (Proveedores):** Laboratorios y distribuidoras farmacéuticas.
4. **`purchases` y `purchase_details` (Compras):** Adquisición de insumos y asignación de lotes entrantes.
5. **`sales` y `sale_details` (Ventas):** Facturas emitidas con trazabilidad de lotes salientes.
6. **`clients` (Pacientes / Clientes):** Datos del comprador, documento y dirección.
7. **`users`, `roles`, `permissions` (Seguridad Spatie):** Control de acceso basado en roles (RBAC).

---

### 5.2. Diccionario de Datos

#### Tabla: `lots` (Lotes de Medicamentos)
| Campo | Tipo de Dato | Longitud / Formato | Nulo | Por Defecto | Descripción | Restricciones / Claves |
|:---|:---|:---|:---|:---|:---|:---|
| `id` | BIGINT UNSIGNED | 20 | NO | Auto Increment | Identificador único del lote | Clave Primaria (PK) |
| `product_id` | BIGINT UNSIGNED | 20 | NO | N/A | Medicamento al que pertenece el lote | Clave Foránea (FK) `products(id)` ON DELETE RESTRICT |
| `lot_number` | VARCHAR | 50 | NO | N/A | Número alfanumérico de lote del laboratorio | Índice Único compuesto `(product_id, lot_number)` |
| `expiration_date` | DATE | AAAA-MM-DD | NO | N/A | Fecha de caducidad sanitaria del lote | Índice ordinario para ordenamiento FEFO |
| `quantity` | INT | 11 | NO | 0 | Unidades vigentes disponibles en bodega | CHECK (`quantity >= 0`) |
| `created_at` | TIMESTAMP | N/A | YES | NULL | Fecha de creación del registro | Timestamp Laravel |
| `updated_at` | TIMESTAMP | N/A | YES | NULL | Fecha de última modificación | Timestamp Laravel |
| `deleted_at` | TIMESTAMP | N/A | YES | NULL | Fecha de baja lógica (Soft Delete) | SoftDeletes |

#### Tabla: `purchase_details` (Detalle de Factura de Compra)
| Campo | Tipo de Dato | Longitud / Formato | Nulo | Por Defecto | Descripción | Restricciones / Claves |
|:---|:---|:---|:---|:---|:---|:---|
| `id` | BIGINT UNSIGNED | 20 | NO | Auto Increment | Identificador único de la línea de compra | Clave Primaria (PK) |
| `purchase_id` | BIGINT UNSIGNED | 20 | NO | N/A | Factura de compra a la que pertenece | Clave Foránea (FK) `purchases(id)` ON DELETE CASCADE |
| `product_id` | BIGINT UNSIGNED | 20 | NO | N/A | Medicamento comprado | Clave Foránea (FK) `products(id)` ON DELETE RESTRICT |
| `lot_number` | VARCHAR | 50 | NO | N/A | Lote recepcionado del laboratorio | Almacena el código para crear o alimentar `lots` |
| `expiration_date` | DATE | AAAA-MM-DD | NO | N/A | Fecha de vencimiento declarada en empaque | Debe ser fecha futura |
| `quantity` | INT | 11 | NO | N/A | Cantidad de unidades adquiridas | CHECK (`quantity > 0`) |
| `unit_cost` | DECIMAL | 12,2 | NO | 0.00 | Costo unitario de compra sin IVA | CHECK (`unit_cost >= 0`) |
| `subtotal` | DECIMAL | 14,2 | NO | 0.00 | Subtotal de la línea (`quantity * unit_cost`) | Calculado |
| `created_at` | TIMESTAMP | N/A | YES | NULL | Fecha de creación | Timestamp |
| `updated_at` | TIMESTAMP | N/A | YES | NULL | Fecha de modificación | Timestamp |

#### Tabla: `sale_details` (Detalle de Venta y Dispensación)
| Campo | Tipo de Dato | Longitud / Formato | Nulo | Por Defecto | Descripción | Restricciones / Claves |
|:---|:---|:---|:---|:---|:---|:---|
| `id` | BIGINT UNSIGNED | 20 | NO | Auto Increment | Identificador único de la línea de venta | Clave Primaria (PK) |
| `sale_id` | BIGINT UNSIGNED | 20 | NO | N/A | Factura de venta a la que pertenece | Clave Foránea (FK) `sales(id)` ON DELETE CASCADE |
| `product_id` | BIGINT UNSIGNED | 20 | NO | N/A | Medicamento dispensado | Clave Foránea (FK) `products(id)` ON DELETE RESTRICT |
| `lot_id` | BIGINT UNSIGNED | 20 | YES | NULL | Lote específico de donde salieron las unidades | Clave Foránea (FK) `lots(id)` ON DELETE SET NULL / RESTRICT |
| `quantity` | INT | 11 | NO | N/A | Cantidad de unidades entregadas del lote | CHECK (`quantity > 0`) |
| `unit_price` | DECIMAL | 12,2 | NO | 0.00 | Precio de venta cobrado al público | CHECK (`unit_price >= 0`) |
| `subtotal` | DECIMAL | 14,2 | NO | 0.00 | Subtotal de la línea (`quantity * unit_price`) | Calculado |
| `created_at` | TIMESTAMP | N/A | YES | NULL | Fecha de creación | Timestamp |
| `updated_at` | TIMESTAMP | N/A | YES | NULL | Fecha de modificación | Timestamp |

---

### 5.3. Pregunta Conceptual de Evaluación
> **¿En qué tabla se guarda de qué lote salió cada unidad vendida? Justificar.**

**Respuesta:**  
Se guarda en la tabla **`sale_details`** mediante la columna **`lot_id`** (clave foránea que referencia a la tabla `lots`). En casos donde una venta requiera unidades de más de un lote para completar la cantidad pedida, el sistema genera **una fila independiente en `sale_details` por cada lote consumido** (o mediante una tabla pivote de asignación de lotes `sale_detail_lots`).

**Justificación Técnica, Regulatoria y Epidemiológica:**
1. **Trazabilidad Farmacológica y Normativa INVIMA:**  
   Ante una alerta sanitaria o un retiro del mercado (*recall*) emitido por el INVIMA o por el laboratorio fabricante (por ejemplo, un lote contaminado o con defecto de estabilidad), la droguería debe ser capaz de consultar en la base de datos exactamente qué clientes compraron ese lote, en qué fecha y qué cajero lo despachó, para contactarlos inmediatamente e impedir afectaciones a la salud pública.
2. **Farmacovigilancia y Auditorías Sanitarias:**  
   Si un paciente reporta una reacción adversa a un medicamento (RAM), la Secretaría de Salud exige conocer el lote específico para realizar el cotejo químico con la contramuestra del laboratorio.
3. **Consistencia Transaccional y Reversibilidad:**  
   Al anular una venta en el ERP, vincular el `lot_id` en el detalle permite devolver las unidades exactamente al lote del cual fueron extraídas (`$lot->increment('quantity', $quantity)`), manteniendo intacta la historia cronológica del inventario.

---

## 6. Matriz de Seguridad y Roles (Spatie)

### 6.1. Matriz de Permisos (Rol × Módulo × Acción)

| Módulo | Acción / Permiso Específico | Admin | Regente | Cajero | Auxiliar Bodega |
|:---|:---|:---:|:---:|:---:|:---:|
| **Productos / Catálogo** | `ver-productos` | ✅ | ✅ | ✅ | ✅ |
| | `crear-productos` | ✅ | ✅ | ❌ | ❌ |
| | `editar-productos` | ✅ | ✅ | ❌ | ❌ |
| | `eliminar-productos` | ✅ | ❌ | ❌ | ❌ |
| **Control Sanitario** | `autorizar-control-especial` | ✅ | ✅ | ❌ | ❌ |
| | `ver-recetas-retenidas` | ✅ | ✅ | ❌ | ❌ |
| **Lotes e Inventario** | `ver-lotes` | ✅ | ✅ | ✅ | ✅ |
| | `recibir-lotes` (Recepción técnica) | ❌ *(Segregación)* | ✅ | ❌ | ❌ |
| | `descargar-mercancia` (Físico) | ❌ | ❌ | ❌ | ✅ |
| | `ajustar-lotes-merma` | ✅ | ✅ | ❌ | ❌ |
| **Compras** | `ver-compras` | ✅ | ✅ | ❌ | ✅ |
| | `crear-compras` (Orden compra) | ✅ | ❌ | ❌ | ❌ |
| | `aprobar-compras` | ✅ | ❌ | ❌ | ❌ |
| **Ventas / POS** | `ver-ventas` | ✅ | ✅ | ✅ | ❌ |
| | `crear-ventas` (Dispensar) | ✅ | ❌ *(En semana)* | ✅ | ❌ |
| | `anular-ventas` | ✅ | ✅ | ❌ | ❌ |
| **Reportes y KPIs** | `ver-reportes` | ✅ | ✅ | ❌ | ❌ |
| | `exportar-informe-invima` | ✅ | ✅ | ❌ | ❌ |

---

### 6.2. Explicación de la Segregación de Funciones
La matriz cumple estrictamente con el principio de **segregación de funciones (quien compra no recibe)**:
* El **Administrador** tiene la potestad de negociar con proveedores, emitir órdenes de compra y aprobar desembolsos financieros (`crear-compras`, `aprobar-compras`), pero **NO tiene asignado el permiso de recepcionar técnicamente los lotes en el almacén** (`recibir-lotes`).
* El **Auxiliar de Bodega** y el **Regente de Farmacia** son los únicos facultados para inspeccionar y dar ingreso físico al lote (`recibir-lotes`). Ninguno de ellos puede crear ni modificar órdenes de compra comerciales.
* **Beneficio de Control Interno:** Se erradica por completo la posibilidad de compras fantasma, ingreso de medicamentos no autorizados o colusión en fraudes de facturación con distribuidores externos.

---

### 6.3. Pregunta de Caso: El Regente cubre la caja los sábados

> **El regente cubre la caja los sábados: ¿le asignaría un segundo rol o un permiso directo? ¿Por qué? ¿Qué rol elegiría al iniciar sesión?**

**1. Asignación: ¿Segundo Rol o Permiso Directo?**  
Se le debe asignar un **segundo rol (`cajero`)** y **NUNCA permisos directos sueltos**.

**Justificación Técnica y de Auditoría:**
* **Buenas Prácticas de Arquitectura RBAC (Role-Based Access Control):**  
  Asignar permisos directos individuales a los usuarios degrada la mantenibilidad del sistema (*Permission Bloat*), genera brechas de seguridad y dificulta saber con exactitud qué responsabilidades tiene un colaborador en un momento dado.
* **Separación de Perfiles y Trazabilidad:**  
  El paquete `spatie/laravel-permission` soporta nativamente la pertenencia a múltiples roles (`$user->assignRole(['regente', 'cajero'])`). Esto permite auditar las transacciones con absoluta claridad: si una factura se emite en sábado, queda registrada bajo el contexto operativo de caja.

**2. ¿Qué rol elegiría al iniciar sesión?**  
El sistema debe implementar un **selector de rol activo de turno (Contextual Role Switcher)** en el login:
* Al iniciar su turno el sábado, el usuario selecciona el rol **`cajero`**.
* Al operar bajo el rol de `cajero`, la interfaz se simplifica mostrando únicamente el módulo de Punto de Venta y cobro, deshabilitando opciones administrativas para evitar distracciones o errores involuntarios durante la atención al público.
* Si durante su turno como cajero se presenta un cliente con una fórmula de medicamento de control especial, el sistema exige una **elevación temporal de privilegios o confirmación con contraseña/pin de supervisor regente** para validar técnicamente la receta, manteniendo la bitácora de auditoría inalterable.
