/**
 * KORPUS Training Club - Formulario de Registro
 * Script principal para manejar la validación y envío del formulario
 */

// Clase personalizada para errores de validación
class ValidationError extends Error {
    constructor(data) {
        super(data.message || 'Error de validación');
        this.name = 'ValidationError';
        this.data = data;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Verificar si debe enfocar el campo nombre después de recarga
    if (sessionStorage.getItem('focusNameField') === 'true') {
        sessionStorage.removeItem('focusNameField');
        
        // Ir al top de la página
        if (sessionStorage.getItem('scrollToTop') === 'true') {
            sessionStorage.removeItem('scrollToTop');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Enfocar el campo nombre después de un pequeño delay
        setTimeout(() => {
            const nameField = document.getElementById('fullName');
            if (nameField) {
                nameField.focus();
                nameField.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }, 500);
    }
    
    // Elementos del DOM
    const form = document.getElementById('registrationForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    
    // Campos del formulario
    const fields = {
        fullName: document.getElementById('fullName'),
        documentNumber: document.getElementById('documentNumber'),
        age: document.getElementById('age'),
        email: document.getElementById('email'),
        whatsapp: document.getElementById('whatsapp'),
        otherGoal: document.getElementById('otherGoal'),
        otherSource: document.getElementById('otherSource')
    };

    // Configuración de validaciones personalizadas
    const validationRules = {
        fullName: {
            pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
            message: 'El nombre debe contener solo letras y espacios (2-50 caracteres)'
        },
        documentNumber: {
            pattern: /^[0-9]{6,12}$/,
            message: 'Si decides ingresarlo, debe tener entre 6 y 12 dígitos'
        },
        age: {
            min: 16,
            max: 80,
            message: 'La edad debe estar entre 16 y 80 años'
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Por favor ingresa un correo electrónico válido'
        },
        whatsapp: {
            pattern: /^[0-9]{3}\s?[0-9]{3}\s?[0-9]{4}$/,
            message: 'Formato válido: XXX XXX XXXX'
        }
    };

    // Inicialización
    init();

    function init() {
        setupEventListeners();
        setupValidationMessages();
        formatPhoneInput();
        setupConditionalFields();
        
        // Focus inicial en el primer campo - solo al cargar
        setTimeout(() => {
            if (fields.fullName) {
                fields.fullName.focus();
            }
        }, 100);
    }

    function setupEventListeners() {
        // Validación en tiempo real para todos los campos de texto (obligatorios y opcionales)
        const textFields = ['fullName', 'documentNumber', 'age', 'email', 'whatsapp'];
        textFields.forEach(fieldName => {
            if (fields[fieldName]) {
                fields[fieldName].addEventListener('input', () => validateField(fieldName));
                fields[fieldName].addEventListener('blur', () => validateField(fieldName));
            }
        });

        // Formateo automático del teléfono
        if (fields.whatsapp) {
            fields.whatsapp.addEventListener('input', formatWhatsAppNumber);
        }

        // Envío del formulario
        form.addEventListener('submit', handleSubmit);

        // Radio buttons para mostrar/ocultar campos condicionales
        const goalOtherRadio = document.getElementById('goalOther');
        const sourceOtherRadio = document.getElementById('knowOther');
        
        if (goalOtherRadio) {
            goalOtherRadio.addEventListener('change', toggleOtherGoalField);
        }
        
        if (sourceOtherRadio) {
            sourceOtherRadio.addEventListener('change', toggleOtherSourceField);
        }

        // Validación de radio buttons
        setupRadioValidation();
    }

    function setupValidationMessages() {
        // Configurar mensajes de validación HTML5
        Object.keys(fields).forEach(fieldName => {
            if (fields[fieldName] && validationRules[fieldName]) {
                const field = fields[fieldName];
                field.addEventListener('invalid', (e) => {
                    e.preventDefault();
                    showCustomValidationMessage(fieldName);
                });
            }
        });
    }

    function validateField(fieldName) {
        const field = fields[fieldName];
        const rule = validationRules[fieldName];
        
        if (!field || !rule) return true;

        let isValid = true;
        let message = '';

        // Limpiar clases previas
        field.classList.remove('is-valid', 'is-invalid');

        // Verificar si el campo está vacío
        if (field.hasAttribute('required') && !field.value.trim()) {
            isValid = false;
            message = 'Este campo es obligatorio';
        } else if (field.value.trim()) {
            // Validaciones específicas por tipo de campo
            switch (fieldName) {
                case 'fullName':
                    isValid = rule.pattern.test(field.value.trim());
                    message = rule.message;
                    break;
                    
                case 'documentNumber':
                    isValid = rule.pattern.test(field.value.trim());
                    message = rule.message;
                    break;
                    
                case 'age':
                    const age = parseInt(field.value);
                    isValid = age >= rule.min && age <= rule.max;
                    message = rule.message;
                    break;
                    
                case 'email':
                    isValid = rule.pattern.test(field.value.trim());
                    message = rule.message;
                    break;
                    
                case 'whatsapp':
                    isValid = rule.pattern.test(field.value.trim());
                    message = rule.message;
                    break;
            }
        }

        // Aplicar clases de validación
        if (field.value.trim()) {
            field.classList.add(isValid ? 'is-valid' : 'is-invalid');
        }

        // Mostrar mensaje personalizado
        updateValidationMessage(field, message, isValid);

        return isValid;
    }

    function updateValidationMessage(field, message, isValid) {
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback && !isValid && message) {
            feedback.textContent = message;
        }
    }

    function showCustomValidationMessage(fieldName) {
        const field = fields[fieldName];
        const rule = validationRules[fieldName];
        
        if (rule && rule.message) {
            field.setCustomValidity(rule.message);
        }
    }

    function formatWhatsAppNumber(e) {
        let value = e.target.value.replace(/\D/g, ''); // Solo números
        
        // Limitar a 10 dígitos
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        
        // Formatear como XXX XXX XXXX
        if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{0,4})/, '$1 $2 $3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{0,3})/, '$1 $2');
        }
        
        e.target.value = value.trim();
    }

    function formatPhoneInput() {
        // Permitir solo números y espacios en el campo de WhatsApp
        fields.whatsapp.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9\s]/.test(char)) {
                e.preventDefault();
            }
        });
    }

    function handleSubmit(e) {
        e.preventDefault();
        
        // Limpiar todas las validaciones previas
        clearAllValidations();
        
        // Activar validación visual de Bootstrap
        form.classList.add('was-validated');
        
        // Validar formulario completo
        const isFormValid = validateAllFields();
        
        if (isFormValid && form.checkValidity()) {
            submitForm();
        } else {
            // Mostrar errores y enfocar el primer campo inválido
            showValidationErrors();
        }
    }

    function clearAllValidations() {
        // Limpiar clases de validación de todos los inputs
        const allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
    }

    function validateAllFields() {
        let isFormValid = true;
        const validationResults = [];

        // 1. Validar campos de texto obligatorios
        const requiredTextFields = ['fullName', 'age', 'email', 'whatsapp'];
        requiredTextFields.forEach(fieldName => {
            const result = validateField(fieldName);
            validationResults.push({ field: fieldName, valid: result });
            if (!result) isFormValid = false;
        });

        // 1.1. Validar campo de documento (opcional)
        const documentResult = validateField('documentNumber');
        validationResults.push({ field: 'documentNumber', valid: documentResult });
        // No afecta isFormValid porque es opcional

        // 2. Validar radio groups obligatorios
        const requiredRadioGroups = ['mainGoal', 'preferredSchedule', 'howDidYouKnow', 'consent'];
        requiredRadioGroups.forEach(groupName => {
            const result = validateRadioGroup(groupName);
            validationResults.push({ field: groupName, valid: result });
            if (!result) isFormValid = false;
        });

        // 3. Validar campos condicionales
        const conditionalResults = validateConditionalFields();
        validationResults.push(...conditionalResults);
        conditionalResults.forEach(result => {
            if (!result.valid) isFormValid = false;
        });

        // Log para debugging en desarrollo
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('Resultados de validación:', validationResults);
        }

        return isFormValid;
    }

    function validateConditionalFields() {
        const results = [];
        
        // Validar "Otro objetivo" si está seleccionado
        const goalOtherRadio = document.getElementById('goalOther');
        if (goalOtherRadio && goalOtherRadio.checked) {
            const otherGoalInput = document.getElementById('otherGoal');
            const isValid = otherGoalInput.value.trim().length > 0;
            
            otherGoalInput.classList.remove('is-valid', 'is-invalid');
            otherGoalInput.classList.add(isValid ? 'is-valid' : 'is-invalid');
            
            results.push({ field: 'otherGoal', valid: isValid });
            
            if (!isValid) {
                showFieldError(otherGoalInput, 'Por favor especifica tu objetivo');
            }
        }

        // Validar "Otra fuente" si está seleccionado
        const sourceOtherRadio = document.getElementById('knowOther');
        if (sourceOtherRadio && sourceOtherRadio.checked) {
            const otherSourceInput = document.getElementById('otherSource');
            const isValid = otherSourceInput.value.trim().length > 0;
            
            otherSourceInput.classList.remove('is-valid', 'is-invalid');
            otherSourceInput.classList.add(isValid ? 'is-valid' : 'is-invalid');
            
            results.push({ field: 'otherSource', valid: isValid });
            
            if (!isValid) {
                showFieldError(otherSourceInput, 'Por favor especifica cómo te enteraste');
            }
        }

        return results;
    }

    function showFieldError(field, message, isRadioGroup = false) {
        let container = field;
        
        // Para grupos de radio buttons, usar el contenedor del grupo
        if (isRadioGroup || field.type === 'radio') {
            container = field.closest('.form-section') || field.closest('.mb-3') || field.parentNode;
        } else {
            container = field.parentNode;
        }
        
        // Buscar o crear el elemento de feedback
        let feedback = container.querySelector('.invalid-feedback');
        
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback server-error-message';
            feedback.style.display = 'block'; // Asegurar que se muestre
            
            // Para grupos de radio, insertar después del último radio del grupo
            if (isRadioGroup || field.type === 'radio') {
                const radioInputs = container.querySelectorAll('input[type="radio"]');
                const lastRadio = radioInputs[radioInputs.length - 1];
                if (lastRadio) {
                    lastRadio.parentNode.parentNode.appendChild(feedback);
                } else {
                    container.appendChild(feedback);
                }
            } else {
                // Para campos normales
                field.parentNode.appendChild(feedback);
            }
        }
        
        // Mostrar el mensaje con icono para errores del servidor
        feedback.innerHTML = `
            <i class="fas fa-exclamation-circle me-1"></i>
            ${message}
        `;
        
        // Agregar clase para identificar como error del servidor
        feedback.classList.add('server-error-message');
        
        // Animar la aparición del mensaje
        feedback.style.opacity = '0';
        feedback.style.transform = 'translateY(-5px)';
        
        setTimeout(() => {
            feedback.style.transition = 'all 0.3s ease';
            feedback.style.opacity = '1';
            feedback.style.transform = 'translateY(0)';
        }, 50);
    }

    function showValidationErrors() {
        // Mostrar notificación de errores
        showNotification('Por favor, completa todos los campos obligatorios correctamente', 'danger');
        
        // Enfocar el primer campo inválido
        const firstInvalidField = form.querySelector('.is-invalid');
        if (firstInvalidField) {
            firstInvalidField.focus();
            // Comentado para evitar scroll automático molesto
            // firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Resaltar temporalmente el campo
            firstInvalidField.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                firstInvalidField.style.animation = '';
            }, 500);
        }

        // Resaltar secciones con errores
        highlightErrorSections();
    }

    function highlightErrorSections() {
        // Limpiar resaltados previos
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('section-error');
        });

        // Resaltar secciones con errores
        document.querySelectorAll('.is-invalid').forEach(invalidField => {
            const section = invalidField.closest('.form-section');
            if (section) {
                section.classList.add('section-error');
            }
        });
    }

    function submitForm() {
        // Mostrar estado de carga
        setLoadingState(true);
        
        // Recopilar datos del formulario
        const formData = collectFormData();
        
        // Enviar datos al servidor
        submitToServer(formData);
    }

    function submitToServer(formData) {
        // Preparar datos para envío
        const submitData = new FormData();
        
        // Agregar token CSRF
        const csrfToken = document.querySelector('input[name="csrf_token_name"]');
        if (csrfToken) {
            submitData.append('csrf_token_name', csrfToken.value);
        }
        
        // Agregar datos del formulario
        Object.keys(formData).forEach(key => {
            if (formData[key] !== null && formData[key] !== '') {
                submitData.append(key, formData[key]);
            }
        });

        // Configurar timeout y controlador de aborto
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 segundos timeout

        // Realizar petición con fetch mejorado
        fetch(window.location.origin + '/register', {
            method: 'POST',
            body: submitData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            
            // Verificar el tipo de contenido
            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');
            
            if (!response.ok) {
                // Manejar diferentes códigos de error HTTP
                return response.text().then(text => {
                    let errorData = null;
                    let errorMessage = 'Error del servidor';
                    
                    if (isJson) {
                        try {
                            errorData = JSON.parse(text);
                            errorMessage = errorData.message || errorMessage;
                            
                            // Para errores de validación (422), pasar los datos completos
                            if (response.status === 422 && errorData.errors) {
                                throw new ValidationError(errorData);
                            }
                            
                        } catch (e) {
                            // Si es ValidationError, relanzar
                            if (e instanceof ValidationError) {
                                throw e;
                            }
                            // Si no se puede parsear JSON, usar mensaje por defecto
                        }
                    }
                    
                    switch (response.status) {
                        case 400:
                            errorMessage = 'Los datos enviados no son válidos';
                            break;
                        case 422:
                            errorMessage = 'Error de validación en el servidor';
                            break;
                        case 500:
                            errorMessage = 'Error interno del servidor. Por favor intenta más tarde';
                            break;
                        case 503:
                            errorMessage = 'Servicio temporalmente no disponible';
                            break;
                    }
                    
                    throw new Error(`${response.status}: ${errorMessage}`);
                });
            }
            
            if (!isJson) {
                throw new Error('Respuesta del servidor no válida');
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Registro exitoso
                handleSuccessResponse(data);
            } else {
                // Error en la respuesta del servidor
                handleErrorResponse(data);
            }
        })
        .catch(error => {
            // Manejar específicamente errores de validación
            if (error instanceof ValidationError) {
                handleErrorResponse(error.data);
            } else {
                handleNetworkError(error);
            }
        })
        .finally(() => {
            clearTimeout(timeoutId);
            setLoadingState(false);
        });
    }

    function handleSuccessResponse(data) {
        // Mostrar mensaje de éxito personalizado
        const message = data.message || '¡Registro exitoso! Te contactaremos pronto.';
        showSuccess(message);
        
        // Log información adicional si está disponible (solo en desarrollo)
        if (data.data && (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
            console.log('📊 Datos del registro:', {
                id: data.data.id,
                nombre: data.data.name,
                email: data.data.email,
                consentimiento: data.data.consent_given ? 'Sí' : 'No'
            });
        }
        
        // Opcional: Enviar evento personalizado para analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'registro_completado', {
                'custom_parameter': data.data?.consent_given || false
            });
        }
        
        // Guardar referencia local para debugging (solo en desarrollo)
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            localStorage.setItem('lastRegistration', JSON.stringify({
                timestamp: new Date().toISOString(),
                data: data.data
            }));
        }
    }

    function handleErrorResponse(data) {
        console.error('❌ Error en respuesta del servidor:', data);
        
        let errorMessage = data.message || 'Error al procesar el registro';
        let errorType = 'server';
        
        // Verificar si tiene la sección 'errors' específicamente
        if (data.errors && typeof data.errors === 'object') {
            // Procesar los errores independientemente del error_type
            const processedErrors = processValidationErrors(data.errors);
            
            if (processedErrors.length > 0) {
                errorType = 'validation';
                
                // Crear mensaje de error personalizado
                if (processedErrors.length === 1) {
                    errorMessage = processedErrors[0].message;
                } else {
                    errorMessage = `Se encontraron ${processedErrors.length} errores de validación:\n` + 
                                 processedErrors.map(error => `• ${error.field}: ${error.message}`).join('\n');
                }
                
                // Resaltar campos con errores específicos
                highlightServerValidationErrors(data.errors);
                
                // Mostrar notificación detallada para errores de validación
                showValidationErrorNotification(processedErrors);
                return;
            }
        }
        
        // Para otros tipos de errores, usar el método estándar
        showError(errorMessage, errorType);
    }

    function processValidationErrors(errors) {
        const processedErrors = [];
        
        // Verificar que errors sea un objeto válido
        if (!errors || typeof errors !== 'object') {
            return processedErrors;
        }
        
        // Procesar cada campo con errores
        Object.keys(errors).forEach(fieldName => {
            let fieldErrors = errors[fieldName];
            
            // Manejar diferentes formatos de errores
            if (typeof fieldErrors === 'string') {
                // Error único como string
                processedErrors.push({
                    field: getFieldDisplayName(fieldName),
                    fieldName: fieldName,
                    message: fieldErrors
                });
            } else if (Array.isArray(fieldErrors)) {
                // Array de errores
                fieldErrors.forEach((errorMsg) => {
                    if (errorMsg && typeof errorMsg === 'string') {
                        processedErrors.push({
                            field: getFieldDisplayName(fieldName),
                            fieldName: fieldName,
                            message: errorMsg
                        });
                    }
                });
            } else {
                // Intentar convertir a string
                const errorStr = String(fieldErrors);
                if (errorStr && errorStr !== 'undefined' && errorStr !== 'null') {
                    processedErrors.push({
                        field: getFieldDisplayName(fieldName),
                        fieldName: fieldName,
                        message: errorStr
                    });
                }
            }
        });
        
        return processedErrors;
    }

    function getFieldDisplayName(fieldName) {
        const displayNames = {
            'fullName': 'Nombre completo',
            'documentNumber': 'Número de documento',
            'age': 'Edad',
            'email': 'Correo electrónico',
            'whatsapp': 'WhatsApp',
            'mainGoal': 'Objetivo principal',
            'otherGoal': 'Objetivo personalizado',
            'preferredSchedule': 'Horario preferido',
            'howDidYouKnow': 'Cómo nos conociste',
            'otherSource': 'Fuente personalizada',
            'consent': 'Consentimiento'
        };
        
        return displayNames[fieldName] || fieldName;
    }

    function showValidationErrorNotification(processedErrors) {
        // Crear notificación personalizada para errores de validación
        let errorHtml = '';
        
        if (processedErrors.length === 1) {
            errorHtml = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Error de Validación</strong><br>
                        <small>${processedErrors[0].message}</small>
                    </div>
                </div>
            `;
        } else {
            errorHtml = `
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                    <div>
                        <strong>Errores de Validación (${processedErrors.length})</strong><br>
                        <small>Por favor corrige los siguientes campos:</small>
                        <ul class="mb-0 mt-1 ps-3">
                            ${processedErrors.map(error => 
                                `<li><strong>${error.field}:</strong> ${error.message}</li>`
                            ).join('')}
                        </ul>
                    </div>
                </div>
            `;
        }
        
        showNotification(errorHtml, 'warning', 10000); // 10 segundos para leer todos los errores
        
        // Vibrar si está disponible
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200]);
        }
        
        // Crear resumen visual si hay múltiples errores
        if (processedErrors.length > 1) {
            createErrorSummaryWidget(processedErrors);
        }
    }

    function createErrorSummaryWidget(errors) {
        // Remover widget previo si existe
        const existingWidget = document.getElementById('errorSummaryWidget');
        if (existingWidget) {
            existingWidget.remove();
        }
        
        // Crear widget de resumen de errores
        const widget = document.createElement('div');
        widget.id = 'errorSummaryWidget';
        widget.className = 'error-summary-widget';
        widget.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            max-width: 300px;
            z-index: 8888;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: slideInLeft 0.3s ease-out;
        `;
        
        widget.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 text-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${errors.length} Errores Encontrados
                </h6>
                <button type="button" class="btn-close btn-close-dark" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="error-list">
                ${errors.map((error, index) => `
                    <div class="error-item mb-1" style="font-size: 0.9rem;">
                        <strong>${index + 1}.</strong>
                        <span class="text-muted">${error.field}:</span>
                        <span>${error.message}</span>
                    </div>
                `).join('')}
            </div>
            <div class="mt-2">
                <small class="text-muted">Haz clic en los campos resaltados para corregir</small>
            </div>
        `;
        
        document.body.appendChild(widget);
        
        // Auto-remover después de 15 segundos
        setTimeout(() => {
            if (widget.parentNode) {
                widget.style.animation = 'slideOutLeft 0.3s ease-in';
                setTimeout(() => {
                    if (widget.parentNode) {
                        widget.remove();
                    }
                }, 300);
            }
        }, 15000);
    }

    function handleNetworkError(error) {
        console.error('🔌 Error de red/conexión:', error);
        
        let errorMessage = 'Error de conexión';
        
        if (error.name === 'AbortError') {
            errorMessage = 'La solicitud tardó demasiado tiempo. Por favor intenta nuevamente.';
        } else if (error.message.includes('NetworkError') || error.message.includes('fetch')) {
            errorMessage = 'Sin conexión a internet. Verifica tu conexión y vuelve a intentar.';
        } else if (error.message.includes('500')) {
            errorMessage = 'Error del servidor. Nuestro equipo ha sido notificado.';
        } else if (error.message.includes('400') || error.message.includes('422')) {
            errorMessage = 'Error en los datos enviados. Por favor revisa la información e intenta nuevamente.';
        }
        
        showError(errorMessage);
        
        // Opcional: Log para debugging
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('🐛 Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
        }
    }

    function highlightServerValidationErrors(errors) {
        // Mapeo de campos del servidor a campos del frontend
        const fieldMapping = {
            'fullName': 'fullName',
            'documentNumber': 'documentNumber', 
            'age': 'age',
            'email': 'email',
            'whatsapp': 'whatsapp',
            'mainGoal': 'mainGoal',
            'otherGoal': 'otherGoal',
            'preferredSchedule': 'preferredSchedule',
            'howDidYouKnow': 'howDidYouKnow',
            'otherSource': 'otherSource',
            'consent': 'consent'
        };
        
        let firstErrorField = null;
        let errorCount = 0;
        
        // Limpiar errores previos
        clearServerValidationErrors();
        
        Object.keys(errors).forEach(serverField => {
            const frontendField = fieldMapping[serverField];
            
            if (frontendField) {
                let field = fields[frontendField];
                
                // Para radio buttons, buscar el primer elemento del grupo
                if (!field) {
                    field = document.querySelector(`input[name="${frontendField}"]`);
                }
                
                if (field) {
                    let errorMessages = errors[serverField];
                    
                    // Asegurar que sea un array
                    if (!Array.isArray(errorMessages)) {
                        errorMessages = [errorMessages];
                    }
                    
                    // Tomar el primer mensaje de error para mostrar
                    const primaryError = errorMessages[0];
                    
                    if (primaryError && typeof primaryError === 'string') {
                        // Marcar campo como inválido
                        markFieldAsInvalid(field, frontendField, primaryError);
                        
                        // Guardar referencia al primer campo con error
                        if (!firstErrorField) {
                            firstErrorField = field;
                        }
                        
                        errorCount++;
                    }
                }
            }
        });
        
        // Enfocar y hacer scroll al primer campo con error
        if (firstErrorField) {
            setTimeout(() => {
                firstErrorField.focus();
                
                // Hacer scroll suave al campo
                const fieldContainer = firstErrorField.closest('.form-section') || firstErrorField.closest('.mb-3');
                if (fieldContainer) {
                    fieldContainer.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
                
                // Resaltar temporalmente el campo
                firstErrorField.style.animation = 'shake 0.5s ease-in-out';
                setTimeout(() => {
                    firstErrorField.style.animation = '';
                }, 500);
                
            }, 100);
        }
        
        // Resaltar secciones con errores
        highlightErrorSections();
    }

    function markFieldAsInvalid(field, fieldName, errorMessage) {
        // Para radio buttons, marcar solo el primer radio del grupo para mostrar el error
        if (field.type === 'radio') {
            const radioGroup = document.querySelectorAll(`input[name="${fieldName}"]`);
            const firstRadio = radioGroup[0];
            
            // Limpiar todas las validaciones previas del grupo
            radioGroup.forEach(radio => {
                radio.classList.remove('is-valid', 'is-invalid');
            });
            
            // Marcar solo el primer radio como inválido para mostrar el error
            if (firstRadio) {
                firstRadio.classList.add('is-invalid');
            }
            
            // Buscar el contenedor del grupo de radio buttons
            const radioContainer = field.closest('.form-section') || field.closest('.mb-3');
            if (radioContainer) {
                showFieldError(radioContainer, errorMessage, true);
            }
        } else {
            // Para campos normales
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            showFieldError(field, errorMessage);
        }
    }

    function clearServerValidationErrors() {
        // Limpiar todas las clases de validación previa
        const allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        // Limpiar mensajes de error previos
        const errorMessages = form.querySelectorAll('.invalid-feedback, .server-error-message');
        errorMessages.forEach(msg => {
            if (msg.classList.contains('server-error-message')) {
                msg.remove();
            } else {
                msg.textContent = '';
            }
        });
        
        // Limpiar resaltados de secciones
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('section-error');
        });
    }

    function collectFormData() {
        // Obtener valores de radio buttons
        const getRadioValue = (name) => {
            const radio = document.querySelector(`input[name="${name}"]:checked`);
            return radio ? radio.value : '';
        };

        return {
            fullName: fields.fullName.value.trim(),
            documentNumber: fields.documentNumber.value.trim() || null,
            age: parseInt(fields.age.value),
            email: fields.email.value.trim().toLowerCase(),
            whatsapp: fields.whatsapp.value.trim().replace(/\s/g, ''), // Solo números
            mainGoal: getRadioValue('mainGoal'),
            otherGoal: fields.otherGoal ? fields.otherGoal.value.trim() : null,
            preferredSchedule: getRadioValue('preferredSchedule'),
            howDidYouKnow: getRadioValue('howDidYouKnow'),
            otherSource: fields.otherSource ? fields.otherSource.value.trim() : null,
            consent: getRadioValue('consent')
        };
    }

    function setLoadingState(loading) {
        const loadingText = [
            'Procesando registro...',
            'Validando información...',
            'Guardando datos...',
            'Finalizando...'
        ];
        
        if (loading) {
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            
            // Animar el texto de carga
            let currentIndex = 0;
            const updateLoadingText = () => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText[currentIndex]}`;
                    currentIndex = (currentIndex + 1) % loadingText.length;
                }
            };
            
            updateLoadingText();
            window.loadingInterval = setInterval(updateLoadingText, 1500);
            
            // Deshabilitar todo el formulario durante el envío
            form.classList.add('loading');
            const formElements = form.querySelectorAll('input, select, textarea, button');
            formElements.forEach(element => {
                element.disabled = true;
            });
            
            // Mostrar indicador de progreso
            showProgressIndicator();
            
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Registrarme';
            
            // Limpiar intervalo de texto
            if (window.loadingInterval) {
                clearInterval(window.loadingInterval);
                window.loadingInterval = null;
            }
            
            // Rehabilitar formulario
            form.classList.remove('loading');
            const formElements = form.querySelectorAll('input, select, textarea, button');
            formElements.forEach(element => {
                element.disabled = false;
            });
            
            // Ocultar indicador de progreso
            hideProgressIndicator();
        }
    }

    function showProgressIndicator() {
        // Crear barra de progreso si no existe
        let progressBar = document.getElementById('submitProgress');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.id = 'submitProgress';
            progressBar.className = 'progress position-fixed';
            progressBar.style.cssText = `
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                z-index: 9999;
                border-radius: 0;
            `;
            progressBar.innerHTML = `
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                     role="progressbar" style="width: 0%"></div>
            `;
            document.body.appendChild(progressBar);
        }
        
        // Animar progreso
        const bar = progressBar.querySelector('.progress-bar');
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90; // No llegar al 100% hasta completar
            bar.style.width = progress + '%';
        }, 200);
        
        // Guardar referencia para limpiar después
        progressBar.progressInterval = progressInterval;
    }

    function hideProgressIndicator() {
        const progressBar = document.getElementById('submitProgress');
        if (progressBar) {
            // Completar barra
            const bar = progressBar.querySelector('.progress-bar');
            bar.style.width = '100%';
            
            // Limpiar intervalo
            if (progressBar.progressInterval) {
                clearInterval(progressBar.progressInterval);
            }
            
            // Remover después de animación
            setTimeout(() => {
                if (progressBar.parentNode) {
                    progressBar.remove();
                }
            }, 500);
        }
    }

    function showSuccess(message = '¡Registro exitoso! Te contactaremos pronto.') {
        // Mostrar notificación de éxito con duración extendida
        showNotification(`
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <div>
                    <strong>¡Éxito!</strong><br>
                    <small>${message}</small>
                    <br><small class="text-muted">La página se recargará en unos segundos...</small>
                </div>
            </div>
        `, 'success', 7000);
        
        // Animar la tarjeta con efecto de éxito
        const registrationCard = document.querySelector('.registration-card');
        if (registrationCard) {
            registrationCard.classList.add('success-animation');
            
            // Agregar efecto de confetti si está disponible
            createSuccessAnimation();
        }
        
        // Mostrar modal de éxito si existe
        const successModal = document.getElementById('successModal');
        if (successModal) {
            const modal = new bootstrap.Modal(successModal);
            modal.show();
            
            // Agregar botón de "Continuar" al modal que recargue la página
            const modalFooter = successModal.querySelector('.modal-footer');
            if (modalFooter) {
                const continueBtn = modalFooter.querySelector('.btn');
                if (continueBtn) {
                    continueBtn.addEventListener('click', () => {
                        sessionStorage.setItem('focusNameField', 'true');
                        sessionStorage.setItem('scrollToTop', 'true');
                        window.location.reload();
                    });
                }
            }
        }
        
        // Vibrar dispositivo para feedback háptico (móviles)
        if (navigator.vibrate) {
            navigator.vibrate([100, 50, 100, 50, 100]);
        }
        
        // Reproducir sonido de éxito si está disponible
        playSuccessSound();
        
        // Limpiar formulario después de un breve delay
        setTimeout(() => {
            handleReset();
            
            // Remover animación de éxito
            if (registrationCard) {
                registrationCard.classList.remove('success-animation');
            }
        }, 3000);
        
        // Mostrar contador regresivo y recargar página automáticamente
        showReloadCountdown();
        
        // Log del éxito (solo en desarrollo)
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('✅ Registro completado exitosamente - Recargando página en 5 segundos');
        }
    }

    function createSuccessAnimation() {
        // Crear efecto de partículas de éxito simple
        const particles = document.createElement('div');
        particles.className = 'success-particles';
        particles.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 8888;
        `;
        
        // Crear múltiples partículas
        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.innerHTML = Math.random() > 0.5 ? '✨' : '🎉';
            particle.style.cssText = `
                position: absolute;
                font-size: ${Math.random() * 20 + 15}px;
                left: ${Math.random() * 100}%;
                top: -50px;
                animation: fall ${Math.random() * 3 + 2}s linear forwards;
                opacity: 0.8;
            `;
            particles.appendChild(particle);
        }
        
        document.body.appendChild(particles);
        
        // Remover después de la animación
        setTimeout(() => {
            if (particles.parentNode) {
                particles.remove();
            }
        }, 5000);
    }

    function showReloadCountdown() {
        // Crear widget de cuenta regresiva
        const countdownWidget = document.createElement('div');
        countdownWidget.id = 'reloadCountdown';
        countdownWidget.className = 'reload-countdown-widget';
        countdownWidget.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            border-radius: 12px;
            padding: 20px;
            max-width: 280px;
            z-index: 9999;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
            animation: slideInRight 0.5s ease-out;
            text-align: center;
        `;
        
        let countdown = 5; // 5 segundos
        
        const updateCountdown = () => {
            countdownWidget.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <i class="fas fa-sync-alt fa-2x text-success mb-2" style="animation: rotate 2s linear infinite;"></i>
                    <h6 class="mb-2 text-success">¡Registro Completado!</h6>
                    <p class="mb-2">La página se recargará en:</p>
                    <div class="countdown-number" style="
                        font-size: 2rem;
                        font-weight: bold;
                        color: #28a745;
                        background: white;
                        border-radius: 50%;
                        width: 60px;
                        height: 60px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 10px;
                        border: 3px solid #28a745;
                    ">${countdown}</div>
                    <button class="btn btn-sm btn-success" onclick="
                        sessionStorage.setItem('focusNameField', 'true');
                        sessionStorage.setItem('scrollToTop', 'true');
                        window.location.reload();
                    ">
                        <i class="fas fa-redo me-1"></i>Recargar Ahora
                    </button>
                </div>
            `;
        };
        
        // Mostrar widget inicial
        updateCountdown();
        document.body.appendChild(countdownWidget);
        
        // Contador regresivo
        const countdownInterval = setInterval(() => {
            countdown--;
            
            if (countdown > 0) {
                updateCountdown();
            } else {
                clearInterval(countdownInterval);
                
                // Mostrar mensaje final antes de recargar
                countdownWidget.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x text-success mb-2"></i>
                        <p class="mb-0 text-success"><strong>Recargando página...</strong></p>
                    </div>
                `;
                
                // Recargar la página con scroll al top y enfoque en nombre
                setTimeout(() => {
                    // Almacenar en sessionStorage que debe enfocar el campo nombre
                    sessionStorage.setItem('focusNameField', 'true');
                    sessionStorage.setItem('scrollToTop', 'true');
                    window.location.reload();
                }, 1000);
            }
        }, 1000);
        
        // Permitir cancelar la recarga
        document.addEventListener('keydown', function cancelReload(e) {
            if (e.key === 'Escape') {
                clearInterval(countdownInterval);
                if (countdownWidget.parentNode) {
                    countdownWidget.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => {
                        if (countdownWidget.parentNode) {
                            countdownWidget.remove();
                        }
                    }, 300);
                }
                document.removeEventListener('keydown', cancelReload);
            }
        });
    }

    function playSuccessSound() {
        // Reproducir sonido de éxito si está disponible
        try {
            // Crear contexto de audio web
            if (window.AudioContext || window.webkitAudioContext) {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                
                // Crear un tono de éxito simple
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
                oscillator.frequency.setValueAtTime(1108, audioContext.currentTime + 0.1); // C#6
                
                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.3);
            }
        } catch (error) {
            // Silenciar errores de audio
            console.log('Audio no disponible');
        }
    }

    function showError(message = 'Error al procesar el registro. Por favor intenta nuevamente.', errorType = 'general') {
        // Mostrar notificación de error con diferentes estilos según el tipo
        const errorConfig = {
            validation: {
                type: 'warning',
                icon: 'fas fa-exclamation-triangle',
                title: 'Error de Validación'
            },
            network: {
                type: 'danger',
                icon: 'fas fa-wifi',
                title: 'Error de Conexión'
            },
            server: {
                type: 'danger', 
                icon: 'fas fa-server',
                title: 'Error del Servidor'
            },
            general: {
                type: 'danger',
                icon: 'fas fa-times-circle',
                title: 'Error'
            }
        };

        const config = errorConfig[errorType] || errorConfig.general;
        
        // Mostrar notificación mejorada con título e icono
        showNotification(`
            <div class="d-flex align-items-center">
                <i class="${config.icon} me-2"></i>
                <div>
                    <strong>${config.title}</strong><br>
                    <small>${message}</small>
                </div>
            </div>
        `, config.type, 8000); // 8 segundos para errores
        
        // Remover clases de validación para permitir nuevo intento
        form.classList.remove('was-validated');
        
        // Log del error para debugging
        console.error(`🔴 ${config.title}:`, message);
        
        // Vibrar dispositivo si está disponible (móviles)
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200]);
        }
        
        // Mostrar opción de reintentar para errores de red
        if (errorType === 'network') {
            showRetryOption();
        }
    }

    function showRetryOption() {
        // Crear botón de reintentar si no existe
        let retryButton = document.getElementById('retryButton');
        if (!retryButton) {
            retryButton = document.createElement('button');
            retryButton.id = 'retryButton';
            retryButton.className = 'btn btn-outline-primary btn-sm mt-2';
            retryButton.innerHTML = '<i class="fas fa-redo me-1"></i>Reintentar';
            retryButton.onclick = () => {
                retryButton.remove();
                handleSubmit(new Event('submit'));
            };
            
            // Insertar después del botón de envío
            submitBtn.parentNode.insertBefore(retryButton, submitBtn.nextSibling);
            
            // Auto-remover después de 10 segundos
            setTimeout(() => {
                if (retryButton.parentNode) {
                    retryButton.remove();
                }
            }, 10000);
        }
    }

    function showNotification(message, type = 'info', duration = 5000) {
        // Crear o actualizar notificación
        let notification = document.getElementById('notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'notification';
            notification.className = 'alert alert-dismissible fade show position-fixed';
            notification.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 320px;
                max-width: 500px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: none;
                border-radius: 8px;
            `;
            document.body.appendChild(notification);
        }
        
        // Configurar el tipo de alerta con mejores estilos
        const alertTypes = {
            success: 'alert-success',
            danger: 'alert-danger', 
            warning: 'alert-warning',
            info: 'alert-info'
        };
        
        notification.className = `alert ${alertTypes[type] || alertTypes.info} alert-dismissible fade show position-fixed`;
        
        // Configurar el contenido con mejor estructura
        notification.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    ${message}
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        `;
        
        // Agregar animación de entrada
        notification.style.transform = 'translateX(100%)';
        notification.style.transition = 'transform 0.3s ease-out';
        
        // Animar entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto-cerrar con duración personalizada
        const autoCloseTimer = setTimeout(() => {
            if (notification && notification.parentNode) {
                // Animar salida
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, duration);
        
        // Limpiar timer si se cierra manualmente
        const closeButton = notification.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                clearTimeout(autoCloseTimer);
            });
        }
        
        // Pausar auto-close al hacer hover (para errores importantes)
        if (type === 'danger' || type === 'warning') {
            notification.addEventListener('mouseenter', () => {
                clearTimeout(autoCloseTimer);
            });
            
            notification.addEventListener('mouseleave', () => {
                setTimeout(() => {
                    if (notification && notification.parentNode) {
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 300);
                    }
                }, 2000); // 2 segundos adicionales después del hover
            });
        }
    }

    function handleReset() {
        // Limpiar clases de validación
        form.classList.remove('was-validated');
        
        // Limpiar clases de campos individuales
        const allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        // Reset del formulario
        form.reset();

        // Ocultar campos condicionales
        const otherGoalContainer = document.getElementById('otherGoalContainer');
        const otherSourceContainer = document.getElementById('otherSourceContainer');
        
        if (otherGoalContainer) {
            otherGoalContainer.style.display = 'none';
            otherGoalContainer.classList.remove('fade-in');
        }
        
        if (otherSourceContainer) {
            otherSourceContainer.style.display = 'none';
            otherSourceContainer.classList.remove('fade-in');
        }

        // Remover atributos required de campos condicionales
        const otherGoalInput = document.getElementById('otherGoal');
        const otherSourceInput = document.getElementById('otherSource');
        
        if (otherGoalInput) {
            otherGoalInput.removeAttribute('required');
        }
        
        if (otherSourceInput) {
            otherSourceInput.removeAttribute('required');
        }
        
        // No hacer focus automático después del reset
    }

    // Funciones para campos condicionales
    function setupConditionalFields() {
        // Configurar evento para objetivo "Otro"
        const goalRadios = document.querySelectorAll('input[name="mainGoal"]');
        goalRadios.forEach(radio => {
            radio.addEventListener('change', toggleOtherGoalField);
        });

        // Configurar evento para fuente "Otro"
        const sourceRadios = document.querySelectorAll('input[name="howDidYouKnow"]');
        sourceRadios.forEach(radio => {
            radio.addEventListener('change', toggleOtherSourceField);
        });
    }

    function toggleOtherGoalField() {
        const otherGoalContainer = document.getElementById('otherGoalContainer');
        const otherGoalInput = document.getElementById('otherGoal');
        const goalOtherRadio = document.getElementById('goalOther');

        if (goalOtherRadio && goalOtherRadio.checked) {
            otherGoalContainer.style.display = 'block';
            otherGoalContainer.classList.add('fade-in');
            otherGoalInput.setAttribute('required', '');
            setTimeout(() => otherGoalInput.focus(), 300);
        } else {
            otherGoalContainer.style.display = 'none';
            otherGoalContainer.classList.remove('fade-in');
            otherGoalInput.removeAttribute('required');
            otherGoalInput.value = '';
        }
    }

    function toggleOtherSourceField() {
        const otherSourceContainer = document.getElementById('otherSourceContainer');
        const otherSourceInput = document.getElementById('otherSource');
        const sourceOtherRadio = document.getElementById('knowOther');

        if (sourceOtherRadio && sourceOtherRadio.checked) {
            otherSourceContainer.style.display = 'block';
            otherSourceContainer.classList.add('fade-in');
            otherSourceInput.setAttribute('required', '');
            setTimeout(() => otherSourceInput.focus(), 300);
        } else {
            otherSourceContainer.style.display = 'none';
            otherSourceContainer.classList.remove('fade-in');
            otherSourceInput.removeAttribute('required');
            otherSourceInput.value = '';
        }
    }

    function setupRadioValidation() {
        // Validación para objetivo principal
        const goalRadios = document.querySelectorAll('input[name="mainGoal"]');
        goalRadios.forEach(radio => {
            radio.addEventListener('change', () => validateRadioGroup('mainGoal'));
        });

        // Validación para horario preferido
        const scheduleRadios = document.querySelectorAll('input[name="preferredSchedule"]');
        scheduleRadios.forEach(radio => {
            radio.addEventListener('change', () => validateRadioGroup('preferredSchedule'));
        });

        // Validación para cómo se enteró
        const sourceRadios = document.querySelectorAll('input[name="howDidYouKnow"]');
        sourceRadios.forEach(radio => {
            radio.addEventListener('change', () => validateRadioGroup('howDidYouKnow'));
        });

        // Validación para consentimiento
        const consentRadios = document.querySelectorAll('input[name="consent"]');
        consentRadios.forEach(radio => {
            radio.addEventListener('change', () => validateRadioGroup('consent'));
        });
    }

    function validateRadioGroup(groupName) {
        const radios = document.querySelectorAll(`input[name="${groupName}"]`);
        const checked = document.querySelector(`input[name="${groupName}"]:checked`);
        
        // Limpiar clases previas de todos los radios
        radios.forEach(radio => {
            radio.classList.remove('is-valid', 'is-invalid');
        });

        if (checked) {
            // Marcar solo el radio seleccionado como válido
            checked.classList.add('is-valid');
            return true;
        } else {
            // Marcar el primer radio como inválido para mostrar el error
            const firstRadio = radios[0];
            if (firstRadio) {
                firstRadio.classList.add('is-invalid');
            }
            return false;
        }
    }

    // Exponer funciones útiles al scope global para debugging
    window.KorpusForm = {
        validateAllFields,
        collectFormData,
        showNotification: showNotification
    };
});

// Mejorar accesibilidad con navegación por teclado
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
        const form = e.target.closest('form');
        if (!form) return;
        
        const inputs = Array.from(form.querySelectorAll('input, select, textarea'));
        const index = inputs.indexOf(e.target);
        
        if (index > -1 && index < inputs.length - 1) {
            e.preventDefault();
            inputs[index + 1].focus();
        }
    }
});