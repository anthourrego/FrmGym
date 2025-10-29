// Función de prueba para simular errores del servidor
// Usar en la consola del navegador para probar el manejo de errores

function testErrorHandling() {
    console.log('🧪 Iniciando pruebas de manejo de errores...');
    
    // Simulación 1: Error exacto como aparece en la imagen del usuario
    const exactUserError = {
        success: false,
        message: "Los datos enviados contienen errores. Por favor revisa la información.",
        error_type: "validation",
        errors: {
            email: ["Este email ya está registrado en nuestro sistema"]
        },
        timestamp: "2025-10-29 04:21:40"
    };
    
    // Simulación 2: Error de validación con múltiples campos
    const multipleErrorsResponse = {
        success: false,
        message: "Los datos enviados contienen errores. Por favor revisa la información.",
        errors: {
            email: ["Este email ya está registrado en nuestro sistema"],
            whatsapp: ["Por favor ingresa un número de WhatsApp válido (10-20 dígitos)"],
            age: ["Debes tener al menos 15 años para registrarte"],
            consent: ["Debes seleccionar una opción de consentimiento"]
        },
        error_type: "validation",
        timestamp: "2025-10-29 04:21:40"
    };
    
    // Simulación 3: Error único de validación con string directo
    const singleStringError = {
        success: false,
        message: "Los datos enviados contienen errores. Por favor revisa la información.",
        errors: {
            email: "Este email ya está registrado en nuestro sistema"
        },
        error_type: "validation",
        timestamp: "2025-10-29 04:21:40"
    };
    
    // Simulación 4: Error de servidor general
    const serverErrorResponse = {
        success: false,
        message: "Error interno del servidor. Por favor intenta más tarde",
        error_type: "server",
        timestamp: "2025-10-29 04:21:40"
    };
    
    // Función para simular respuesta del servidor
    window.simulateErrorResponse = function(type = 'userExact') {
        let response;
        
        switch(type) {
            case 'userExact':
                response = exactUserError;
                console.log('🎯 Simulando error exacto del usuario...');
                break;
            case 'multiple':
                response = multipleErrorsResponse;
                console.log('📝 Simulando errores múltiples...');
                break;
            case 'single':
                response = singleStringError;
                console.log('📝 Simulando error único como string...');
                break;
            case 'server':
                response = serverErrorResponse;
                console.log('📝 Simulando error de servidor...');
                break;
            default:
                response = exactUserError;
        }
        
        console.log('📤 Enviando respuesta:', JSON.stringify(response, null, 2));
        
        // Llamar directamente a la función de manejo de errores
        if (typeof handleErrorResponse === 'function') {
            handleErrorResponse(response);
        } else {
            console.error('❌ Función handleErrorResponse no encontrada');
        }
    };
    
    // Función para probar ValidationError directamente
    window.testValidationError = function() {
        console.log('🧪 Probando ValidationError directamente...');
        const validationError = new ValidationError(exactUserError);
        handleErrorResponse(validationError.data);
    };
    
    console.log(`
🎮 Pruebas disponibles:
- simulateErrorResponse('userExact') - Error exacto como el del usuario
- simulateErrorResponse('multiple') - Errores múltiples
- simulateErrorResponse('single') - Error único como string
- simulateErrorResponse('server') - Error de servidor
- testValidationError() - Probar ValidationError directamente

📋 Ejemplo de uso:
simulateErrorResponse('userExact');
    `);
}

// Ejecutar al cargar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', testErrorHandling);
} else {
    testErrorHandling();
}