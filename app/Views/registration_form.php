<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KORPUS Training Club</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container-fluid min-vh-100 py-2 py-md-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0 registration-card my-2 my-md-3">
                    <div class="card-header text-center py-4">
                        <div class="logo-container">
                            <img src="assets/logo.webp" 
                                 alt="KORPUS Training Club Logo" 
                                 class="logo-image"
                                 loading="lazy">
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- <h2 class="text-center mb-4 form-title">Registro de Miembro</h2> -->
                        
                        <form id="registrationForm" action="/" method="POST" novalidate>
                            <?= csrf_field() ?>
                            <!-- Sección 1: Datos Personales -->
                            <div class="form-section mb-4">
                                <h4 class="section-title mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    1. Datos Personales
                                </h4>
                                
                                <!-- Nombre Completo -->
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">
                                        Nombre Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control custom-input" 
                                           id="fullName" 
                                           name="fullName" 
                                           required
                                           placeholder="Ingresa tu nombre completo">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa tu nombre completo.
                                    </div>
                                </div>
                                
                                <!-- Número de Documento -->
                                <div class="mb-3">
                                    <label for="documentNumber" class="form-label">
                                        Número de Documento
                                    </label>
                                    <input type="text" 
                                           class="form-control custom-input" 
                                           id="documentNumber" 
                                           name="documentNumber" 
                                           placeholder="Ej: 1234567890"
                                           pattern="[0-9]{6,12}">
                                    <div class="invalid-feedback">
                                        Si decides ingresarlo, debe ser un número de documento válido (6-12 dígitos).
                                    </div>
                                </div>
                                
                                <!-- Edad -->
                                <div class="mb-3">
                                    <label for="age" class="form-label">
                                        Edad <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control custom-input" 
                                           id="age" 
                                           name="age" 
                                           required
                                           min="16" 
                                           max="80"
                                           placeholder="Ej: 25">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa una edad válida (16-80 años).
                                    </div>
                                </div>
                                
                                <!-- Correo Electrónico -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control custom-input" 
                                           id="email" 
                                           name="email" 
                                           required
                                           placeholder="ejemplo@correo.com">
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un correo electrónico válido.
                                    </div>
                                </div>
                                
                                <!-- Número de WhatsApp -->
                                <div class="mb-3">
                                    <label for="whatsapp" class="form-label">
                                        Número de WhatsApp <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text custom-input-addon">+57</span>
                                        <input type="tel" 
                                               class="form-control custom-input" 
                                               id="whatsapp" 
                                               name="whatsapp" 
                                               required
                                               placeholder="XXX XXX XXXX"
                                               pattern="[0-9]{3}[\s]?[0-9]{3}[\s]?[0-9]{4}">
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor, ingresa un número de WhatsApp válido (formato: XXX XXX XXXX).
                                    </div>
                                    <small class="form-text text-muted">Formato: XXX XXX XXXX</small>
                                </div>
                            </div>
                            
                            <!-- Sección 2: Objetivo Principal -->
                            <div class="form-section mb-4">
                                <h4 class="section-title mb-3">
                                    <i class="fas fa-bullseye me-2"></i>
                                    2. Objetivo Principal
                                </h4>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="mainGoal" 
                                                   id="goalWeightLoss" 
                                                   value="bajar_peso"
                                                   required>
                                            <label class="form-check-label" for="goalWeightLoss">
                                                Bajar de peso
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="mainGoal" 
                                                   id="goalMuscle" 
                                                   value="aumentar_masa">
                                            <label class="form-check-label" for="goalMuscle">
                                                Aumentar masa muscular
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="mainGoal" 
                                                   id="goalHealth" 
                                                   value="mejorar_salud">
                                            <label class="form-check-label" for="goalHealth">
                                                Mejorar mi salud
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="mainGoal" 
                                                   id="goalTone" 
                                                   value="tonificar">
                                            <label class="form-check-label" for="goalTone">
                                                Tonificar
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="mainGoal" 
                                                   id="goalOther" 
                                                   value="otro">
                                            <label class="form-check-label" for="goalOther">
                                                Otro:
                                            </label>
                                        </div>
                                        <div class="mb-3" id="otherGoalContainer" style="display: none;">
                                            <input type="text" 
                                                   class="form-control custom-input" 
                                                   id="otherGoal" 
                                                   name="otherGoal" 
                                                   placeholder="Especifica tu objetivo"
                                                   maxlength="100">
                                        </div>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu objetivo principal.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección 3: Horario de Preferencia -->
                            <div class="form-section mb-4">
                                <h4 class="section-title mb-3">
                                    <i class="fas fa-clock me-2"></i>
                                    3. Horario de Preferencia
                                </h4>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="preferredSchedule" 
                                                   id="scheduleMorning" 
                                                   value="manana"
                                                   required>
                                            <label class="form-check-label" for="scheduleMorning">
                                                <strong>Mañana</strong> (5:00 a.m. – 9:00 a.m.)
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="preferredSchedule" 
                                                   id="scheduleNoon" 
                                                   value="medio_dia">
                                            <label class="form-check-label" for="scheduleNoon">
                                                <strong>Medio día</strong> (11:00 a.m. – 2:00 p.m.)
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="preferredSchedule" 
                                                   id="scheduleEvening" 
                                                   value="tarde_noche">
                                            <label class="form-check-label" for="scheduleEvening">
                                                <strong>Tarde/Noche</strong> (5:00 p.m. – 9:00 p.m.)
                                            </label>
                                        </div>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona tu horario de preferencia.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección 4: ¿Cómo te enteraste de nosotros? -->
                            <div class="form-section mb-4">
                                <h4 class="section-title mb-3">
                                    <i class="fas fa-search me-2"></i>
                                    4. ¿Cómo te enteraste de nosotros?
                                </h4>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="howDidYouKnow" 
                                                   id="knowSocial" 
                                                   value="redes_sociales"
                                                   required>
                                            <label class="form-check-label" for="knowSocial">
                                                Redes sociales
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="howDidYouKnow" 
                                                   id="knowFriend" 
                                                   value="amigo_familiar">
                                            <label class="form-check-label" for="knowFriend">
                                                Amigo o familiar
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="howDidYouKnow" 
                                                   id="knowAdvertising" 
                                                   value="publicidad_local">
                                            <label class="form-check-label" for="knowAdvertising">
                                                Publicidad local
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="howDidYouKnow" 
                                                   id="knowOther" 
                                                   value="otro">
                                            <label class="form-check-label" for="knowOther">
                                                Otro:
                                            </label>
                                        </div>
                                        <div class="mb-3" id="otherSourceContainer" style="display: none;">
                                            <input type="text" 
                                                   class="form-control custom-input" 
                                                   id="otherSource" 
                                                   name="otherSource" 
                                                   placeholder="¿Cómo te enteraste?"
                                                   maxlength="100">
                                        </div>
                                        <div class="invalid-feedback">
                                            Por favor, selecciona cómo te enteraste de nosotros.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección 5: Consentimiento -->
                            <div class="form-section mb-4">
                                <h4 class="section-title mb-3">
                                    <i class="fas fa-handshake me-2"></i>
                                    5. Consentimiento
                                </h4>
                                
                                <div class="consent-box p-3 mb-3">
                                    <p class="mb-3 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Acepto ser contactado por WhatsApp y correo electrónico para recibir información sobre planes, promociones y eventos de KORPUS Training Club.
                                    </p>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="consent" 
                                                       id="consentYes" 
                                                       value="si"
                                                       required>
                                                <label class="form-check-label fw-bold text-success" for="consentYes">
                                                    <i class="fas fa-check me-1"></i>
                                                    Sí, acepto
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="consent" 
                                                       id="consentNo" 
                                                       value="no">
                                                <label class="form-check-label fw-bold text-warning" for="consentNo">
                                                    <i class="fas fa-times me-1"></i>
                                                    No, gracias
                                                </label>
                                            </div>
                                            <div class="invalid-feedback">
                                                Por favor, selecciona una opción de consentimiento.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg custom-btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Registrarme
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Registro Exitoso!
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">¡Bienvenido a KORPUS Training Club!</p>
                    <p class="text-muted">Tu registro ha sido completado exitosamente. Pronto nos pondremos en contacto contigo.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="fas fa-thumbs-up me-2"></i>
                        ¡Entendido!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>