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
     */
    public function register()
    {
        // Validar si es una petición POST
        if (!$this->request->is('post')) {
            return redirect()->to('/');
        }

        // Reglas de validación
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
                    'regex_match' => 'Por favor ingresa un número de WhatsApp válido'
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
                'rules' => 'required|in_list[si]',
                'errors' => [
                    'required' => 'Debes aceptar el consentimiento para continuar',
                    'in_list' => 'Debes aceptar el consentimiento para continuar'
                ]
            ]
        ];

        // Validaciones condicionales
        if ($this->request->getPost('mainGoal') === 'otro') {
            $validationRules['otherGoal'] = [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Debes especificar tu objetivo cuando seleccionas "Otro"',
                    'max_length' => 'La descripción del objetivo no puede exceder 100 caracteres'
                ]
            ];
        }

        if ($this->request->getPost('howDidYouKnow') === 'otro') {
            $validationRules['otherSource'] = [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => 'Debes especificar el medio cuando seleccionas "Otro"',
                    'max_length' => 'La descripción del medio no puede exceder 100 caracteres'
                ]
            ];
        }

        // Ejecutar validación
        if (!$this->validate($validationRules)) {
            // Si hay errores de validación, redirigir con errores
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Preparar datos para insertar
        $data = [
            'full_name' => trim($this->request->getPost('fullName')),
            'document_number' => $this->request->getPost('documentNumber') ?: null,
            'age' => (int) $this->request->getPost('age'),
            'email' => strtolower(trim($this->request->getPost('email'))),
            'whatsapp' => preg_replace('/[^0-9]/', '', $this->request->getPost('whatsapp')),
            'main_goal' => $this->request->getPost('mainGoal'),
            'other_goal' => $this->request->getPost('otherGoal') ?: null,
            'preferred_schedule' => $this->request->getPost('preferredSchedule'),
            'how_did_you_know' => $this->request->getPost('howDidYouKnow'),
            'other_source' => $this->request->getPost('otherSource') ?: null,
            'consent' => $this->request->getPost('consent'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'status' => 'active'
        ];

        try {
            // Intentar guardar en la base de datos
            $insertId = $this->frmGymModel->insert($data);

            if ($insertId) {
                // Registro exitoso
                $response = [
                    'success' => true,
                    'message' => '¡Registro exitoso! Te contactaremos pronto para coordinar tu primera sesión en KORPUS Training Club.',
                    'data' => [
                        'id' => $insertId,
                        'name' => $data['full_name'],
                        'email' => $data['email']
                    ]
                ];

                // Si es una petición AJAX, devolver JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON($response);
                }

                // Si no es AJAX, redirigir con mensaje de éxito
                return redirect()->to('/')->with('success', $response['message']);
            } else {
                throw new \Exception('No se pudo completar el registro');
            }
        } catch (\Exception $e) {
            // Error en el registro
            log_message('error', 'Error en registro de usuario: ' . $e->getMessage());

            $response = [
                'success' => false,
                'message' => 'Ocurrió un error al procesar tu registro. Por favor intenta nuevamente.',
                'error' => $e->getMessage()
            ];

            // Si es una petición AJAX, devolver JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Si no es AJAX, redirigir con mensaje de error
            return redirect()->back()->withInput()->with('error', $response['message']);
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
                                                           ->countAllResults()
        ];

        return $this->response->setJSON($stats);
    }
}
