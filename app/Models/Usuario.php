<?php

namespace App\Models;

use CodeIgniter\Model;

class Usuario extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $allowedFields    = ['nome', 'email', 'senha', 'token_acesso', 'departamento_id', 'cargo_id', 'administrador', 'ativo'];

    //Não usar campos padrões de dates
    protected $useTimestamps = false;

    // Validações
    protected $validationRules      = [
        'nome' => 'required',
        'email' => 'required',
        'senha' => 'required',
        'confirmar_senha' => 'required',
        'cargo_id' => 'required',
        'departamento_id' => 'required',
        
        'ativo' => 'required|in_list[0, 1]'
    ];
    protected $validationMessages   = [
        'nome' => [
            'required' => 'O campo Nome é obrigatorio'
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatorio'
        ],
        'senha' => [
            'required' => 'O campo Senha é obrigatorio'
        ],
        'confirmar_senha' => [
            'required' => 'O campo Confirmar Senha é obrigatorio'
        ],
        'cargo_id' => [
            'required' => 'O campo Cargo é obrigatorio'
        ],
        'departamento_id' => [
            'required' => 'O campo Departamento é obrigatorio'
        ],
        'ativo' => [
            'in_list' => 'O campo ativo dever ser 0 ou 1'
        ]
    ];

    /**
     * Retorna informações sobre os usuarios associados aos departamentos e cargos.
     *
     *
     * @param int $id (Opcional) O ID do usuario desejado. Se não fornecido, todos os usuarios serão retornados.
     *
     * @return CodeIgniter\Database\BaseResult|null
     *
     * Exemplo de retorno:
     * ```
     * [
     *     {
     *          "id": "2",
     *          "nome": "Usuario teste",
     *          "email": "teste@teste.com.br",
     *          "senha": "$2y$10$Ub1MOc7J1Igg/2xcY3U2tuBRLRdu9tiOMSNI07n14t9OYWKD6EeyK",
     *          "cargo_id": "1",
     *          "cargo": "Cargo 1",
     *          "departamento_id": "7",
     *          "departamento": "Departamento 6",
     *          "centro_custo": "Centro de Custo 01",
     *          "token_acesso": null,
     *          "ativo": "1"
     *      }
     *     // ... outros registros, se houver
     * ]
     * ```
     */
    public function buscaUsuarios($id = 0, $departamento_id = 0)
    {
        $db = db_connect();

        //Montando a query de busca com a relação de usuarios com departamentos, cargos e centros de cutos
        $builder = $db->table('usuarios u');
        $builder->select('u.id, u.nome, u.email, u.senha, u.cargo_id, c.titulo AS cargo, u.departamento_id, d.titulo AS departamento, cc.titulo AS centro_custo, u.token_acesso, u.ativo');
        $builder->join('cargos c', 'u.cargo_id = c.id');
        $builder->join('departamentos d', 'u.departamento_id = d.id');
        $builder->join('centros_custo cc', 'd.centro_custo_id = cc.id');
        $builder->where('u.ativo', '1');

        //Caso for passado o parametro ID, é realizado a consulta para buscar somente um usuarios
        if ($id) {
            $builder->where('u.id', $id);
        }

        //Caso for passado o parametro departamento_id, é realizado a consulta dos usuarios pelo departamento enviado
        if ($departamento_id) {
            $builder->where('u.departamento_id', $departamento_id);
        }

        $query = $builder->get();

        return $query;
    }
}
