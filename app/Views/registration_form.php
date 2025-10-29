<?php

namespace App\Views;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'KORPUS Training Club - Registro' ?></title>
    <link rel="icon" href="<?= base_url('assets/logo.webp') ?>" type="image/webp">
    <link rel="icon" href="<?= base_url('site.webmanifest') ?>" sizes="192x192">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="background-overlay"></div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="form-container">
                    <!-- Header con Logo -->
                    <div class="text-center mb-4">
                        <img src="<?= base_url('assets/logo.webp') ?>" alt="KORPUS Training Club" class="logo-img">
                        <h2 class="form-title">Registro de Miembros</h2>
                        <p class="form-subtitle">¡Únete a la familia KORPUS!</p>
                    </div>

                    <!-- Mostrar mensajes de validación -->
                    <?php if (session()->has('validation')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('validation')->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <!-- Mostrar mensaje de éxito -->
                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success">
                            <?= session('success') ?>
                        </div>
                    <?php endif ?>

                    <!-- Formulario de Registro -->
                    <?= form_open(base_url('/'), ['id' => 'registrationForm', 'class' => 'needs-validation', 'novalidate' => true]) ?>
                        
                        <!-- Sección 1: Información Personal -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user text-primary me-2"></i>
                                Información Personal
                            </h5>
                            
                            <div class="mb-3">
                                <?= form_label('Nombre Completo', 'fullName', ['class' => 'form-label required']) ?>
                                <?= form_input([
                                    'name' => 'fullName',
                                    'id' => 'fullName',
                                    'class' => 'form-control',
                                    'placeholder' => 'Tu nombre completo',
                                    'value' => old('fullName'),
                                    'required' => true,
                                    'maxlength' => 50
                                ]) ?>
                                <div class="invalid-feedback">Por favor ingresa tu nombre completo.</div>
                            </div>

                            <div class="mb-3">
                                <?= form_label('Número de Documento (Opcional)', 'documentNumber', ['class' => 'form-label']) ?>
                                <?= form_input([
                                    'name' => 'documentNumber',
                                    'id' => 'documentNumber',
                                    'class' => 'form-control',
                                    'placeholder' => 'Cédula, pasaporte, etc.',
                                    'value' => old('documentNumber'),
                                    'maxlength' => 12
                                ]) ?>
                                <div class="form-text">Opcional - Solo si deseas proporcionarlo</div>
                            </div>

                            <div class="mb-3">
                                <?= form_label('Edad', 'age', ['class' => 'form-label required']) ?>
                                <?= form_input([
                                    'name' => 'age',
                                    'id' => 'age',
                                    'type' => 'number',
                                    'class' => 'form-control',
                                    'placeholder' => 'Tu edad',
                                    'value' => old('age'),
                                    'required' => true,
                                    'min' => 15,
                                    'max' => 100
                                ]) ?>
                                <div class="invalid-feedback">Debes tener entre 15 y 100 años.</div>
                            </div>

                            <div class="mb-3">
                                <?= form_label('Email', 'email', ['class' => 'form-label required']) ?>
                                <?= form_input([
                                    'name' => 'email',
                                    'id' => 'email',
                                    'type' => 'email',
                                    'class' => 'form-control',
                                    'placeholder' => 'tu@email.com',
                                    'value' => old('email'),
                                    'required' => true,
                                    'maxlength' => 100
                                ]) ?>
                                <div class="invalid-feedback">Por favor ingresa un email válido.</div>
                            </div>

                            <div class="mb-3">
                                <?= form_label('WhatsApp', 'whatsapp', ['class' => 'form-label required']) ?>
                                <div class="input-group">
                                    <span class="input-group-text">+57</span>
                                    <?= form_input([
                                        'name' => 'whatsapp',
                                        'id' => 'whatsapp',
                                        'class' => 'form-control',
                                        'placeholder' => '300 123 4567',
                                        'value' => old('whatsapp'),
                                        'required' => true,
                                        'maxlength' => 15
                                    ]) ?>
                                </div>
                                <div class="invalid-feedback">Por favor ingresa un número de WhatsApp válido.</div>
                            </div>
                        </div>

                        <!-- Sección 2: Objetivos -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-bullseye text-success me-2"></i>
                                Objetivos de Entrenamiento
                            </h5>
                            
                            <div class="mb-3">
                                <?= form_label('¿Cuál es tu objetivo principal?', 'mainGoal', ['class' => 'form-label required']) ?>
                                
                                <div class="goal-options">
                                    <?php 
                                    $goals = [
                                        'bajar_peso' => 'Bajar de peso',
                                        'aumentar_masa' => 'Aumentar masa muscular',
                                        'mejorar_salud' => 'Mejorar salud general',
                                        'tonificar' => 'Tonificar el cuerpo',
                                        'otro' => 'Otro objetivo'
                                    ];
                                    ?>
                                    
                                    <?php foreach ($goals as $value => $label): ?>
                                        <div class="form-check">
                                            <?= form_radio([
                                                'name' => 'mainGoal',
                                                'id' => 'goal_' . $value,
                                                'value' => $value,
                                                'class' => 'form-check-input',
                                                'checked' => old('mainGoal') === $value
                                            ]) ?>
                                            <?= form_label($label, 'goal_' . $value, ['class' => 'form-check-label']) ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                                <div class="invalid-feedback">Por favor selecciona tu objetivo principal.</div>
                            </div>

                            <div class="mb-3 d-none" id="otherGoalContainer">
                                <?= form_label('Especifica tu objetivo:', 'otherGoal', ['class' => 'form-label']) ?>
                                <?= form_input([
                                    'name' => 'otherGoal',
                                    'id' => 'otherGoal',
                                    'class' => 'form-control',
                                    'placeholder' => 'Describe tu objetivo específico',
                                    'value' => old('otherGoal'),
                                    'maxlength' => 100
                                ]) ?>
                            </div>
                        </div>

                        <!-- Sección 3: Horarios -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-clock text-warning me-2"></i>
                                Horarios Preferidos
                            </h5>
                            
                            <div class="mb-3">
                                <?= form_label('¿En qué horario prefieres entrenar?', 'preferredSchedule', ['class' => 'form-label required']) ?>
                                
                                <div class="schedule-options">
                                    <?php 
                                    $schedules = [
                                        'manana' => ['label' => 'Mañana', 'time' => '6:00 AM - 12:00 PM', 'icon' => 'fa-sun'],
                                        'medio_dia' => ['label' => 'Medio día', 'time' => '12:00 PM - 3:00 PM', 'icon' => 'fa-sun'],
                                        'tarde_noche' => ['label' => 'Tarde/Noche', 'time' => '3:00 PM - 10:00 PM', 'icon' => 'fa-moon']
                                    ];
                                    ?>
                                    
                                    <?php foreach ($schedules as $value => $info): ?>
                                        <div class="form-check schedule-card">
                                            <?= form_radio([
                                                'name' => 'preferredSchedule',
                                                'id' => 'schedule_' . $value,
                                                'value' => $value,
                                                'class' => 'form-check-input',
                                                'checked' => old('preferredSchedule') === $value
                                            ]) ?>
                                            <?= form_label('
                                                <i class="fas ' . $info['icon'] . ' me-2"></i>
                                                <strong>' . $info['label'] . '</strong><br>
                                                <small>' . $info['time'] . '</small>
                                            ', 'schedule_' . $value, ['class' => 'form-check-label']) ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                                <div class="invalid-feedback">Por favor selecciona tu horario preferido.</div>
                            </div>
                        </div>

                        <!-- Sección 4: Cómo nos conociste -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-share-alt text-info me-2"></i>
                                ¿Cómo nos conociste?
                            </h5>
                            
                            <div class="mb-3">
                                <?= form_label('Selecciona una opción:', 'howDidYouKnow', ['class' => 'form-label required']) ?>
                                
                                <div class="source-options">
                                    <?php 
                                    $sources = [
                                        'redes_sociales' => 'Redes sociales (Instagram, Facebook, etc.)',
                                        'amigo_familiar' => 'Por un amigo o familiar',
                                        'publicidad_local' => 'Publicidad local (volantes, carteles, etc.)',
                                        'otro' => 'Otro medio'
                                    ];
                                    ?>
                                    
                                    <?php foreach ($sources as $value => $label): ?>
                                        <div class="form-check">
                                            <?= form_radio([
                                                'name' => 'howDidYouKnow',
                                                'id' => 'source_' . $value,
                                                'value' => $value,
                                                'class' => 'form-check-input',
                                                'checked' => old('howDidYouKnow') === $value
                                            ]) ?>
                                            <?= form_label($label, 'source_' . $value, ['class' => 'form-check-label']) ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                                <div class="invalid-feedback">Por favor selecciona cómo nos conociste.</div>
                            </div>

                            <div class="mb-3 d-none" id="otherSourceContainer">
                                <?= form_label('Especifica el medio:', 'otherSource', ['class' => 'form-label']) ?>
                                <?= form_input([
                                    'name' => 'otherSource',
                                    'id' => 'otherSource',
                                    'class' => 'form-control',
                                    'placeholder' => 'Dinos cómo nos conociste',
                                    'value' => old('otherSource'),
                                    'maxlength' => 100
                                ]) ?>
                            </div>
                        </div>

                        <!-- Sección 5: Consentimiento -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-shield-alt text-secondary me-2"></i>
                                Consentimiento
                            </h5>
                            
                            <div class="mb-4">
                                <div class="consent-box p-3 border rounded">
                                    <div class="form-check">
                                        <?= form_checkbox([
                                            'name' => 'consent',
                                            'id' => 'consent',
                                            'value' => 'si',
                                            'class' => 'form-check-input',
                                            'checked' => old('consent') === 'si'
                                        ]) ?>
                                        <?= form_label('
                                            Acepto que mis datos personales sean utilizados para contactarme 
                                            y brindarme información sobre los servicios de KORPUS Training Club. 
                                            Entiendo que puedo solicitar la eliminación de mis datos en cualquier momento.
                                        ', 'consent', ['class' => 'form-check-label']) ?>
                                    </div>
                                </div>
                                <div class="invalid-feedback">Debes aceptar el consentimiento para continuar.</div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-buttons text-center">
                            <?= form_submit('submit', '¡Registrarme en KORPUS!', [
                                'class' => 'btn btn-primary btn-lg px-5',
                                'id' => 'submitBtn'
                            ]) ?>
                        </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/script.js') ?>"></script>
</body>
</html>