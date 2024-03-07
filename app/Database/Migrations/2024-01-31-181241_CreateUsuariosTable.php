<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'senha' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'token_acesso' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'administrador' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'default' => '0'
            ],
            'cargo_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true
            ],
            'departamento_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true
            ],
            'ativo' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'default' => '1'
            ],
        ]);

        $this->forge->addForeignKey('cargo_id', 'cargos', 'id');
        $this->forge->addForeignKey('departamento_id', 'departamentos', 'id');
        $this->forge->addKey('id', true);
        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
