# API v2 - Documentación de Endpoints

> **Base URL:** `/api/v2`
> **Autenticación:** Bearer Token (OAuth2) requerido en todos los endpoints
> **Header:** `Authorization: Bearer {token}`

---

## 1. GET /v2/client

Obtiene los datos de un cliente (tomador) por su número de documento.

### Request

```json
POST /api/v2/client
Content-Type: application/json

{
  "document": "12345678"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| document | string | Sí | Número de documento del cliente |

### Respuestas

**200 - Éxito**
```json
{
  "nombres": "JUAN",
  "apellidos": "PÉREZ",
  "tipo_id": "DNI",
  "fecha_nacimiento": "1990-05-15",
  "telefono": "1123456789",
  "direccion": "Av. Corrientes 1234",
  "codpostal": "1043",
  "success": true
}
```

**200 - No encontrado**
```json
{
  "success": false
}
```

---

## 2. POST /v2/insured

Valida la existencia de múltiples asegurados por sus documentos.

### Request

```json
POST /api/v2/insured
Content-Type: application/json

{
  "documents": "12345678,87654321,11223344"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| documents | string | Sí | Documentos separados por coma |

### Respuestas

**200 - Todos encontrados**
```json
{
  "success": true,
  "documentsFound": "12345678,87654321,11223344",
  "namesFound": "12345678 JUAN PÉREZ,87654321 MARÍA GARCÍA,11223344 PEDRO LÓPEZ"
}
```

**200 - Algunos no encontrados**
```json
{
  "success": false,
  "documentsFound": "12345678,87654321",
  "namesFound": "12345678 JUAN PÉREZ,87654321 MARÍA GARCÍA",
  "documentsNotFound": "11223344"
}
```

**200 - Ninguno encontrado**
```json
{
  "success": false
}
```

---

## 3. POST /v2/client/create

Crea o actualiza un cliente (tomador).

### Request

```json
POST /api/v2/client/create
Content-Type: application/json

{
  "id": "12345678",
  "codempresa": "BARRIOS",
  "nombres": "JUAN",
  "apellidos": "PÉREZ",
  "tipo_id": "DNI",
  "fecha_nacimiento": "1990-05-15",
  "telefono": "1123456789",
  "direccion": "Av. Corrientes 1234",
  "email": "juan@email.com",
  "codpostal": "1043",
  "sexo": "M"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| id | string | Sí | Documento del cliente (PK) |
| codempresa | string | Sí | Código de empresa |
| nombres | string | Sí | Nombres del cliente |
| apellidos | string | Sí | Apellidos del cliente |
| tipo_id | string | Sí | Tipo de documento (DNI, CUIT, etc.) |
| fecha_nacimiento | string | Sí | Fecha YYYY-MM-DD |
| telefono | string | Sí | Teléfono de contacto |
| direccion | string | No | Dirección completa |
| email | string | No | Correo electrónico válido |
| codpostal | string | Sí | Código postal |
| sexo | string | No | Sexo (M/F) |

### Respuestas

**200 - Éxito**
```json
{
  "success": true
}
```

**200 - Error de validación**
```json
{
  "success": false,
  "message": "Nombres, Apellidos, Tipo ID, Fecha Nacimiento, Teléfono y Codpostal no pueden estar vacíos"
}
```

**200 - Email inválido**
```json
{
  "success": false,
  "message": "Correo electrónico inválido"
}
```

---

## 4. POST /v2/client-insureds/create

Crea o actualiza múltiples asegurados en formato string delimitado.

### Request

```json
POST /api/v2/client-insureds/create
Content-Type: application/json

{
  "insureds": "JUAN,PÉREZ,12345678,DNI,1990-05-15;MARÍA,GARCÍA,87654321,DNI,1985-08-20",
  "codempresa": "BARRIOS"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| insureds | string | Sí | Asegurados: `nombres,apellidos,doc,tipo_doc,fecha_nac` separados por `;` |
| codempresa | string | Sí | Código de empresa |

### Formato de cada asegurado

```
nombres,apellidos,documento,tipo_documento,fecha_nacimiento
```

### Respuestas

**200 - Éxito**
```json
{
  "success": true
}
```

**200 - Datos incompletos**
```json
{
  "success": false,
  "message": "Los datos JUAN,PÉREZ no están completos, deberían ser 5 datos"
}
```

**200 - Campos vacíos**
```json
{
  "success": false,
  "message": "Los datos de JUAN,PÉREZ,12345678,DNI,1990-05-15 tienen campos vacíos"
}
```

**200 - Fecha inválida**
```json
{
  "success": false,
  "message": "La fecha 15-05-1990 es incorrecta, el formato correcto es yyyy-mm-dd"
}
```

---

## 5. POST /v2/cuits

Valida CUITs de barrios y retorna la cobertura disponible.

### Request

```json
POST /api/v2/cuits
Content-Type: application/json

{
  "cuits": "20345678901,20345678902,san juan"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| cuits | string | Sí | CUITs o nombres de barrios separados por coma |

### Respuestas

**200 - Éxito con cobertura**
```json
{
  "success": true,
  "cobertura": "BASIC",
  "cobertura_info": "Cobertura BASIC, Suma : 500000 , Vr. Mensual : 1500",
  "cuits": "20345678901,20345678902",
  "message": "Consulta exitosa"
}
```

**200 - Algunos no encontrados**
```json
{
  "success": false,
  "cadenaNoEncontrados": "san juan",
  "message": "Algunos barrios no fueron encontrados"
}
```

**200 - Sin cobertura disponible**
```json
{
  "success": false,
  "message": "No hay una cobertura disponible para la suma muerte : $1.000.000"
}
```

---

## 6. POST /v2/proposal/create

Crea una nueva propuesta completa (cotización).

### Request

```json
POST /api/v2/proposal/create
Content-Type: application/json

{
  "tomador": "12345678",
  "agregar_tomador": "SI",
  "asegurados": "12345678,87654321",
  "cuits": "20345678901,20345678902",
  "meses": 3,
  "cobertura": "BASIC",
  "cod_actividad": "ACT001",
  "cod_clasificacion": "CL001",
  "codempresa": "BARRIOS",
  "master": "MA",
  "organizador": "OR001",
  "productor": "PR001",
  "fecha_desde": "2026-10-01",
  "lista_grupos_descartar": "",
  "tipo_pago": "DEBITO_AUTOMATICO",
  "nro_comprobante": "CBU-0123456789",
  "valor_pagado": 4500.00,
  "fecha_comprobante": "2026-10-01",
  "fecha_paga": "2026-10-01 10:00:00"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| tomador | string | Sí | Documento del tomador principal |
| agregar_tomador | string | Sí | "SI" o "NO" - incluir tomador como asegurado |
| asegurados | string | No | Documentos separados por coma |
| cuits | string | Sí | CUITs de barrios separados por coma |
| meses | int | Sí | Duración (1-6 meses) |
| cobertura | string | No | Nombre de cobertura (si vacío, auto-asigna) |
| cod_actividad | string | Sí | Código de actividad |
| cod_clasificacion | string | Sí | Código de clasificación |
| codempresa | string | Sí | Código de empresa |
| master | string | Sí | Código master |
| organizador | string | Sí | Código organizador |
| productor | string | Sí | Código productor |
| fecha_desde | string | No | Fecha inicio vigencia YYYY-MM-DD |
| lista_grupos_descartar | string | No | Grupos a excluir separados por coma |
| tipo_pago | string | No | Tipo de pago |
| nro_comprobante | string | No | Número de comprobante |
| valor_pagado | float | No | Valor pagado |
| fecha_comprobante | string | No | Fecha comprobante YYYY-MM-DD |
| fecha_paga | string | No | Fecha de pago YYYY-MM-DD HH:mm:ss |

### Respuestas

**200 - Éxito**
```json
{
  "success": true,
  "valorpropuesta": 4500.00,
  "vigencia_hasta": "01/01/2027"
}
```

**400 - Error de validación**
```json
{
  "success": false,
  "message": "El tomador no existe"
}
```

```json
{
  "success": false,
  "message": "El tomador debe tener datos completos"
}
```

```json
{
  "success": false,
  "message": "Los meses no están en el rango correcto"
}
```

```json
{
  "success": false,
  "message": "Algunos de los barrios no existen"
}
```

```json
{
  "success": false,
  "message": "La cobertura no es correcta"
}
```

```json
{
  "success": false,
  "message": "No hay asegurados"
}
```

**500 - Error interno**
```json
{
  "success": false,
  "message": "Error interno del servidor"
}
```

---

## 7. POST /v2/proposal/validate

Valida el valor total de una propuesta sin crearla.

### Request

```json
POST /api/v2/proposal/validate
Content-Type: application/json

{
  "tomador": "12345678",
  "agregar_tomador": "SI",
  "asegurados": "12345678,87654321",
  "cuits": "20345678901,20345678902",
  "meses": 3,
  "cobertura": "BASIC",
  "cod_actividad": "ACT001",
  "cod_clasificacion": "CL001",
  "codempresa": "BARRIOS",
  "master": "MA",
  "organizador": "OR001",
  "productor": "PR001",
  "fecha_desde": "2026-10-01",
  "lista_grupos_descartar": ""
}
```

> Misma estructura que `/v2/proposal/create` pero sin campos de pago.

### Respuestas

**200 - Éxito**
```json
{
  "success": true,
  "valorpropuesta": 4500.00
}
```

**400 - Error de validación** (mismos que `/v2/proposal/create`)

---

## 8. POST /v2/paypro

Realiza el pago de una propuesta existente. Registra el pago en `propuestas`, actualiza las `lineas_propuestas`, crea registro en `payregistries` y genera entrada en `Cola`. Todo dentro de una transacción.

### Request

```json
POST /api/v2/paypro
Content-Type: application/json

{
  "idpropuesta": 1521,
  "prefijopropuesta": "MA",
  "tipopago": "DEBITO_AUTOMATICO",
  "compformapago": "CBU-0123456789012345678901",
  "usuariopaga": "admin@barrios.com",
  "fecha_paga": "2026-09-15 14:30:00",
  "codempresa": "BARRIOS",
  "fecha_comprobante": "2026-09-15",
  "valor_pagado": 15750.50,
  "cuit_pagador": "20345678901",
  "comprobante_bitrix": "https://bitrix.barrrios.com/comprobantes/BTRX-001234.pdf",
  "banco_destino": "NACION"
}
```

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| idpropuesta | int | Sí | ID numérico de la propuesta |
| prefijopropuesta | string | Sí | Prefijo de la propuesta (max 5 chars) |
| tipopago | string | Sí | Tipo de pago: `DEBITO_AUTOMATICO`, `TARJETA_CREDITO`, `TARJETA_DEBITO`, `TRANSFERENCIA`, `EFECTIVO` (max 50 chars) |
| compformapago | string | Sí | Número de comprobante de pago (max 100 chars) |
| usuariopaga | string | Sí | Usuario o sistema que procesa el pago (max 100 chars) |
| fecha_paga | string | Sí | Fecha y hora del pago formato `YYYY-MM-DD HH:mm:ss` |
| codempresa | string | Sí | Código de empresa (max 50 chars) |
| fecha_comprobante | string | No | Fecha del comprobante formato `YYYY-MM-DD` |
| valor_pagado | float | Sí | Monto total pagado (min 0) |
| cuit_pagador | string | Sí | CUIT del pagador (numérico) |
| comprobante_bitrix | string | No | URL del comprobante Bitrix |
| banco_destino | string | No | Banco destino (max 100 chars) |

### Validaciones de negocio

1. La propuesta debe existir (idpropuesta + prefijo + codempresa)
2. `codestado` debe ser `1` (propuesta activa)
3. `paga` debe ser `0` (aún no pagada)
4. No debe existir registro previo en `payregistries` para esta propuesta

### Qué hace internamente

- Marca la propuesta como pagada (`paga=1`, `codestado=1`)
- Limpia el campo `csrf`
- Incrementa `version` en +1
- Actualiza `lineas_propuestas` con `codestado=1`
- Crea registro en `payregistries`
- Registra en `Cola` para sincronización

### Respuestas

**200 - Éxito**
```json
{
  "success": true,
  "message": "Se ha hecho el pago de la propuesta con éxito",
  "url_descarga": "https://tudominio.com/descargaseguro/1521/MA"
}
```

**404 - Propuesta no encontrada**
```json
{
  "res": "La propuesta no existe"
}
```

**400 - Error de negocio**
```json
{
  "res": "La propuesta se encuentra anulada"
}
```

```json
{
  "res": "La propuesta ya se encuentra pagada"
}
```

```json
{
  "res": "El pago de esta propuesta ya fue registrado"
}
```

```json
{
  "res": "No se pudo procesar el pago de la propuesta"
}
```

**422 - Error de validación**
```json
{
  "res": "Error de validación",
  "errors": {
    "idpropuesta": ["El campo idpropuesta es obligatorio."],
    "tipopago": ["El campo tipopago es obligatorio."],
    "compformapago": ["El campo compformapago es obligatorio."],
    "usuariopaga": ["El campo usuariopaga es obligatorio."],
    "fecha_paga": ["El campo fecha_paga no coincide con el formato Y-m-d H:i:s."],
    "codempresa": ["El campo codempresa es obligatorio."],
    "valor_pagado": ["El campo valor_pagado es obligatorio."],
    "cuit_pagador": ["El campo cuit_pagador es obligatorio."]
  }
}
```
