<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdicionarUsuarioAdmin extends Seeder
{
    public function run()
    {
        $data = [
            'nome' => 'Usuário Administrador',
            'email' => 'admin@teste.com.br',
            'senha' => password_hash('admin', PASSWORD_DEFAULT),
            'administrador' => '1'
        ];

        // Using Query Builder
        $this->db->table('usuarios')->insert($data);
    }
}
