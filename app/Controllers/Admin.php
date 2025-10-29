<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FrmGymModel;
use Hermawan\DataTables\DataTable;

class Admin extends BaseController
{
    private $adminPassword = 'KorpusAdmin2024!'; // Cambiar por una contraseña segura
    
    public function __construct()
    {
        helper(['form', 'url', 'session']);
    }
    
    /**
     * Página de login del administrador
     */
    public function login()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (session('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('admin/login');
    }
    
    /**
     * Procesar autenticación del administrador
     */
    public function authenticate()
    {
        $password = $this->request->getPost('password');
        
        if ($password === $this->adminPassword) {
            // Establecer sesión de administrador
            session()->set([
                'admin_logged_in' => true,
                'admin_login_time' => time()
            ]);
            
            return redirect()->to('/admin/dashboard')->with('success', '¡Bienvenido al panel de administración!');
        } else {
            return redirect()->back()->with('error', 'Contraseña incorrecta');
        }
    }
    
    /**
     * Dashboard principal con DataTable
     */
    public function dashboard()
    {
        // Verificar autenticación
        if (!$this->isAuthenticated()) {
            return redirect()->to('/admin/login');
        }
        
        return view('admin/dashboard');
    }
    
    /**
     * API endpoint para DataTables
     */
    public function datatables()
    {
        // Verificar autenticación para API
        if (!$this->isAuthenticated()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }
        
        $model = new FrmGymModel();
        
        // Configurar DataTables
        $builder = $model->select([
            'id',
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
            'created_at'
        ]);
        
        return DataTable::of($builder)
            ->addNumbering('no') // Columna de numeración
            ->edit('consent', function($row) {
                return $row->consent === 'si' ? 
                    '<span class="badge bg-success">Sí</span>' : 
                    '<span class="badge bg-danger">No</span>';
            })
            ->edit('created_at', function($row) {
                return date('d/m/Y H:i:s', strtotime($row->created_at));
            })
            ->edit('whatsapp', function($row) {
                if ($row->whatsapp) {
                    return '<a href="https://wa.me/57' . preg_replace('/\D/', '', $row->whatsapp) . '" target="_blank" class="text-success">
                        <i class="fab fa-whatsapp me-1"></i>' . $row->whatsapp . '</a>';
                }
                return '-';
            })
            ->edit('email', function($row) {
                return '<a href="mailto:' . $row->email . '" class="text-primary">' . $row->email . '</a>';
            })
            ->edit('main_goal', function($row) {
                $goals = [
                    'lose_weight' => 'Bajar de peso',
                    'gain_muscle' => 'Aumentar masa muscular',
                    'improve_health' => 'Mejorar mi salud',
                    'tone_up' => 'Tonificar',
                    'other' => $row->other_goal ?: 'Otro'
                ];
                
                return $goals[$row->main_goal] ?? $row->main_goal;
            })
            ->edit('how_did_you_know', function($row) {
                $sources = [
                    'social_media' => 'Redes sociales',
                    'friend_referral' => 'Recomendación de amigo',
                    'google_search' => 'Búsqueda en Google',
                    'advertisement' => 'Publicidad',
                    'other' => $row->other_source ?: 'Otro'
                ];
                
                return $sources[$row->how_did_you_know] ?? $row->how_did_you_know;
            })
            ->add('actions', function($row) {
                return '
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="viewDetails(' . $row->id . ')" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRecord(' . $row->id . ')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->toJson(true);
    }
    
    /**
     * Ver detalles de un registro
     */
    public function view($id)
    {
        if (!$this->isAuthenticated()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }
        
        $model = new FrmGymModel();
        $record = $model->find($id);
        
        if (!$record) {
            return $this->response->setJSON(['error' => 'Registro no encontrado'])->setStatusCode(404);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $record
        ]);
    }
    
    /**
     * Eliminar un registro
     */
    public function delete($id)
    {
        if (!$this->isAuthenticated()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }
        
        $model = new FrmGymModel();
        
        if ($model->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar el registro'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Exportar datos a CSV
     */
    public function export()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->to('/admin/login');
        }
        
        $model = new FrmGymModel();
        $records = $model->findAll();
        
        // Configurar headers para descarga
        $this->response->setHeader('Content-Type', 'application/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="registros_korpus_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers del CSV
        fputcsv($output, [
            'ID',
            'Nombre Completo',
            'Documento',
            'Edad',
            'Email',
            'WhatsApp',
            'Objetivo Principal',
            'Objetivo Personalizado',
            'Horario Preferido',
            'Como nos conoció',
            'Fuente Personalizada',
            'Consentimiento',
            'Fecha de Registro'
        ]);
        
        // Datos
        foreach ($records as $record) {
            fputcsv($output, [
                $record['id'],
                $record['full_name'],
                $record['document_number'] ?: 'No proporcionado',
                $record['age'],
                $record['email'],
                $record['whatsapp'],
                $this->getGoalLabel($record['main_goal']),
                $record['other_goal'] ?: '-',
                $record['preferred_schedule'],
                $this->getSourceLabel($record['how_did_you_know']),
                $record['other_source'] ?: '-',
                $record['consent'] === 'si' ? 'Sí' : 'No',
                date('d/m/Y H:i:s', strtotime($record['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Cerrar sesión de administrador
     */
    public function logout()
    {
        session()->remove(['admin_logged_in', 'admin_login_time']);
        session()->setFlashdata('success', 'Sesión cerrada correctamente');
        return redirect()->to('/admin/login');
    }
    
    /**
     * Verificar si el administrador está autenticado
     */
    private function isAuthenticated()
    {
        if (!session('admin_logged_in')) {
            return false;
        }
        
        // Verificar tiempo de sesión (2 horas)
        $loginTime = session('admin_login_time');
        if (time() - $loginTime > 7200) { // 2 horas
            session()->remove(['admin_logged_in', 'admin_login_time']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Obtener etiqueta legible del objetivo
     */
    private function getGoalLabel($goal)
    {
        $goals = [
            'lose_weight' => 'Bajar de peso',
            'gain_muscle' => 'Aumentar masa muscular',
            'improve_health' => 'Mejorar mi salud',
            'tone_up' => 'Tonificar',
            'other' => 'Otro'
        ];
        
        return $goals[$goal] ?? $goal;
    }
    
    /**
     * Obtener etiqueta legible de la fuente
     */
    private function getSourceLabel($source)
    {
        $sources = [
            'social_media' => 'Redes sociales',
            'friend_referral' => 'Recomendación de amigo',
            'google_search' => 'Búsqueda en Google',
            'advertisement' => 'Publicidad',
            'other' => 'Otro'
        ];
        
        return $sources[$source] ?? $source;
    }
}