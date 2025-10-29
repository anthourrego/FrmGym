/**
 * KORPUS Training Club - Formulario de Registro
 * Script principal para manejar la validación y envío del formulario
 */

document.addEventListener('DOMContentLoaded', function() {
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

    function validateForm() {
        // Esta función se mantiene para compatibilidad, pero ahora usa validateAllFields
        return validateAllFields();
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

        // Log para debugging
        console.log('Resultados de validación:', validationResults);

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

    function showFieldError(field, message) {
        // Buscar o crear el elemento de feedback
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
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
        
        // Simular envío del formulario (aquí conectarías con tu backend)
        setTimeout(() => {
            // Recopilar datos del formulario
            const formData = collectFormData();
            
            // Procesar datos (aquí harías la llamada a tu API)
            processFormData(formData);
            
            // Mostrar éxito
            showSuccess();
            
            // Quitar estado de carga
            setLoadingState(false);
        }, 1500);
    }

    function collectFormData() {
        // Obtener valores de radio buttons
        const getRadioValue = (name) => {
            const radio = document.querySelector(`input[name="${name}"]:checked`);
            return radio ? radio.value : '';
        };

        return {
            fullName: fields.fullName.value.trim(),
            documentNumber: fields.documentNumber.value.trim(),
            age: parseInt(fields.age.value),
            email: fields.email.value.trim(),
            whatsapp: `+57 ${fields.whatsapp.value.trim()}`,
            mainGoal: getRadioValue('mainGoal'),
            otherGoal: fields.otherGoal ? fields.otherGoal.value.trim() : '',
            preferredSchedule: getRadioValue('preferredSchedule'),
            howDidYouKnow: getRadioValue('howDidYouKnow'),
            otherSource: fields.otherSource ? fields.otherSource.value.trim() : '',
            consent: getRadioValue('consent'),
            timestamp: new Date().toISOString()
        };
    }

    function processFormData(data) {
        // Aquí puedes procesar los datos como necesites
        console.log('Datos del formulario:', data);
        
        // Ejemplo: Guardar en localStorage (para demo)
        const registrations = JSON.parse(localStorage.getItem('korpusRegistrations') || '[]');
        registrations.push(data);
        localStorage.setItem('korpusRegistrations', JSON.stringify(registrations));
        
        // Aquí harías la llamada real a tu API
        // fetch('/api/register', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(data)
        // });
    }

    function setLoadingState(loading) {
        if (loading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            form.classList.add('loading');
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Registrarme';
            form.classList.remove('loading');
        }
    }

    function showSuccess() {
        // Animar la tarjeta
        document.querySelector('.registration-card').classList.add('success-animation');
        
        // Mostrar modal de éxito
        successModal.show();
        
        // Limpiar formulario después de un breve delay
        setTimeout(() => {
            handleReset();
        }, 1000);

        // No hacer focus automático después del modal
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
        const firstRadio = radios[0];
        
        // Limpiar clases previas
        radios.forEach(radio => {
            radio.classList.remove('is-valid', 'is-invalid');
        });

        if (checked) {
            // Marcar como válido
            firstRadio.classList.add('is-valid');
            return true;
        } else {
            // Marcar como inválido
            firstRadio.classList.add('is-invalid');
            return false;
        }
    }

    // Funciones de utilidad adicionales
    function showNotification(message, type = 'info') {
        // Crear notificación toast
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${getIconForType(type)} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        // Agregar toast al DOM si no existe container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, {
            autohide: type !== 'danger', // Los errores no se ocultan automáticamente
            delay: type === 'danger' ? 8000 : 5000
        });
        toast.show();
        
        // Remover del DOM después de que se oculte
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    function getIconForType(type) {
        const icons = {
            'success': 'fa-check-circle',
            'danger': 'fa-exclamation-triangle',
            'warning': 'fa-exclamation-circle',
            'info': 'fa-info-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    // Exponer funciones útiles al scope global para debugging
    window.KorpusForm = {
        validateForm,
        collectFormData,
        showNotification
    };
});

// Funciones adicionales para mejorar la experiencia de usuario
document.addEventListener('DOMContentLoaded', function() {
    // Mejorar accesibilidad con navegación por teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
            const form = e.target.closest('form');
            const inputs = Array.from(form.querySelectorAll('input, select, textarea'));
            const index = inputs.indexOf(e.target);
            
            if (index > -1 && index < inputs.length - 1) {
                e.preventDefault();
                inputs[index + 1].focus();
            }
        }
    });

    // Solo focus inicial al cargar el DOM - sin eventos adicionales
});