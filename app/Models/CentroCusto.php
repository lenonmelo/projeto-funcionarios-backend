<?php

namespace App\Models;

use CodeIgniter\Model;

class CentroCusto extends Model
{
    protected $table            = 'centros_custo';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    
    protected $allowedFields    = ['titulo', 'ativo'];

    
    //Não usar campos padrões de dates
    protected $useTimestamps = false;
    
    // Validações
    protected $validationRules      = [
        'titulo' => 'required',
        'ativo' => 'required|in_list[0, 1]'
    ];
    protected $validationMessages   = [
        'titulo' => [
            'required' => 'O parâmetro titulo é obrigatorio'
        ],
        'ativo' => [
            'in_list' => 'O campo ativo dever ser 0 ou 1'
        ]
    ];

    // Executar as validações das regras acima
    protected $skipValidation       = false;

}
