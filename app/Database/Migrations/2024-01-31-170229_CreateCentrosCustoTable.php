<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCentrosCustoTable extends Migration
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
            'ativo' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'default' => '1'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('centros_custo');
    }

    public function down()
    {
        $this->forge->dropTable('centros_custo');
    }
}
