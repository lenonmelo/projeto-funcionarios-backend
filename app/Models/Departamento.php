<?php

namespace App\Models;

use CodeIgniter\Model;

class Departamento extends Model
{
    protected $table            = 'departamentos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $allowedFields    = ['titulo', 'centro_custo_id', 'ativo'];

    //Não usar campos padrões de dates
    protected $useTimestamps = false;

    // Validações
    protected $validationRules      = [
        'titulo' => 'required',
        'centro_custo_id' => 'required',
        'ativo' => 'required|in_list[0, 1]'
    ];
    protected $validationMessages   = [
        'titulo' => [
            'required' => 'O parâmetro titulo é obrigatorio'
        ],
        'centro_custo_id' => [
            'required' => 'O parâmetro centro_custo_id é obrigatorio'
        ],
        'ativo' => [
            'in_list' => 'O campo ativo dever ser 0 ou 1'
        ]
    ];

    // Executar as validações das regras acima
    protected $skipValidation       = false;

    /**
     * Retorna informações sobre os departamentos associados aos centros de custo.
     *
     *
     * @param int $id (Opcional) O ID do departamento desejado. Se não fornecido, todos os departamentos serão retornados.
     *
     * @return CodeIgniter\Database\BaseResult|null
     *
     * Exemplo de retorno:
     * ```
     * [
     *     {
     *         "id": "2",
     *         "titulo": "Desenvolvimento de Software",
     *         "centro_custo_id": "6",
     *         "ativo": "1",
     *         "centro_custo": "TI - Tecnologia da Informação"
     *     },
     *     // ... outros registros, se houver
     * ]
     * ```
     */
    public function departamentosCentroCusto($id = 0, $centro_custo_id = 0)
    {
        $db = db_connect();

        //Montando a qery de busca com a relação de departamentos com centros de cutos
        $builder = $db->table('departamentos d');
        $builder->select('d.id, d.titulo, d.centro_custo_id, d.ativo, cc.titulo AS centro_custo');
        $builder->join('centros_custo cc', 'd.centro_custo_id = cc.id');
        $builder->where('d.ativo', '1');

        //Caso for passado o parametro ID, é realizado a consulta para buscar somente um departamento
        if ($id) {
            $builder->where('d.id', $id);
        }

        //Caso for passado o parametro centro_custo_id, é realizado a consulta dos departamentos pelo centro de custo enviado
        if ($centro_custo_id) {
            $builder->where('d.centro_custo_id', $centro_custo_id);
        }

        $query = $builder->get();

        return $query;
    }
}
