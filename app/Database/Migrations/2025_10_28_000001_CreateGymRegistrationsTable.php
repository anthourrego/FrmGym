<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGymRegistrationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'document_number' => [
                'type' => 'VARCHAR',
                'constraint' => 12,
                'null' => true,
                'comment' => 'Número de documento (opcional)'
            ],
            'age' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'unsigned' => true,
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'main_goal' => [
                'type' => 'ENUM',
                'constraint' => ['bajar_peso', 'aumentar_masa', 'mejorar_salud', 'tonificar', 'otro'],
                'null' => false,
            ],
            'other_goal' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'preferred_schedule' => [
                'type' => 'ENUM',
                'constraint' => ['manana', 'medio_dia', 'tarde_noche'],
                'null' => false,
            ],
            'how_did_you_know' => [
                'type' => 'ENUM',
                'constraint' => ['redes_sociales', 'amigo_familiar', 'publicidad_local', 'otro'],
                'null' => false,
            ],
            'other_source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'consent' => [
                'type' => 'ENUM',
                'constraint' => ['si', 'no'],
                'null' => false,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'comment' => 'IP del usuario para auditoria'
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'User agent para auditoria'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'contacted', 'member', 'inactive'],
                'default' => 'active',
                'null' => false,
                'comment' => 'Estado del registro'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('main_goal');
        $this->forge->addKey('preferred_schedule');
        $this->forge->addKey('created_at');
        $this->forge->addKey('status');
        
        $this->forge->createTable('gym_registrations');
    }

    public function down()
    {
        $this->forge->dropTable('gym_registrations');
    }
}