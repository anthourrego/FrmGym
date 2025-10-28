# KORPUS Training Club - Formulario de Registro

## 📋 Descripción

Formulario web profesional para el registro de nuevos miembros del gimnasio KORPUS Training Club. Diseñado con Bootstrap 5 y JavaScript vanilla, respetando la identidad visual de la marca.

## 🎨 Paleta de Colores

Basada en la identidad visual de KORPUS:

- **ACERO**: `#9CAFB7` (RGB 156, 175, 183) - Color principal de la marca
- **POTENCY**: `#121212` (RGB 18, 18, 18) - Color oscuro para contraste
- **WHITE**: `#FFFFFF` (RGB 255, 255, 255) - Color de fondo y texto

## 🚀 Características

### ✅ Validación Completa
- Validación en tiempo real de todos los campos
- Mensajes de error personalizados
- Validación de formato para email y WhatsApp
- Verificación de edad (16-80 años)
- Validación de documento (6-12 dígitos)

### 📱 Diseño Responsivo
- Adaptable a dispositivos móviles, tablets y desktop
- Interfaz optimizada para pantallas pequeñas
- Navegación por teclado mejorada

### 🎯 Campos del Formulario

1. **Datos Personales**
   - Nombre completo (obligatorio)
   - Número de documento (obligatorio, 6-12 dígitos)
   - Edad (obligatorio, 16-80 años)
   - Correo electrónico (obligatorio, formato válido)
   - Número de WhatsApp (obligatorio, formato +57 XXX XXX XXXX)

### 🔧 Funcionalidades Técnicas
- Formateo automático del número de WhatsApp
- Animaciones CSS suaves
- Modal de confirmación
- Almacenamiento local de registros (demo)
- Estados de carga visuales
- Notificaciones toast

## 📂 Estructura del Proyecto

```
FrmGym/
├── index.html          # Página principal
├── css/
│   └── styles.css      # Estilos personalizados
├── js/
│   └── script.js       # Funcionalidades JavaScript
└── README.md           # Documentación
```

## 🛠️ Tecnologías Utilizadas

- **HTML5**: Estructura semántica
- **CSS3**: Estilos personalizados con variables CSS
- **Bootstrap 5.3.2**: Framework CSS responsivo
- **JavaScript ES6+**: Funcionalidades interactivas
- **Font Awesome 6.4.0**: Iconografía

## 📦 Dependencias Externas

Todas las dependencias se cargan desde CDN:

- Bootstrap CSS y JS
- Font Awesome Icons

## 🚀 Instalación y Uso

1. **Clonar o descargar** los archivos del proyecto
2. **Abrir** `index.html` en un navegador web
3. **¡Listo!** El formulario está funcional

### Uso Local
```bash
# Navegar a la carpeta del proyecto
cd FrmGym

# Abrir con servidor local (opcional)
python -m http.server 8000
# o
npx serve .
```

## 💡 Personalización

### Cambiar Colores
Modificar las variables CSS en `css/styles.css`:

```css
:root {
    --korpus-acero: #9CAFB7;    /* Color principal */
    --korpus-potency: #121212;   /* Color oscuro */
    --korpus-white: #FFFFFF;     /* Color claro */
}
```

### Añadir Campos
1. Agregar el HTML del campo en `index.html`
2. Actualizar las validaciones en `js/script.js`
3. Añadir estilos si es necesario en `css/styles.css`

### Conectar con Backend
Modificar la función `processFormData()` en `js/script.js`:

```javascript
function processFormData(data) {
    fetch('/api/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log('Éxito:', result);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
```

## 🔍 Validaciones Implementadas

| Campo | Validación | Formato |
|-------|------------|---------|
| Nombre | Solo letras y espacios, 2-50 caracteres | Juan Pérez |
| Documento | Solo números, 6-12 dígitos | 1234567890 |
| Edad | Número entre 16-80 | 25 |
| Email | Formato de email válido | usuario@email.com |
| WhatsApp | Formato colombiano | XXX XXX XXXX |

## 🎨 Características de Diseño

- **Gradientes**: Uso de gradientes sutiles para dar profundidad
- **Animaciones**: Transiciones suaves y efectos hover
- **Sombras**: Sombras personalizadas para elevación
- **Tipografía**: Fuente Segoe UI para legibilidad
- **Iconografía**: Font Awesome para iconos consistentes

## 📱 Compatibilidad

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Dispositivos móviles (iOS/Android)

## 🤝 Contribución

Para contribuir al proyecto:

1. Fork el repositorio
2. Crear una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📧 Contacto

Para preguntas o soporte técnico relacionado con este formulario, contacta al equipo de desarrollo.

---

**KORPUS Training Club** - Formulario de Registro v1.0