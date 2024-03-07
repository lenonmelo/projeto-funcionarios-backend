<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\JWTHandler;
use App\Models\CentroCusto;
use App\Models\Departamento;
use CodeIgniter\HTTP\ResponseInterface;

class DepartamentosController extends BaseController
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
     * Este método é responsável por recuperar todos os departamentos ativos existentes na base de dados.
     * 
     * Método HTTP: GET
     * URL: /api/departamentos
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        $departamentoModel = new Departamento();

        //Busca todos os departamentos com o seu respectivo centro de custo
        $departamentos = $departamentoModel->departamentosCentroCusto()->getResultArray();

        //Caso não conter nenhum departamento cadastrado na base, retorna a mensagem abaixo
        if (count($departamentos) == 0) {
            $responseData = [
                'status' => 'error',
                'message' => 'Nenhum departamento encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        return $this->response->setJSON(['data' => $departamentos]);
    }
    
    /**
     * Método getAll
     *
     * Este método é responsável por recuperar todos os departamentos ativos e inativos existentes na base de dados.
     * 
     * Método HTTP: GET
     * URL: /api/departamentos/getAll
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAll()
    {
        $departamentoModel = new Departamento();

        $departamentos = $departamentoModel->findAll();
        
        //Caso não conter nenhum centro de custo cadastrado na base, retorna a mensagem abaixo
        if (count($departamentos) == 0) {
            $departamentos = [];

        }

        return $this->response->setJSON(['data'=>$departamentos]);
    }

     /**
     * Método show
     *
     * Este método é responsável por recuperar um unico departamento conforme o id enviado via parametro.
     * 
     * Método HTTP: GET
     * URL: /api/departamentos/(id)
     * 
     * @param int $id O ID do departamento a ser recuperado.
     * @return \CodeIgniter\HTTP\Response
     */
    public function show($id = null)
    {
        $departamentoModel = new Departamento();

        //Busca o departamento conforme o iD enviado via parametro
        $departamento = $departamentoModel->departamentosCentroCusto($id)->getRowArray();

        //Caso não exixtir departamento cadastrado com o id enviado, retorna a mensagem abaixo
        if (!$departamento) {
            $responseData = [
                'status' => 'error',
                'message' => 'Departamento não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        return $this->response->setJSON(["data" => $departamento]);
    }

    /**
     * Método create
     *
     * Este método é responsável por criar um novo departamento com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: POST
     * URL: /api/departamentos
     * 
     * Parâmetros (POST):
     * {
     *     "titulo": "Nome do departamento",
     *     "centro_custo_id": ID do centro de custo
     * }
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function create()
    {

        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via POST
        $titulo = $arrayPuts->titulo;
        $centro_custo_id = $arrayPuts->centro_custo_id;
        
        //Realizando a validação do centro de custo incorreto
        $centrosCustoModel = new CentroCusto();
        $centroCusto = $centrosCustoModel->where('ativo', '1')->find($centro_custo_id);

        //Caso não exixtir centro de custo cadastrado com o id enviado, retorna a mensagem abaixo
        if (!$centroCusto) {
            $responseData = [
                'status' => 'error',
                'message' => 'O parâmetro centro_custo_id esta incorreto, não foi encontrado o respectivo centro de custo'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        $departamentoModel = new Departamento();

        //Dados para o novo departamento
        $data = [
            'titulo' => $titulo,
            'centro_custo_id' => $centro_custo_id,
            'ativo' => '1'
        ];

        //Salva um novo departamento e retorna mensagem de sucesso
        if ($departamentoModel->save($data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Departamento criado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $departamentoModel->errors(); // Obtenha os erros de validação

            $responseData = [
                'status' => 'error',
                'message' => 'Erro na validação',
                'errors' => $errors
            ];

            return $this->response->setStatusCode(400)->setJSON($responseData);
        }
    }

    /**
     * Método update
     *
     * Este método é responsável por atualizar um departamento existente com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: PUT
     * URL: /api/departamentos/{id}
     * 
     * Parâmetros (PUT):
     * {
     *    "titulo": "Nome do departamento",
     *    "centro_custo_id": ID do centro de custo
     * }
     * 
     * @param int $id O ID do departamento a ser atualizado.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function update($id = null)
    {

        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via PUT
        $titulo = $arrayPuts->titulo;
        $centroCustoId = $arrayPuts->centro_custo_id;

         //Realizando a validação do centro de custo incorreto
         $centrosCustoModel = new CentroCusto();
         $centroCusto = $centrosCustoModel->where('ativo', '1')->find($centroCustoId);
 
         //Caso não exixtir centro de custo cadastrado com o id enviado, retorna a mensagem abaixo
         if (!$centroCusto) {
             $responseData = [
                 'status' => 'error',
                 'message' => 'O parâmetro centro_custo_id esta incorreto, não foi encontrado o respectivo centro de custo'
             ];
 
             return $this->response->setStatusCode(404)->setJSON($responseData);
         }

        $departamentoModel = new Departamento();

        //Verifica se existe o departamento antes de ser alterado
        $departamento = $departamentoModel->where('ativo', '1')->find($id);
        if (!$departamento) {
            $responseData = [
                'status' => 'error',
                'message' => 'Departamento não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        //Seta o novo titulo de departamento para ser alterado
        $data = [
            'titulo' => $titulo,
            'centro_custo_id' => $centroCustoId,
        ];

        //Realiza a alteração do titulo do departamento e o seu centro de custo conforme o id enviado
        if ($departamentoModel->update($id, $data)) {

            $responseData = [
                'status' => 'success',
                'message' => 'Departamento alterado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $departamentoModel->errors();

            $responseData = [
                'status' => 'error',
                'message' => 'Erro na validação',
                'errors' => $errors
            ];

            return $this->response->setStatusCode(400)->setJSON($responseData);
        }
    }

    /**
     * Método delete
     *
     * Este método é responsável por excluir(desativar) um departamento existente com base no ID fornecido.
     * 
     * Exemplo de uso:
     * Método HTTP: DELETE
     * URL: /api/departamentos/{id}
     * 
     * @param int $id O ID do departamento a ser excluído.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function delete($id = null)
    {
        $departamentoModel = new Departamento();

        //Verifica se existe o departamento antes de ser excluido
        $departamento = $departamentoModel->where('ativo', '1')->find($id);
        if (!$departamento) {
            $responseData = [
                'status' => 'error',
                'message' => 'Departamento não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        $data = [
            'ativo' => '0',
        ];

        //Realiza a desativação do departamento
        if ($departamentoModel->update($id, $data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Departamento excluido com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        }
    }

    /**
     * Método searchDepartamentosByCentroCusto
     *
     * Este método é responsável por recuperar todos os departamentos ativos por centro de custo;
     * 
     * 
     * Método HTTP: GET
     * URL: /api/departamentos/searchDepartamentosByCentroCusto/{id do centro de custo}
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function searchDepartamentosByCentroCusto($centro_custo_id = null)
    {
        $departamentoModel = new Departamento();

        //Busca todos os departamentos conforme o centro de custo recebido
        $departamentos = $departamentoModel->departamentosCentroCusto(0, $centro_custo_id)->getResultArray();

        //Caso não conter nenhum departamentos cadastrado na base com o respectivo centro de custo, retorna a mensagem abaixo
        if (count($departamentos) == 0) {
            $departamentos = [];
        }

        return $this->response->setJSON(['data'=>$departamentos]);
    }
}
