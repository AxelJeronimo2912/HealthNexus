# HealthNexus

HealthNexus es una aplicación web para la gestión integral de clínicas y hospitales. Permite administrar pacientes, citas, consultas médicas, expedientes, medicamentos, inventario, camas, signos vitales, servicios y usuarios del sistema.

El proyecto está desarrollado con Laravel y utiliza Blade para renderizar la interfaz web.

## Características principales

### Autenticación y usuarios

- Inicio de sesión y autenticación.
- Gestión de usuarios.
- Administración de roles y permisos.
- Control de acceso por módulo.
- Regeneración de PIN para usuarios.
- Asignación de turnos a usuarios.

### Gestión de pacientes

- Registro, consulta, edición y eliminación de pacientes.
- Consulta de información geográfica por estado y municipio.
- Acceso al expediente clínico de cada paciente.
- Seguimiento médico de pacientes.

### Citas y agenda

- Gestión de citas médicas.
- Agenda general y agenda diaria.
- Consulta de médicos disponibles.
- Consulta de pacientes disponibles.
- Cambio de estado de las citas.

### Consultas médicas

- Inicio y registro de consultas.
- Edición y consulta de consultas médicas.
- Generación de documentos PDF.
- Generación de recetas médicas en PDF.
- Asociación de consultas con citas y pacientes.

### Medicamentos e inventario

- Administración de medicamentos.
- Registro de entradas de medicamentos.
- Consulta de existencias.
- Gestión de lotes.
- Registro de movimientos de inventario.
- Dispensación de medicamentos.
- Reversión de dispensaciones.

### Camas y signos vitales

- Gestión de camas.
- Cambio de estado de las camas.
- Registro de signos vitales.
- Asignación de camas a registros clínicos.
- Liberación de camas.

### Servicios y personal

- Creación y administración de servicios médicos.
- Consulta y edición de servicios.
- Asignación de personal a cada servicio.
- Eliminación de personal asignado.

## Tecnologías utilizadas

### Backend

- PHP 8.3
- Laravel 13
- Laravel Breeze
- Laravel Tinker
- Spatie Laravel Permission
- Dompdf para generación de archivos PDF

### Frontend

- Blade
- Tailwind CSS
- Alpine.js
- Vite
- PostCSS
- Heroicons para Blade

### Pruebas

- PHPUnit
- Laravel Testing Framework

## Estructura principal

```text
HealthNexus/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Support/
│   └── View/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── admin/
│       ├── agenda/
│       ├── auth/
│       ├── camas/
│       ├── citas/
│       ├── consultas/
│       ├── dispensaciones/
│       ├── existencias/
│       ├── expedientes/
│       ├── layouts/
│       ├── medicamentos/
│       ├── movimientos/
│       ├── pacientes/
│       ├── seguimientos/
│       ├── servicios/
│       ├── signos-vitales/
│       └── turnos/
├── routes/
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
├── artisan
├── composer.json
├── package.json
└── vite.config.js
```

## Requisitos

Antes de instalar el proyecto, es necesario contar con:

- PHP 8.3 o superior
- Composer
- Node.js y npm
- Una base de datos compatible con Laravel
- Extensiones PHP requeridas por Laravel

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/AxelJeronimo2912/HealthNexus.git
cd HealthNexus
```

### 2. Instalar las dependencias de PHP

```bash
composer install
```

### 3. Crear el archivo de entorno

```bash
cp .env.example .env
```

En Windows puede utilizarse:

```bash
copy .env.example .env
```

### 4. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5. Configurar la base de datos

Editar el archivo `.env` y establecer los datos de conexión:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthnexus
DB_USERNAME=root
DB_PASSWORD=
```

También puede configurarse SQLite:

```env
DB_CONNECTION=sqlite
```

En ese caso, crear el archivo de base de datos:

```bash
touch database/database.sqlite
```

### 6. Ejecutar las migraciones

```bash
php artisan migrate
```

Si el proyecto cuenta con datos iniciales mediante seeders:

```bash
php artisan db:seed
```

O ejecutar ambos comandos:

```bash
php artisan migrate --seed
```

### 7. Instalar las dependencias frontend

```bash
npm install
```

### 8. Compilar los assets

Para producción:

```bash
npm run build
```

Para desarrollo:

```bash
npm run dev
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

La aplicación estará disponible normalmente en:

```text
http://127.0.0.1:8000
```

## Instalación rápida

El proyecto incluye un script de configuración en `composer.json`. Para ejecutarlo:

```bash
composer run setup
```

Este comando realiza las siguientes tareas:

1. Instala las dependencias de Composer.
2. Crea el archivo `.env` si no existe.
3. Genera la clave de la aplicación.
4. Ejecuta las migraciones.
5. Instala las dependencias de npm.
6. Compila los archivos frontend.

## Ejecución durante el desarrollo

Para ejecutar simultáneamente el servidor de Laravel y las herramientas de desarrollo frontend, puede utilizarse:

```bash
composer run dev
```

También es posible ejecutar cada proceso por separado:

```bash
php artisan serve
```

En otra terminal:

```bash
npm run dev
```

## Rutas principales

La aplicación utiliza rutas protegidas por autenticación, roles y permisos.

Algunos módulos disponibles son:

```text
/dashboard
/pacientes
/medicamentos
/camas
/signos-vitales
/agenda
/citas
/consultas
/expedientes
/existencias
/dispensaciones
/seguimientos
/movimientos
/servicios
/admin
```

El acceso a cada módulo está controlado mediante permisos como:

```text
pacientes.ver
medicamentos.ver
camas.ver
signos-vitales.ver
agenda.ver
citas.ver
consultas.ver
expediente.ver
existencias.ver
dispensaciones.ver
seguimiento.ver
movimientos.ver
servicios.ver
```

El rol de `administrador` tiene acceso a las funciones administrativas del sistema.

## Vistas

Las vistas se encuentran en:

```text
resources/views
```

Están organizadas por módulo y utilizan archivos Blade:

```text
*.blade.php
```

También existen layouts y componentes reutilizables para mantener una interfaz consistente:

```text
resources/views/layouts
resources/views/components
```

## Pruebas

Para ejecutar las pruebas automatizadas:

```bash
composer run test
```

También puede utilizarse directamente:

```bash
php artisan test
```

## Generación de PDFs

HealthNexus utiliza `barryvdh/laravel-dompdf` para generar:

- Consultas médicas en PDF.
- Recetas médicas en PDF.

Estas funcionalidades están disponibles desde el módulo de consultas.

## Seguridad

El sistema utiliza:

- Autenticación de Laravel.
- Middleware de autenticación.
- Middleware basado en roles.
- Middleware basado en permisos.
- Protección CSRF.
- Validación de solicitudes.
- Control de acceso por módulo.

No se deben subir al repositorio los archivos que contengan credenciales reales, especialmente:

```text
.env
```

## Estado del proyecto

HealthNexus se encuentra estructurado como una aplicación de gestión clínica basada en Laravel, con módulos administrativos, clínicos y de inventario.

## Licencia

El proyecto utiliza la licencia MIT, de acuerdo con la configuración base de Laravel incluida en el repositorio.
