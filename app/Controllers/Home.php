<?php

namespace App\Controllers;

use App\Models\FrmGymModel;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    protected $frmGymModel;

    public function __construct()
    {
        $this->frmGymModel = new FrmGymModel();
    }

    /**
     * Mostrar el formulario de registro
     */
    public function index(): string
    {
        $data = [
            'title' => 'KORPUS Training Club - Registro de Miembros'
        ];
        
        return view('registration_form', $data);
    }

    /**
     * Procesar el registro del formulario
     * Optimizado para peticiones fetch/AJAX con manejo mejorado de respuestas
     */
    public function register()
    {   
        // Validar que sea una petición POST
        if (!$this->request->is('post')) {
            return $this->buildErrorResponse(
                'Método no permitido', 
                ResponseInterface::HTTP_METHOD_NOT_ALLOWED,
                ['allowed_methods' => ['POST']]
            );
        }

        // Verificar si es una petición AJAX/fetch
        $isAjax = $this->request->isAJAX() || 
                  $this->request->getHeaderLine('Content-Type') === 'application/json' ||
                  $this->request->getHeaderLine('Accept') === 'application/json';

        // Logging de petición para debugging
        log_message('info', 'Registro iniciado - IP: ' . $this->request->getIPAddress() . 
                   ' - User Agent: ' . substr($this->request->getUserAgent()->getAgentString(), 0, 100) .
                   ' - AJAX: ' . ($isAjax ? 'Yes' : 'No'));

        // Rate limiting básico (máximo 5 intentos por minuto por IP)
        if (!$this->checkRateLimit()) {
            return $this->buildErrorResponse(
                'Demasiados intentos. Por favor espera un momento antes de intentar nuevamente.',
                ResponseInterface::HTTP_TOO_MANY_REQUESTS,
                ['retry_after' => 60]
            );
        }

        // Reglas de validación mejoradas
        $validationRules = [
            'fullName' => [
                'rules' => 'required|min_length[2]|max_length[50]|alpha_space',
                'errors' => [
                    'required' => 'El nombre completo es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 2 caracteres',
                    'max_length' => 'El nombre no puede exceder 50 caracteres',
                    'alpha_space' => 'El nombre solo puede contener letras y espacios'
                ]
            ],
            'documentNumber' => [
                'rules' => 'permit_empty|max_length[12]|numeric',
                'errors' => [
                    'max_length' => 'El documento no puede exceder 12 caracteres',
                    'numeric' => 'El documento solo puede contener números'
                ]
            ],
            'age' => [
                'rules' => 'required|integer|greater_than_equal_to[15]|less_than_equal_to[100]',
                'errors' => [
                    'required' => 'La edad es obligatoria',
                    'integer' => 'La edad debe ser un número entero',
                    'greater_than_equal_to' => 'Debes tener al menos 15 años para registrarte',
                    'less_than_equal_to' => 'La edad máxima permitida es 100 años'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[100]|is_unique[gym_registrations.email]',
                'errors' => [
                    'required' => 'El email es obligatorio',
                    'valid_email' => 'Por favor ingresa un email válido',
                    'max_length' => 'El email no puede exceder 100 caracteres',
                    'is_unique' => 'Este email ya está registrado en nuestro sistema'
                ]
            ],
            'whatsapp' => [
                'rules' => 'required|regex_match[/^[0-9\s\-\+\(\)]{10,20}$/]',
                'errors' => [
                    'required' => 'El número de WhatsApp es obligatorio',
                    'regex_match' => 'Por favor ingresa un número de WhatsApp válido (10-20 dígitos)'
                ]
            ],
            'mainGoal' => [
                'rules' => 'required|in_list[bajar_peso,aumentar_masa,mejorar_salud,tonificar,otro]',
                'errors' => [
                    'required' => 'Debes seleccionar tu objetivo principal',
                    'in_list' => 'Por favor selecciona un objetivo válido'
                ]
            ],
            'preferredSchedule' => [
                'rules' => 'required|in_list[manana,medio_dia,tarde_noche]',
                'errors' => [
                    'required' => 'Debes seleccionar tu horario preferido',
                    'in_list' => 'Por favor selecciona un horario válido'
                ]
            ],
            'howDidYouKnow' => [
                'rules' => 'required|in_list[redes_sociales,amigo_familiar,publicidad_local,otro]',
                'errors' => [
                    'required' => 'Debes indicar cómo nos conociste',
                    'in_list' => 'Por favor selecciona una opción válida'
                ]
            ],
            'consent' => [
                'rules' => 'required|in_list[si,no]',
                'errors' => [
                    'required' => 'Debes seleccionar una opción de consentimiento',
                    'in_list' => 'Por favor selecciona si aceptas o no el consentimiento'
                ]
            ]
        ];

        // Validaciones condicionales mejoradas
        $mainGoal = $this->request->getPost('mainGoal');
        $howDidYouKnow = $this->request->getPost('howDidYouKnow');

        if ($mainGoal === 'otro') {
            $validationRules['otherGoal'] = [
                'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric_space',
                'errors' => [
                    'required' => 'Debes especificar tu objetivo cuando seleccionas "Otro"',
                    'min_length' => 'La descripción del objetivo debe tener al menos 3 caracteres',
                    'max_length' => 'La descripción del objetivo no puede exceder 100 caracteres',
                    'alpha_numeric_space' => 'La descripción solo puede contener letras, números y espacios'
                ]
            ];
        }

        if ($howDidYouKnow === 'otro') {
            $validationRules['otherSource'] = [
                'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric_space',
                'errors' => [
                    'required' => 'Debes especificar el medio cuando seleccionas "Otro"',
                    'min_length' => 'La descripción del medio debe tener al menos 3 caracteres',
                    'max_length' => 'La descripción del medio no puede exceder 100 caracteres',
                    'alpha_numeric_space' => 'La descripción solo puede contener letras, números y espacios'
                ]
            ];
        }

        // Ejecutar validación
        if (!$this->validate($validationRules)) {
            return $this->buildValidationErrorResponse($this->validator->getErrors(), $isAjax);
        }

        // Preparar y sanitizar datos para insertar
        $data = [
            'full_name' => $this->sanitizeInput(trim($this->request->getPost('fullName'))),
            'document_number' => $this->request->getPost('documentNumber') ? 
                                $this->sanitizeInput($this->request->getPost('documentNumber')) : null,
            'age' => (int) $this->request->getPost('age'),
            'email' => strtolower(trim($this->sanitizeInput($this->request->getPost('email')))),
            'whatsapp' => preg_replace('/[^0-9]/', '', $this->request->getPost('whatsapp')),
            'main_goal' => $this->request->getPost('mainGoal'),
            'other_goal' => $mainGoal === 'otro' ? 
                           $this->sanitizeInput($this->request->getPost('otherGoal')) : null,
            'preferred_schedule' => $this->request->getPost('preferredSchedule'),
            'how_did_you_know' => $this->request->getPost('howDidYouKnow'),
            'other_source' => $howDidYouKnow === 'otro' ? 
                             $this->sanitizeInput($this->request->getPost('otherSource')) : null,
            'consent' => $this->request->getPost('consent'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => substr($this->request->getUserAgent()->getAgentString(), 0, 500),
            'status' => 'active',
            'registration_source' => $isAjax ? 'ajax' : 'form'
        ];

        try {
            // Validación adicional de integridad de datos
            $this->validateDataIntegrity($data);

            // Intentar guardar en la base de datos
            $insertId = $this->frmGymModel->insert($data);

            if ($insertId) {
                // Log de registro exitoso
                log_message('info', "Registro exitoso - ID: {$insertId} - Email: {$data['email']} - IP: {$data['ip_address']}");

                // Personalizar mensaje según el consentimiento
                $consentMessage = $data['consent'] === 'si' 
                    ? '¡Registro exitoso! Te contactaremos pronto para coordinar tu primera sesión en KORPUS Training Club. ¡Bienvenido a la familia KORPUS!'
                    : '¡Registro exitoso! Respetaremos tu preferencia de no recibir comunicaciones promocionales. Solo te contactaremos para asuntos esenciales de tu membresía.';
                
                // Preparar respuesta de éxito con datos estructurados
                $successResponse = [
                    'success' => true,
                    'message' => $consentMessage,
                    'data' => [
                        'id' => $insertId,
                        'name' => $data['full_name'],
                        'email' => $data['email'],
                        'consent_given' => $data['consent'] === 'si',
                        'registration_date' => date('Y-m-d H:i:s'),
                        'member_number' => 'KORPUS-' . str_pad($insertId, 6, '0', STR_PAD_LEFT)
                    ],
                    'metadata' => [
                        'timestamp' => time(),
                        'version' => '1.0',
                        'source' => $data['registration_source']
                    ]
                ];

                // Respuesta para peticiones AJAX/fetch
                if ($isAjax) {
                    return $this->response
                        ->setStatusCode(ResponseInterface::HTTP_CREATED)
                        ->setJSON($successResponse)
                        ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
                }

                // Respuesta para peticiones normales (fallback)
                return redirect()->to('/')->with('success', $successResponse['message']);

            } else {
                throw new \Exception('No se pudo completar el registro en la base de datos');
            }

        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return $this->handleDatabaseError($e, $isAjax);
        } catch (\Exception $e) {
            return $this->handleGeneralError($e, $isAjax);
        }
    }

    /**
     * Endpoint para estadísticas básicas (futuro)
     */
    public function stats()
    {
        $stats = [
            'total_registrations' => $this->frmGymModel->countAll(),
            'today_registrations' => $this->frmGymModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'this_month_registrations' => $this->frmGymModel->where('MONTH(created_at)', date('m'))
                                                           ->where('YEAR(created_at)', date('Y'))
                                                           ->countAllResults(),
            'consent_stats' => [
                'with_consent' => $this->frmGymModel->where('consent', 'si')->countAllResults(),
                'without_consent' => $this->frmGymModel->where('consent', 'no')->countAllResults()
            ]
        ];

        return $this->response->setJSON($stats);
    }

    // =====================================================
    // MÉTODOS AUXILIARES PARA MEJORAR EL SISTEMA DE REGISTRO
    // =====================================================

    /**
     * Verificar rate limiting básico
     */
    private function checkRateLimit(): bool
    {
        $cache = service('cache');
        $ip = $this->request->getIPAddress();
        $key = 'rate_limit_' . md5($ip);
        
        $attempts = $cache->get($key) ?? 0;
        
        if ($attempts >= 5) {
            return false;
        }
        
        $cache->save($key, $attempts + 1, 60); // 60 segundos
        return true;
    }

    /**
     * Sanitizar entrada de datos
     */
    private function sanitizeInput(string $input): string
    {
        // Remover caracteres peligrosos y normalizar
        $input = trim($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Remover caracteres de control (excepto salto de línea y tabulación)
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        return $input;
    }

    /**
     * Construir respuesta de error estándar
     */
    private function buildErrorResponse(string $message, int $statusCode = 400, array $additional = []): ResponseInterface
    {
        $errorResponse = [
            'success' => false,
            'message' => $message,
            'error_code' => $statusCode,
            'timestamp' => date('Y-m-d H:i:s'),
            ...$additional
        ];

        log_message('warning', "Error response - Status: {$statusCode} - Message: {$message} - IP: " . $this->request->getIPAddress());

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($errorResponse)
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Construir respuesta de errores de validación
     */
    private function buildValidationErrorResponse(array $errors, bool $isAjax): ResponseInterface
    {
        $validationResponse = [
            'success' => false,
            'message' => 'Los datos enviados contienen errores. Por favor revisa la información.',
            'errors' => $errors,
            'error_type' => 'validation',
            'timestamp' => date('Y-m-d H:i:s')
        ];

        log_message('info', 'Validation errors: ' . json_encode($errors) . ' - IP: ' . $this->request->getIPAddress());

        if ($isAjax) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                ->setJSON($validationResponse)
                ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        // Fallback para peticiones no-AJAX
        return redirect()->back()->withInput()->with('validation', $this->validator);
    }

    /**
     * Validar integridad de datos adicional
     */
    private function validateDataIntegrity(array $data): void
    {
        // Validar que el email no tenga patrones sospechosos
        if (preg_match('/[<>"\']|script|javascript/i', $data['email'])) {
            throw new \InvalidArgumentException('Email contiene caracteres no válidos');
        }

        // Validar que el número de WhatsApp tenga al menos 10 dígitos
        if (strlen($data['whatsapp']) < 10) {
            throw new \InvalidArgumentException('Número de WhatsApp muy corto');
        }

        // Validar que la edad sea razonable
        if ($data['age'] < 15 || $data['age'] > 100) {
            throw new \InvalidArgumentException('Edad fuera del rango permitido');
        }

        // Validar campos condicionales
        if ($data['main_goal'] === 'otro' && empty($data['other_goal'])) {
            throw new \InvalidArgumentException('Objetivo personalizado requerido');
        }

        if ($data['how_did_you_know'] === 'otro' && empty($data['other_source'])) {
            throw new \InvalidArgumentException('Fuente personalizada requerida');
        }
    }

    /**
     * Manejar errores de base de datos
     */
    private function handleDatabaseError(\CodeIgniter\Database\Exceptions\DatabaseException $e, bool $isAjax): ResponseInterface
    {
        $errorCode = $e->getCode();
        $errorMessage = 'Error al procesar tu registro. Por favor intenta nuevamente.';

        // Manejar diferentes tipos de errores de base de datos
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            if (strpos($e->getMessage(), 'email') !== false) {
                $errorMessage = 'Este email ya está registrado en nuestro sistema.';
            } else {
                $errorMessage = 'Los datos enviados ya existen en nuestro sistema.';
            }
        } elseif (strpos($e->getMessage(), 'Connection') !== false) {
            $errorMessage = 'Problemas de conexión con la base de datos. Por favor intenta más tarde.';
        }

        log_message('error', 'Database error during registration: ' . $e->getMessage() . 
                   ' - IP: ' . $this->request->getIPAddress());

        if ($isAjax) {
            return $this->buildErrorResponse($errorMessage, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, [
                'error_type' => 'database',
                'retry_recommended' => true
            ]);
        }

        return redirect()->back()->withInput()->with('error', $errorMessage);
    }

    /**
     * Manejar errores generales
     */
    private function handleGeneralError(\Exception $e, bool $isAjax): ResponseInterface
    {
        $errorMessage = 'Ocurrió un error inesperado. Por favor intenta nuevamente.';
        
        // Manejar tipos específicos de errores
        if ($e instanceof \InvalidArgumentException) {
            $errorMessage = $e->getMessage();
            $statusCode = ResponseInterface::HTTP_BAD_REQUEST;
        } else {
            $statusCode = ResponseInterface::HTTP_INTERNAL_SERVER_ERROR;
        }

        log_message('error', 'General error during registration: ' . $e->getMessage() . 
                   ' - File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . 
                   ' - IP: ' . $this->request->getIPAddress());

        if ($isAjax) {
            return $this->buildErrorResponse($errorMessage, $statusCode, [
                'error_type' => 'general',
                'debug_info' => ENVIRONMENT === 'development' ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ]);
        }

        return redirect()->back()->withInput()->with('error', $errorMessage);
    }
}
