## KORPUS Training Club - Sistema de Registro de Miembros

### Estructura del Proyecto CodeIgniter 4

```
korpus-gym/
├── app/
│   ├── Controllers/
│   │   └── Home.php                 # Controlador principal del formulario
│   ├── Models/
│   │   └── UserModel.php           # Modelo para gestión de usuarios
│   ├── Views/
│   │   └── registration_form.php   # Vista del formulario de registro
│   ├── Config/
│   │   ├── Database.php            # Configuración de base de datos
│   │   ├── Routes.php              # Definición de rutas
│   │   └── Routing.php             # Configuración de enrutamiento
│   └── Database/
│       └── Migrations/
│           └── 2025_10_28_000001_CreateGymRegistrationsTable.php
├── public/
│   ├── index.php                   # Punto de entrada principal
│   ├── assets/
│   │   ├── logo.webp              # Logo de KORPUS
│   │   └── fondo.png              # Imagen de fondo
│   ├── css/
│   │   └── styles.css             # Estilos personalizados
│   └── js/
│       └── script.js              # JavaScript del formulario
├── site.webmanifest               # Configuración PWA
├── package.json                   # Dependencias del proyecto
└── README.md                      # Documentación
```

### Instalación y Configuración

#### 1. Requisitos del Sistema
- PHP 8.1 o superior
- MySQL 5.7 o superior
- Composer
- Servidor web (Apache/Nginx)

#### 2. Instalación de CodeIgniter 4

```bash
# Instalar CodeIgniter 4 via Composer
composer create-project codeigniter4/appstarter korpus-gym

# O si ya tienes el proyecto, instalar dependencias
composer install
```

#### 3. Configuración de Base de Datos

1. Crear la base de datos:
```sql
CREATE DATABASE korpus_gym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Configurar la conexión en `app/Config/Database.php`:
```php
public array $default = [
    'hostname' => 'localhost',
    'username' => 'tu_usuario',
    'password' => 'tu_contraseña',
    'database' => 'korpus_gym',
    'DBDriver' => 'MySQLi',
    // ... resto de configuración
];
```

#### 4. Ejecutar Migraciones

```bash
# Ejecutar la migración para crear la tabla
php spark migrate
```

#### 5. Configuración del Servidor Web

**Apache (.htaccess)**:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [QSA,L]
```

**Nginx**:
```nginx
location / {
    try_files $uri $uri/ /index.php$is_args$args;
}
```

### Funcionalidades Implementadas

#### 1. Formulario de Registro (`app/Views/registration_form.php`)
- **Sección 1**: Información Personal
  - Nombre completo (requerido)
  - Número de documento (opcional)
  - Edad (15-100 años)
  - Email (único)
  - WhatsApp (formato colombiano)

- **Sección 2**: Objetivos de Entrenamiento
  - Objetivos predefinidos con opción "Otro"
  - Campo condicional para objetivo personalizado

- **Sección 3**: Horarios Preferidos
  - Mañana (6:00 AM - 12:00 PM)
  - Medio día (12:00 PM - 3:00 PM)
  - Tarde/Noche (3:00 PM - 10:00 PM)

- **Sección 4**: Fuente de Referencia
  - Redes sociales, referencias, publicidad
  - Campo condicional para "Otro medio"

- **Sección 5**: Consentimiento
  - Aceptación de uso de datos personales

#### 2. Controlador Principal (`app/Controllers/Home.php`)

**Métodos Principales**:
- `index()`: Muestra el formulario de registro
- `register()`: Procesa el envío del formulario

**Validaciones Implementadas**:
```php
'fullName' => 'required|min_length[2]|max_length[50]',
'age' => 'required|integer|greater_than_equal_to[15]|less_than_equal_to[100]',
'email' => 'required|valid_email|max_length[100]|is_unique[gym_registrations.email]',
'whatsapp' => 'required|regex_match[/^[0-9\s\-\+\(\)]{10,20}$/]',
'mainGoal' => 'required|in_list[bajar_peso,aumentar_masa,mejorar_salud,tonificar,otro]',
'preferredSchedule' => 'required|in_list[manana,medio_dia,tarde_noche]',
'howDidYouKnow' => 'required|in_list[redes_sociales,amigo_familiar,publicidad_local,otro]',
'consent' => 'required|in_list[si]'
```

