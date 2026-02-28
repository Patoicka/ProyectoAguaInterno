# Sistema de Reporte de Incidencias — Conagua

Aplicación web interna para la **Comisión Nacional del Agua (Conagua)** que permite a la ciudadanía reportar incidencias relacionadas con el agua en su comunidad, y al personal autorizado gestionarlas, asignarlas y dar seguimiento.

## ¿Qué hace este proyecto?

- **Portal público (sin login):**
  - Página de bienvenida e información.
  - Formulario para **crear una incidencia**: tipo de incidencia, descripción, datos del reportador (nombre, teléfono, correo), ubicación (calle, colonia, ciudad, estado) y hasta 5 imágenes.
  - Búsqueda de incidencias por **código único** (ej. `XXYYZZ24-1`).
  - Visualización del detalle y estado de una incidencia.

- **Área interna (con login):**
  - **Dashboard** para usuarios autenticados.
  - **Gestión de incidencias:** listado, asignación a revisores, carga de evidencias.
  - **Tipos de incidencia** (CRUD).
  - **Mapa de incidencias** y **gráficas** (visualizaciones).
  - **Seguridad:** módulos, permisos y roles (Spatie), usuarios.
  - **Perfil** de usuario (Laravel Breeze).

- **Automático:**
  - Al registrar una incidencia se genera un **código único** por ubicación y año.
  - Se envían **emails** a administradores (nueva incidencia) y al reportador (confirmación con código).

## Stack técnico

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Vue 3, Inertia.js, Vite, Tailwind CSS, Flowbite
- **Autenticación:** Laravel Breeze, Sanctum
- **Permisos:** Spatie Laravel Permission
- **Exportación:** Maatwebsite Excel, Spatie Simple Excel
- **Mapas:** Leaflet
- **Gráficas:** Chart.js, Plotly

## Requisitos

- PHP 8.2+
- Composer
- Node.js y npm
- Base de datos (MySQL/MariaDB recomendado)
- Servidor SMTP o configuración de mail para envío de correos

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd ProyectoAguaInterno
   ```

2. **Variables de entorno**
   - Copiar el archivo de ejemplo: `cp .env.example .env`
   - Editar `.env` y configurar al menos:
     - `APP_NAME`, `APP_KEY` (generar con `php artisan key:generate`)
     - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
     - `MAIL_*` si vas a usar envío de correos

3. **Dependencias PHP**
   ```bash
   composer install
   ```

4. **Dependencias frontend**
   ```bash
   npm install
   ```

5. **Base de datos**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Ejecutar en desarrollo**
   ```bash
   npm run dev
   ```
   En otra terminal (opcional, para colas y correos):
   ```bash
   php artisan serve
   ```

Para producción: compilar assets con `npm run build` y configurar el servidor web (Apache/Nginx) y la cola de Laravel según tu entorno.

## Variables de entorno sensibles

Todas las claves y secretos deben configurarse en `.env`; **nunca** subas el archivo `.env` al repositorio. El archivo `.env.example` lista las variables necesarias sin valores reales. En particular:

- `APP_KEY`: clave de cifrado de Laravel.
- `DB_*`: credenciales de base de datos.
- `MAIL_*`, `MAILGUN_*`, `POSTMARK_*`: correo.
- `AWS_*`, `PUSHER_*`, `REDIS_*`, etc.: según los servicios que uses.
- Si se usa **reCAPTCHA** en el formulario público: `RECAPTCHA_SITE_KEY` (frontend) y `RECAPTCHA_SECRET_KEY` (backend).

## Estructura breve

- `app/Http/Controllers/`: `WelcomeController` (público), `IncidentController`, `IncidentTypeController`, seguridad (User, Role, Permission, Module).
- `app/Services/IncidentService.php`: lógica de código único, archivos y envío de emails.
- `resources/js/Pages/Welcome/`: vistas públicas (Home, Incident/Create, Search, Show).
- `resources/js/Pages/`: Dashboard, Incident, IncidentType, Security, Visualizations (mapa, gráficas).

## Licencia

Proyecto interno. Consultar con la organización para uso y distribución.
