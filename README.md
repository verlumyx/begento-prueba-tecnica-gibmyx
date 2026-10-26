# API RESTful de Gestión de Productos y Divisas

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Tests](https://img.shields.io/badge/Tests-186%20Passed-10B981?style=flat&logo=checkmarx&logoColor=white)](#ejecución-de-pruebas-testing-sin-docker)
[![Swagger](https://img.shields.io/badge/OpenAPI-Swagger%203.0-85EA2D?style=flat&logo=swagger&logoColor=black)](#documentación-interactiva-swagger--openapi)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

API RESTful desarrollada en **Laravel 12** y **PHP 8.2+** para la gestión de productos, divisas y precios multimoneda, cumpliendo con los requerimientos técnicos y estándares de arquitectura limpia (*Clean Architecture*), desacoplamiento mediante el *Patrón Repositorio*, validaciones dedicadas con *Form Requests*, controladores de acción única (*Single Action Controllers*), excepciones personalizadas de dominio y cobertura completa de pruebas automatizadas (*Unit*, *Integration* y *Feature/E2E*).

---

## Tabla de Contenidos

1. [Requisitos Previos (Sin Docker)](#requisitos-previos-sin-docker)
2. [Cómo Clonar el Repositorio](#cómo-clonar-el-repositorio)
3. [Cómo Inicializar el Proyecto](#cómo-inicializar-el-proyecto)
   - [Inicio Rápido (Cheat Sheet)](#inicio-rápido-cheat-sheet)
   - [Paso a Paso Detallado](#paso-a-paso-detallado)
   - [Verificación de la Instalación](#verificación-de-la-instalación)
4. [Ejecución de Pruebas (Testing sin Docker)](#ejecución-de-pruebas-testing-sin-docker)
5. [Documentación Interactiva (Swagger / OpenAPI)](#documentación-interactiva-swagger--openapi)
6. [Catálogo de Endpoints de la API](#catálogo-de-endpoints-de-la-api)
   - [Health Check](#health-check)
   - [Divisas (Currencies)](#divisas-currencies)
   - [Productos (Products)](#productos-products)
   - [Precios de Productos (Product Prices)](#precios-de-productos-product-prices)
7. [Arquitectura y Estructura del Código](#arquitectura-y-estructura-del-código)
8. [Comandos de Utilidad](#comandos-de-utilidad)

---

## Requisitos Previos (Sin Docker)

Para inicializar y ejecutar este proyecto de forma estándar en tu máquina local, requieres contar con:

- **PHP** >= 8.2
  - Extensiones comunes de PHP activas:
    - `pdo`
    - `pdo_sqlite` (o el driver de BD que prefieras)
    - `mbstring`
    - `openssl`
    - `xml`
    - `curl`
    - `intl`
    - `bcmath`
    - `zip`
- **Composer** >= 2.x
- **SQLite 3** (base de datos predeterminada y sin configuración adicional)
- **Git**
- **Node.js** >= 18.x y **NPM** (opcional, únicamente si se desea compilar assets de frontend o Vite)

> 💡 Puedes verificar tus versiones instaladas ejecutando:
> ```bash
> php -v
> composer --version
> sqlite3 --version
> ```

---

## Cómo Clonar el Repositorio

Abre tu terminal y ejecuta uno de los siguientes comandos según tu preferencia de acceso:

### Opción A: Vía HTTPS (Recomendado)
```bash
git clone https://github.com/verlumyx/begento-prueba-tecnica-gibmyx.git
cd begento-prueba-tecnica-gibmyx
```

### Opción B: Vía SSH
```bash
git clone git@github.com:verlumyx/begento-prueba-tecnica-gibmyx.git
cd begento-prueba-tecnica-gibmyx
```

---

## Cómo Inicializar el Proyecto Normal (Sin Docker)

### Inicio Rápido (Cheat Sheet)

Si ya estás familiarizado con Laravel, puedes copiar y ejecutar este bloque de comandos para dejar el proyecto 100% listo y corriendo:

```bash
# 1. Clonar y entrar al directorio
git clone https://github.com/verlumyx/begento-prueba-tecnica-gibmyx.git
cd begento-prueba-tecnica-gibmyx

# 2. Instalar dependencias de PHP
composer install

# 3. Crear archivo de variables de entorno
cp .env.example .env

# 4. Generar la clave de encriptación de la aplicación
php artisan key:generate

# 5. Crear la base de datos SQLite local
touch database/database.sqlite

# 6. Ejecutar migraciones y poblar datos iniciales (Seeders)
php artisan migrate --seed

# 7. Iniciar el servidor local
php artisan serve
```

---

### Paso a Paso Detallado

A continuación se explica cada uno de los pasos para comprender lo que realiza el sistema:

#### Paso 1: Instalar las dependencias con Composer
Descarga todas las librerías requeridas por el framework Laravel 12 y las herramientas como L5-Swagger y PHPUnit:
```bash
composer install
```

#### Paso 2: Configurar las variables de entorno (`.env`)
Copia la plantilla de entorno base:
```bash
cp .env.example .env
```
> El archivo `.env.example` viene preconfigurado con SQLite:
> ```env
> DB_CONNECTION=sqlite
> ```
> No se necesita instalar ni configurar servidores pesados como MySQL o PostgreSQL.

#### Paso 3: Generar la clave única de la aplicación (`APP_KEY`)
Genera la clave criptográfica utilizada para cifrar sesiones y datos sensibles:
```bash
php artisan key:generate
```

#### Paso 4: Crear el archivo de base de datos SQLite
Crea el archivo físico donde SQLite almacenará la base de datos localmente:
```bash
touch database/database.sqlite
```

#### Paso 5: Ejecutar las migraciones y seeders
Crea las tablas de la base de datos (`currencies`, `products`, `product_prices`) y ejecuta los seeders para precargar las divisas base (como USD, EUR, etc.):
```bash
php artisan migrate --seed
```

*(Si en algún momento deseas reiniciar la base de datos desde cero, puedes ejecutar `php artisan migrate:fresh --seed`).*

#### Paso 6: (Opcional) Instalar dependencias de Frontend
Si deseas compilar la vista web o los recursos de Vite:
```bash
npm install
npm run build
```

#### Paso 7: Iniciar el servidor de desarrollo de Laravel
Ejecuta el servidor web embebido de PHP:
```bash
php artisan serve
```

Verás una salida similar a esta:
```
   INFO  Server running on [http://127.0.0.1:8000].

   Press Ctrl+C to stop the server
```

---

### Verificación de la Instalación

Una vez iniciado el servidor, abre otra terminal o tu navegador para validar que la API está respondiendo correctamente:

#### 1. Probar Endpoint de Estado (Health Check)
```bash
curl http://127.0.0.1:8000/api/status
```
Respuesta esperada:
```json
{
  "status": "ok",
  "message": "API is running"
}
```

#### 2. Abrir la Documentación Interactiva Swagger UI
Abre en tu navegador web:
👉 **[http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)**

---

## Ejecución de Pruebas (Testing sin Docker)

La suite de pruebas contiene **186 tests** y **758 aserciones** que cubren pruebas unitarias, de integración y funcionales End-to-End.

### Ejecutar todas las pruebas
Directamente con el comando de Laravel:
```bash
php artisan test
```

### Ejecutar pruebas con SQLite en memoria (Más rápido)
```bash
php artisan test --env=testing
```

### Ejecutar solo pruebas específicas
```bash
# Pruebas de productos
php artisan test --filter=Product

# Pruebas de divisas
php artisan test --filter=Currency

# Pruebas End-to-End (Feature)
php artisan test tests/Feature/

# Pruebas Unitarias
php artisan test tests/Unit/
```

---

## Documentación Interactiva (Swagger / OpenAPI)

Toda la API está documentada con atributos de **OpenAPI 3.0 / Swagger** integrados en cada controlador:

- **Swagger UI:** [http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)
- **Especificación JSON OpenAPI:** Ubicada en `storage/api-docs/api-docs.json`.

Si realizas cambios en las anotaciones o controladores, puedes regenerar la documentación con:
```bash
php artisan l5-swagger:generate
```

---

## Catálogo de Endpoints de la API

Todas las peticiones y respuestas manejan el encabezado `Accept: application/json` y formato JSON.

### Health Check
| Método | Endpoint | Descripción | Respuestas |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/status` | Verifica el estado del servicio | `200 OK` |

---

### Divisas (Currencies)

| Método | Endpoint | Descripción | Respuestas |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/currencies` | Lista todas las divisas registradas | `200 OK` |
| `POST` | `/api/currencies` | Registra una nueva divisa | `201 Created`, `422 Unprocessable` |
| `GET` | `/api/currencies/{id}` | Obtiene los detalles de una divisa por ID | `200 OK`, `404 Not Found` |
| `PUT` | `/api/currencies/{id}` | Actualiza datos de una divisa existente | `200 OK`, `404 Not Found`, `422 Unprocessable` |
| `DELETE` | `/api/currencies/{id}` | Elimina una divisa (eliminación en cascada) | `200 OK`, `404 Not Found` |

#### Payload de Ejemplo (POST / PUT Divisa):
```json
{
  "name": "British Pound",
  "symbol": "£",
  "exchange_rate": 0.7900
}
```

---

### Productos (Products)

| Método | Endpoint | Parámetros Query | Descripción | Respuestas |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/products` | `per_page` (int), `cursor` (string) | Lista productos con **Cursor Pagination** | `200 OK` |
| `POST` | `/api/products` | — | Crea un nuevo producto | `201 Created`, `422 Unprocessable` |
| `GET` | `/api/products/{id}` | — | Obtiene el detalle de un producto por ID | `200 OK`, `404 Not Found` |
| `PUT` | `/api/products/{id}` | — | Actualiza un producto existente | `200 OK`, `404 Not Found`, `422 Unprocessable` |
| `DELETE` | `/api/products/{id}` | — | Elimina un producto y sus precios | `200 OK`, `404 Not Found` |

#### Payload de Ejemplo (POST Producto):
```json
{
  "name": "Laptop Gamer ASUS ROG",
  "description": "Laptop de alto rendimiento para desarrollo",
  "price": 1899.99,
  "currency_id": 1,
  "tax_cost": 284.99,
  "manufacturing_cost": 950.00
}
```

#### Payload de Ejemplo (PUT Producto - Actualización Parcial):
```json
{
  "name": "Laptop Gamer ASUS ROG Strix",
  "price": 1799.99
}
```

---

### Precios de Productos (Product Prices)

Permite registrar precios específicos de un producto en diferentes divisas internacionales:

| Método | Endpoint | Descripción | Respuestas |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/products/{id}/prices` | Obtiene el listado de precios registrados para el producto `{id}` | `200 OK`, `404 Not Found` |
| `POST` | `/api/products/{id}/prices` | Registra un nuevo precio para el producto `{id}` en una divisa | `201 Created`, `404 Not Found`, `422 Unprocessable` |

#### Payload de Ejemplo (POST Precio de Producto):
```json
{
  "currency_id": 2,
  "price": 1655.99
}
```

---

### Manejo de Errores Uniforme

- **Recurso No Encontrado (`404 Not Found`):**
  Lanzado a través de excepciones de dominio personalizadas (`ProductNotFoundException`, `CurrencyNotFoundException`):
  ```json
  {
    "message": "Product with ID 999 not found"
  }
  ```

- **Error de Validación (`422 Unprocessable Content`):**
  Lanzado automáticamente por los *Form Requests*:
  ```json
  {
    "message": "The given data was invalid.",
    "errors": {
      "price": [
        "The price field must be greater than or equal to 0."
      ]
    }
  }
  ```

---

## Arquitectura y Estructura del Código

El diseño del proyecto aplica principios de **Clean Architecture**, **SOLID** y el **Patrón Repositorio**:

```
app/
├── Exceptions/
│   ├── CurrencyNotFoundException.php      # Excepción cuando una divisa no existe (404)
│   └── ProductNotFoundException.php       # Excepción cuando un producto no existe (404)
├── Http/
│   ├── Controllers/
│   │   ├── Currencies/                    # Controladores invocables para divisas
│   │   │   ├── CurrenciesGetController.php
│   │   │   ├── CurrencyGetController.php
│   │   │   ├── CurrencyPostController.php
│   │   │   ├── CurrencyPutController.php
│   │   │   └── CurrencyDeleteController.php
│   │   └── Products/                      # Controladores invocables para productos y precios
│   │       ├── ProductsGetController.php
│   │       ├── ProductGetController.php
│   │       ├── ProductPostController.php
│   │       ├── ProductPutController.php
│   │       ├── ProductDeleteController.php
│   │       ├── ProductPriceGetController.php
│   │       └── ProductPricePostController.php
│   └── Requests/                          # Form Requests para validar entradas
├── Models/                                # Modelos Eloquent
├── Repositories/
│   ├── Contracts/                         # Interfaces de repositorios (Inversión de dependencias)
│   └── *EloquentRepository.php            # Implementaciones con Eloquent
├── Services/                              # Lógica de negocio pura desacoplada
└── Providers/
    └── AppServiceProvider.php             # Binding de interfaces a repositorios en el contenedor IoC
```

---

## Comandos de Utilidad

```bash
# Ver todas las rutas de la API en la consola
php artisan route:list --path=api

# Limpiar caches de la aplicación
php artisan optimize:clear

# Consola interactiva (Tinker)
php artisan tinker

# Formatear el código con Laravel Pint
./vendor/bin/pint
```

---

## Licencia

Este proyecto está bajo la licencia [MIT](LICENSE).