**Validaciones Condicionales**:
- `otherGoal`: Requerido si `mainGoal` es "otro"
- `otherSource`: Requerido si `howDidYouKnow` es "otro"

#### 3. Modelo de Datos (`app/Models/UserModel.php`)

**Características**:
- Validación de datos a nivel de modelo
- Encriptación automática de documentos sensibles
- Métodos para análisis estadístico
- Timestamps automáticos

**Métodos Estadísticos**:
```php
getRegistrationsByGoal()      // Distribución por objetivos
getRegistrationsBySchedule()  // Distribución por horarios
getRegistrationsBySource()    // Fuentes de referencia
getRegistrationsByAge()       // Distribución etaria
getMonthlyRegistrations()     // Registros mensuales
getTotalRegistrations()       // Total de registros
```

#### 4. Base de Datos

**Tabla: `gym_registrations`**
- `id`: Clave primaria auto-incremental
- `full_name`: Nombre completo (VARCHAR 50)
- `document_number`: Número de documento (VARCHAR 12, opcional)
- `document_hash`: Hash SHA256 del documento para seguridad
- `age`: Edad (TINYINT, 15-100)
- `email`: Email único (VARCHAR 100)
- `whatsapp`: Número de WhatsApp (VARCHAR 20)
- `main_goal`: Objetivo principal (ENUM)
- `other_goal`: Objetivo personalizado (VARCHAR 100, condicional)
- `preferred_schedule`: Horario preferido (ENUM)
- `how_did_you_know`: Fuente de referencia (ENUM)
- `other_source`: Fuente personalizada (VARCHAR 100, condicional)
- `consent`: Consentimiento (ENUM)
- `ip_address`: IP del usuario (auditoría)
- `user_agent`: User agent (auditoría)
- `status`: Estado del registro (active, contacted, member, inactive)
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

**Índices**:
- Clave primaria en `id`
- Índice único en `email`
- Índices en `main_goal`, `preferred_schedule`, `created_at`, `status`

### Rutas Configuradas

```php
// Principales
GET  /              # Mostrar formulario
POST /              # Procesar registro

// Administración (futuras)
GET  /admin/dashboard
GET  /admin/registrations
GET  /admin/registrations/view/{id}
POST /admin/registrations/update-status/{id}
GET  /admin/statistics
GET  /admin/export

// API (futuras)
GET  /api/stats
GET  /api/registrations
POST /api/registrations

// Utilidades
GET  /health        # Health check
```

### Seguridad Implementada

1. **Validación de Datos**:
   - Validación del lado del servidor con CodeIgniter
   - Validación del lado del cliente con JavaScript
   - Sanitización automática de inputs

2. **Protección de Datos**:
   - Hash SHA256 para números de documento
   - Validación de email único
   - Registro de IP y User Agent para auditoría

3. **Prevención de Ataques**:
   - Protección CSRF (por defecto en CI4)
   - Validación de tipos de datos
   - Sanitización de salida con `esc()`

### Próximos Pasos

1. **Completar la Instalación**:
   ```bash
   # Instalar CodeIgniter 4
   composer create-project codeigniter4/appstarter .
   
   # Copiar archivos personalizados a la estructura CI4
   # Ejecutar migraciones
   php spark migrate
   ```

2. **Funcionalidades Futuras**:
   - Panel de administración para gestionar registros
   - Sistema de notificaciones por email/WhatsApp
   - Dashboard con estadísticas en tiempo real
   - Exportación de datos a Excel/PDF
   - API REST para integraciones
   - Sistema de seguimiento de leads

3. **Optimizaciones**:
   - Cache de consultas frecuentes
   - Compresión de imágenes
   - CDN para assets estáticos
   - Monitoreo de performance

### Comandos Útiles

```bash
# Crear nuevo controlador
php spark make:controller NombreController

# Crear nuevo modelo
php spark make:model NombreModel

# Crear nueva migración
php spark make:migration CreateTableName

# Ejecutar migraciones
php spark migrate

# Rollback migraciones
php spark migrate:rollback

# Ver rutas
php spark routes

# Iniciar servidor de desarrollo
php spark serve
```

Este sistema proporciona una base sólida para el registro de miembros del gimnasio KORPUS, con una arquitectura escalable y mantenible usando el framework CodeIgniter 4.