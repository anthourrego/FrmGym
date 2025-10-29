<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - KORPUS Training Club</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
    
    <style>
        body {
            background: var(--korpus-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        /* Patrón de fondo sutil */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(156, 175, 183, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(156, 175, 183, 0.1) 0%, transparent 50%);
            z-index: -1;
        }
        
        .admin-login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 
                0 20px 40px rgba(18, 18, 18, 0.2),
                0 0 0 1px rgba(156, 175, 183, 0.1);
            border: 1px solid rgba(156, 175, 183, 0.2);
            max-width: 420px;
            width: 100%;
            overflow: hidden;
        }
        
        .admin-header {
            background: var(--korpus-gradient);
            color: var(--korpus-white);
            text-align: center;
            padding: 2.5rem 1.5rem;
            position: relative;
        }
        
        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .admin-header .content {
            position: relative;
            z-index: 1;
        }
        
        .admin-header i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
            color: var(--korpus-white);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .admin-header h3 {
            color: var(--korpus-white);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: var(--korpus-acero);
            box-shadow: 0 0 0 0.2rem var(--korpus-acero-light);
            background: var(--korpus-white);
        }
        
        .btn-admin {
            background: var(--korpus-gradient);
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 600;
            color: var(--korpus-white);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(156, 175, 183, 0.3);
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(156, 175, 183, 0.4);
            color: var(--korpus-white);
        }
        
        .btn-admin:active {
            transform: translateY(0);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--korpus-acero);
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: var(--korpus-potency);
        }
        
        .security-notice {
            background: linear-gradient(135deg, var(--korpus-acero) 0%, var(--korpus-potency) 100%);
            color: var(--korpus-white);
            padding: 1.2rem 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            position: relative;
        }
        
        .security-notice::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            backdrop-filter: blur(10px);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #842029;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #0f5132;
            border: 1px solid rgba(25, 135, 84, 0.2);
        }
        
        .korpus-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .korpus-link:hover {
            color: var(--korpus-white);
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        /* Partículas flotantes */
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            background: rgba(156, 175, 183, 0.1);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
                opacity: 0.1; 
            }
            50% { 
                transform: translateY(-30px) rotate(180deg); 
                opacity: 0.3; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="admin-login-card">
                    <div class="admin-header">
                        <div class="content">
                            <i class="fas fa-shield-alt"></i>
                            <h3 class="mb-0">Panel de Administración</h3>
                            <p class="mb-0 mt-2 opacity-75">KORPUS Training Club</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= session('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('admin/authenticate') ?>" method="POST" id="adminLoginForm">
                            <?= csrf_field() ?>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-key me-2"></i>
                                    Contraseña de Administrador
                                </label>
                                <div class="position-relative">
                                    <input type="password" 
                                           class="form-control form-control-lg pe-5" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="Ingresa la contraseña">
                                    <button type="button" class="password-toggle" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-admin btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Acceder al Panel
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Solo personal autorizado puede acceder
                            </small>
                        </div>
                    </div>
                    
                    <div class="security-notice">
                        <i class="fas fa-lock me-2"></i>
                        <strong>Acceso Seguro:</strong> La sesión expira en 2 horas por seguridad
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?= base_url('/') ?>" class="korpus-link">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al sitio principal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        // Enfocar automáticamente el campo de contraseña
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('password').focus();
        });
        
        // Validación del formulario
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value.trim();
            
            if (password === '') {
                e.preventDefault();
                alert('Por favor ingresa la contraseña');
                return false;
            }
            
            // Mostrar estado de carga
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
        });
        
        // Efecto de partículas de fondo (opcional)
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
        });
        
        function createParticles() {
            const particlesContainer = document.createElement('div');
            particlesContainer.className = 'floating-particles';
            
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.cssText = `
                    width: ${Math.random() * 8 + 4}px;
                    height: ${Math.random() * 8 + 4}px;
                    left: ${Math.random() * 100}%;
                    top: ${Math.random() * 100}%;
                    animation-delay: ${Math.random() * 8}s;
                    animation-duration: ${Math.random() * 6 + 6}s;
                `;
                particlesContainer.appendChild(particle);
            }
            
            document.body.appendChild(particlesContainer);
        }
    </script>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.1; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 0.3; }
        }
    </style>
</body>
</html>