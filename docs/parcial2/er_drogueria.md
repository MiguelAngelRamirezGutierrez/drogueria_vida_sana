# Diagrama Entidad - Relación (MER)
**Droguería Vida Sana — Cartago (Valle del Cauca)**  
*Segundo Parcial · Cotecnova 2026*  
**Estudiantes:** Miguel Ángel Ramírez Gutiérrez ([@MiguelAngelRamirezGutierrez](https://github.com/MiguelAngelRamirezGutierrez)) & Andrés Felipe Mejía Suaza ([@NiquelAwol](https://github.com/NiquelAwol))  

```mermaid
erDiagram
    SUPPLIERS ||--o{ PURCHASES : "emite a"
    PURCHASES ||--|{ PURCHASE_DETAILS : "contiene"
    PRODUCTS ||--o{ PURCHASE_DETAILS : "adquirido en"
    PRODUCTS ||--o{ LOTS : "posee multiples"
    LOTS ||--o{ SALE_DETAILS : "dispensa unidades a"
    SALES ||--|{ SALE_DETAILS : "incluye"
    PRODUCTS ||--o{ SALE_DETAILS : "vendido en"
    CLIENTS ||--o{ SALES : "compra en"
    USERS ||--o{ SALES : "registrada por cajero"
    USERS ||--o{ PURCHASES : "creada por admin"
    USERS ||--o{ MODEL_HAS_ROLES : "asignado a"
    ROLES ||--o{ MODEL_HAS_ROLES : "agrupa a"
    ROLES ||--o{ ROLE_HAS_PERMISSIONS : "contiene"
    PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : "concede"

    PRODUCTS {
        bigint id PK
        varchar name "Nombre comercial y generico"
        varchar invima_code "Registro sanitario"
        enum type "Venta libre, Formula, Control Especial"
        decimal price "Precio de venta"
        int stock "Existencia total no vencida"
        boolean active "Estado comercial"
    }

    LOTS {
        bigint id PK
        bigint product_id FK "Relaciona medicamento"
        varchar lot_number "Codigo alfanumerico laboratorio"
        date expiration_date "Fecha de vencimiento (Indice FEFO)"
        int quantity "Existencias de este lote"
    }

    SUPPLIERS {
        bigint id PK
        varchar nit "Documento tributario"
        varchar name "Distribuidora o Laboratorio"
        varchar phone "Contacto"
        varchar city "Ciudad de origen"
    }

    PURCHASES {
        bigint id PK
        varchar invoice_number "Factura de compra"
        bigint supplier_id FK
        bigint user_id FK "Usuario que crea orden"
        enum status "Borrador, Aprobada, Recepcionada"
        decimal total "Monto factura"
    }

    PURCHASE_DETAILS {
        bigint id PK
        bigint purchase_id FK
        bigint product_id FK
        varchar lot_number "Lote ingresado"
        date expiration_date "Vencimiento declarado"
        int quantity "Unidades recibidas"
        decimal unit_cost
        decimal subtotal
    }

    SALES {
        bigint id PK
        varchar invoice_number "FV-XXXXX"
        bigint client_id FK
        bigint user_id FK "Cajero que factura"
        date sale_date
        decimal subtotal
        decimal tax "IVA 19%"
        decimal total
        enum status "pendiente, pagada, anulada"
    }

    SALE_DETAILS {
        bigint id PK
        bigint sale_id FK
        bigint product_id FK
        bigint lot_id FK "Trazabilidad exacta del lote consumido"
        int quantity "Unidades dispensadas"
        decimal unit_price
        decimal subtotal
    }

    CLIENTS {
        bigint id PK
        enum document_type "CC, NIT, CE, PP"
        varchar document_number
        varchar name
        varchar phone
        varchar address
    }

    USERS {
        bigint id PK
        varchar name
        varchar email
        varchar password
    }

    ROLES {
        bigint id PK
        varchar name "admin, regente, cajero, auxiliar-bodega"
    }

    PERMISSIONS {
        bigint id PK
        varchar name "recibir-lotes, dispensar-control-especial..."
    }
```

---

### Explicación de Cardinalidades y Flujo de Datos

1. **`PRODUCTS (1) ───< LOTS (N)`**:  
   Un mismo medicamento puede tener múltiples lotes en bodega con diferentes fechas de caducidad. Cada lote pertenece estrictamente a un único producto.
2. **`LOTS (1) ───< SALE_DETAILS (N)`**:  
   Un lote puede abastecer varias líneas de venta en diferentes transacciones. Cada línea de venta de un producto identifica de qué lote provienen sus unidades para garantizar la trazabilidad sanitaria (**FEFO**).
3. **`SALES (1) ───< SALE_DETAILS (N) >─── (N:1) PRODUCTS`**:  
   Una venta contiene uno o más productos vendidos, registrando cantidad, precio y lote.
4. **`SUPPLIERS (1) ───< PURCHASES (N) ───< PURCHASE_DETAILS (N)`**:  
   Un proveedor puede surtir múltiples compras; cada compra contiene detalles donde se declaran los lotes y vencimientos que luego se dan de alta en `lots`.
5. **`USERS (N) ─── (N) ROLES (N) ─── (N) PERMISSIONS`**:  
   Modelo RBAC (Role-Based Access Control) mediante Spatie para gobernar la segregación de funciones entre Administrador, Regente, Cajero y Auxiliar de Bodega.
