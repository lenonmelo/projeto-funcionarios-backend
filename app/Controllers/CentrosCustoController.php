<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\library\ErrorsReturn;
use App\Library\JWTHandler;
use App\Models\CentroCusto;
use CodeIgniter\HTTP\Response;

class CentrosCustoController extends BaseController
{

    public function __construct()
    {
        // Carregue a biblioteca JWTHandler
        $jwtHandler = new JWTHandler();
        $token = str_replace("Bearer ", "", $_SERVER['HTTP_AUTHORIZATION']);
        $jwtHandler->autorizacao($token);
    }

    /**
     * Método index
     *
     * Este método é responsável por recuperar todos os centros de custo ativos existentes na base de dados.
     * 
     * Método HTTP: GET
     * URL: /api/centrosCusto
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        $centrosCustoModel = new CentroCusto();
        $centrosCusto = $centrosCustoModel->where('ativo', '1')->findAll();
        
        //Caso não conter nenhum centro de custo cadastrado na base, retorna a mensagem abaixo
        if (count($centrosCusto) == 0) {
              $centrosCusto = [];
        }

        return $this->response->setJSON(['data'=>$centrosCusto]);
    }

    /**
     * Método getAll
     *
     * Este método é responsável por recuperar todos os centros de custo ativos e inativos existentes na base de dados.
     * 
     * Método HTTP: GET
     * URL: /api/centrosCusto/getAll
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAll()
    {
        $centrosCustoModel = new CentroCusto();
        $centrosCusto = $centrosCustoModel->findAll();
        
        //Caso não conter nenhum centro de custo cadastrado na base, retorna a mensagem abaixo
        if (count($centrosCusto) == 0) {
            $centrosCusto = [];

        }

        return $this->response->setJSON(['data'=>$centrosCusto]);
    }

    /**
     * Método show
     *
     * Este método é responsável por recuperar um unico centro de custo conforme o id enviado via parametro.
     * 
     * Método HTTP: GET
     * URL: /api/centrosCusto/(id)
     * 
     * @param int $id O ID do centro de custo a ser recuperado.
     * @return \CodeIgniter\HTTP\Response
     */
    public function show($id = null)
    {
        $centrosCustoModel = new CentroCusto();
        $centroCusto = $centrosCustoModel->where('ativo', '1')->find($id);

        //Caso não exixtir centro de custo cadastrado com o id enviado, retorna a mensagem abaixo
        if (!$centroCusto) {
            $responseData = [
                'status' => 'error',
                'message' => 'Centro de custo não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        return $this->response->setJSON(['data' => $centroCusto]);
    }

    /**
     * Método create
     *
     * Este método é responsável por criar um novo centro de custo com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: POST
     * URL: /api/centrosCusto
     * 
     * Parâmetros (POST):
     * {
     *     "titulo": "Nome do Centro de Custo",
     * }
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function create()
    {
        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via POST
        $titulo = $arrayPuts->titulo;

        $centrosCustoModel = new CentroCusto();
        $data = [
            'titulo' => $titulo,
            'ativo' => '1'
        ];

        //Salva um novo centro de custo e retorna mensagem de sucesso
        if ($centrosCustoModel->save($data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Centro de Custo criado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $centrosCustoModel->errors(); // Obtenha os erros de validação

            $errosReturn = new ErrorsReturn();
            $responseData = [
                'errors' =>  $errosReturn->errors($errors)
            ];

            return $this->response->setStatusCode( Response::HTTP_UNPROCESSABLE_ENTITY )->setJSON($responseData);
        }
    }

    /**
     * Método update
     *
     * Este método é responsável por atualizar um centro de custo existente com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: PUT
     * URL: /api/centrosCusto/{id}
     * 
     * Parâmetros (PUT):
     * {
     *     "titulo": "Novo Nome do Centro de Custo",
     * }
     * 
     * @param int $id O ID do centro de custo a ser atualizado.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function update($id = null)
    {

        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via PUT
        $titulo = $arrayPuts->titulo;

        $centrosCustoModel = new CentroCusto();

        //Verifica se existe o centro de custo antes de ser alterado
        $centroCusto = $centrosCustoModel->where('ativo', '1')->find($id);
        if (!$centroCusto) {
            $responseData = [
                'status' => 'error',
                'message' => 'Centro de custo não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        //Seta o novo titulo de cargo para ser alterado
        $data = [
            'titulo' => $titulo,
        ];

        //Realiza a alteração do titulo do centro de custo conforme o id enviado
        if ($centrosCustoModel->update($id, $data)) {

            $responseData = [
                'status' => 'success',
                'message' => 'Centro de Custo alterado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $centrosCustoModel->errors();

            $errosReturn = new ErrorsReturn();
            $responseData = [
                'errors' =>  $errosReturn->errors($errors)
            ];

            return $this->response->setStatusCode( Response::HTTP_UNPROCESSABLE_ENTITY )->setJSON($responseData);
        }
    }

    /**
     * Método delete
     *
     * Este método é responsável por excluir(desativar) um centro de custo existente com base no ID fornecido.
     * 
     * Exemplo de uso:
     * Método HTTP: DELETE
     * URL: /api/centrosCusto/{id}
     * 
     * @param int $id O ID do centro de custo a ser excluído.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function delete($id = null)
    {
        $centrosCustoModel = new CentroCusto();

        //Verifica se existe o centro de custo antes de ser excluido
        $centroCusto = $centrosCustoModel->where('ativo', '1')->find($id);
        if (!$centroCusto) {
            $responseData = [
                'status' => 'error',
                'message' => 'Centro de custo não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        $data = [
            'ativo' => '0',
        ];
        
        //Realiza a desativação do centro de custo
        if ($centrosCustoModel->update($id, $data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Centro de Custo excluido com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        }
    }
}
