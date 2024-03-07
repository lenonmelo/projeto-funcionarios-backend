<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartamentosTable extends Migration
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
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'centro_custo_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true
            ],
            'ativo' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'default' => '1'
            ],
        ]);

        $this->forge->addForeignKey('centro_custo_id', 'centros_custo', 'id');
        $this->forge->addKey('id', true);
        $this->forge->createTable('departamentos');
    }

    public function down()
    {
        $this->forge->dropTable('departamentos');
    }
}
