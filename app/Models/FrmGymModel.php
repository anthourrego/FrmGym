<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * FrmGymModel - Modelo para gestión de registros del gimnasio KORPUS
 * 
 * Maneja toda la lógica de datos relacionada con el registro de miembros,
 * incluyendo validaciones, estadísticas y operaciones CRUD.
 */
class FrmGymModel extends Model
{
    protected $table = 'gym_registrations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'full_name',
        'document_number',
        'age',
        'email',
        'whatsapp',
        'main_goal',
        'other_goal',
        'preferred_schedule',
        'how_did_you_know',
        'other_source',
        'consent',
        'ip_address',
        'user_agent',
        'status',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'full_name' => 'required|min_length[2]|max_length[50]',
        'age' => 'required|integer|greater_than_equal_to[16]|less_than_equal_to[80]',
        'email' => 'required|valid_email|max_length[100]|is_unique[gym_registrations.email]',
        'whatsapp' => 'required|max_length[20]',
        'main_goal' => 'required|in_list[bajar_peso,aumentar_masa,mejorar_salud,tonificar,otro]',
        'preferred_schedule' => 'required|in_list[manana,medio_dia,tarde_noche]',
        'how_did_you_know' => 'required|in_list[redes_sociales,amigo_familiar,publicidad_local,otro]',
        'consent' => 'required|in_list[si,no]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este correo electrónico ya está registrado.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Obtener registros por objetivo
     */
    public function getByGoal(string $goal)
    {
        return $this->where('main_goal', $goal)->findAll();
    }

    /**
     * Obtener registros por horario preferido
     */
    public function getBySchedule(string $schedule)
    {
        return $this->where('preferred_schedule', $schedule)->findAll();
    }

    /**
     * Obtener estadísticas de registro
     */
    public function getRegistrationStats()
    {
        $stats = [];
        
        // Estadísticas por objetivo
        $stats['by_goal'] = $this->select('main_goal, COUNT(*) as count')
                                ->groupBy('main_goal')
                                ->findAll();
        
        // Estadísticas por horario
        $stats['by_schedule'] = $this->select('preferred_schedule, COUNT(*) as count')
                                    ->groupBy('preferred_schedule')
                                    ->findAll();
        
        // Estadísticas por fuente
        $stats['by_source'] = $this->select('how_did_you_know, COUNT(*) as count')
                                  ->groupBy('how_did_you_know')
                                  ->findAll();
        
        // Estadísticas por consentimiento
        $stats['by_consent'] = $this->select('consent, COUNT(*) as count')
                                   ->groupBy('consent')
                                   ->findAll();
        
        // Total de registros
        $stats['total'] = $this->countAll();
        
        // Registros por mes
        $stats['by_month'] = $this->select('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                                 ->groupBy('month')
                                 ->orderBy('month', 'DESC')
                                 ->limit(12)
                                 ->findAll();
        
        return $stats;
    }

    /**
     * Buscar registros por email o documento
     */
    public function searchUser(string $search)
    {
        return $this->groupStart()
                        ->like('email', $search)
                        ->orLike('document_number', $search)
                        ->orLike('full_name', $search)
                    ->groupEnd()
                    ->findAll();
    }

    /**
     * Verificar si un email ya existe
     */
    public function emailExists(string $email): bool
    {
        return $this->where('email', $email)->countAllResults() > 0;
    }

    /**
     * Obtener usuarios que han dado consentimiento para marketing
     */
    public function getUsersWithConsent(): array
    {
        return $this->where('consent', 'si')->findAll();
    }

    /**
     * Obtener usuarios que NO han dado consentimiento para marketing
     */
    public function getUsersWithoutConsent(): array
    {
        return $this->where('consent', 'no')->findAll();
    }

    /**
     * Obtener estadísticas de consentimiento
     */
    public function getConsentStats(): array
    {
        $total = $this->countAll();
        $withConsent = $this->where('consent', 'si')->countAllResults();
        $withoutConsent = $this->where('consent', 'no')->countAllResults();

        return [
            'total_registrations' => $total,
            'with_consent' => $withConsent,
            'without_consent' => $withoutConsent,
            'consent_percentage' => $total > 0 ? round(($withConsent / $total) * 100, 2) : 0
        ];
    }
}