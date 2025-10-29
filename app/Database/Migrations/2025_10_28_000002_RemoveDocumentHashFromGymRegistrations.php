<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDocumentHashFromGymRegistrations extends Migration
{
    public function up()
    {
        // Intentar eliminar la columna document_hash si existe
        try {
            $this->forge->dropColumn('gym_registrations', 'document_hash');
        } catch (\Exception $e) {
            // La columna no existe, continuar sin error
            log_message('info', 'Columna document_hash no existe o ya fue eliminada: ' . $e->getMessage());
        }
        
        // Modificar el campo document_number para agregar comentario
        try {
            $this->forge->modifyColumn('gym_registrations', [
                'document_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 12,
                    'null' => true,
                    'comment' => 'Número de documento (opcional, sin encriptar)'
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error modificando columna document_number: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // Restaurar la columna document_hash
        $this->forge->addColumn('gym_registrations', [
            'document_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'SHA256 hash del documento para seguridad',
                'after' => 'document_number'
            ]
        ]);
        
        // Remover comentario del campo document_number
        $this->forge->modifyColumn('gym_registrations', [
            'document_number' => [
                'type' => 'VARCHAR',
                'constraint' => 12,
                'null' => true
            ]
        ]);
    }
}