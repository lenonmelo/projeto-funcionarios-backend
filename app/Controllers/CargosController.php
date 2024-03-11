<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\library\ErrorsReturn;
use App\Library\JWTHandler;
use App\Models\Cargo;
use CodeIgniter\HTTP\Response;

class CargosController extends BaseController
{

    public function __construct()
    {
        // Carregue a biblioteca JWTHandler
        $jwtHandler = new JWTHandler();
        $token = str_replace("Bearer ", "", $_SERVER['HTTP_AUTHORIZATION']) ;
        $jwtHandler->autorizacao($token);
    }

    /**
     * Método index
     *
     * Este método é responsável por recuperar todos os cargos ativos existentes na base de dados.
     * 
     * Método HTTP: GET
     * URL: /api/cargos
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        $cargoModel = new Cargo();
        $cargos = $cargoModel->where('ativo', '1')->findAll();

        //Caso não conter nenhum cargo cadastrado na base, retorna a mensagem abaixo
        if (count($cargos) == 0) {
            $responseData = [
                'status' => 'error',
                'message' => 'Nenhum cargo encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        return $this->response->setJSON(['data' => $cargos]);
    }

    /**
     * Método show
     *
     * Este método é responsável por recuperar um unico cargo conforme o id enviado via parametro.
     * 
     * Método HTTP: GET
     * URL: /api/cargos/(id)
     * 
     * @param int $id O ID do cargo a ser recuperado.
     * @return \CodeIgniter\HTTP\Response
     */
    public function show($id = null)
    {
        $cargoModel = new Cargo();
        $cargo = $cargoModel->where('ativo', '1')->find($id);

        //Caso não exixtir cargo cadastrado com o id enviado, retorna a mensagem abaixo
        if (!$cargo) {
            $responseData = [
                'status' => 'error',
                'message' => 'Cargo não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        return $this->response->setJSON(['data'=>$cargo]);
    }

    /**
     * Método create
     *
     * Este método é responsável por criar um novo cargo com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: POST
     * URL: /api/cargos
     * 
     * Parâmetros (POST):
     * {
     *     "titulo": "Nome do cargo",
     * }
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function create()
    {
        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via POST
        $titulo = $arrayPuts->titulo;
        $cargoModel = new Cargo();
        $data = [
            'titulo' => $titulo,
            'ativo' => '1'
        ];

        //Salva um novo cargo e retorna mensagem de sucesso
        if ($cargoModel->save($data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Cargo criado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $cargoModel->errors(); // Obtenha os erros de validação
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
     * Este método é responsável por atualizar um cargo existente com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: PUT
     * URL: /api/cargos/{id}
     * 
     * Parâmetros (PUT):
     * {
     *     "titulo": "Novo Nome do cargo",
     * }
     * 
     * @param int $id O ID do cargo a ser atualizado.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function update($id = null)
    {

        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via PUT
        $titulo = $arrayPuts->titulo;

        $cargoModel = new Cargo();

        //Verifica se existe o cargo antes de ser alterado
        $cargo = $cargoModel->where('ativo', '1')->find($id);
        if (!$cargo) {
            $responseData = [
                'status' => 'error',
                'message' => 'Cargo não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        //Seta o novo titulo de cargo para ser alterado
        $data = [
            'titulo' => $titulo,
        ];

        //Realiza a alteração do titulo do cargo conforme o id enviado
        if ($cargoModel->update($id, $data)) {

            $responseData = [
                'status' => 'success',
                'message' => 'Cargo alterado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $cargoModel->errors();
            
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
     * Este método é responsável por excluir(desativar) um cargo existente com base no ID fornecido.
     * 
     * Exemplo de uso:
     * Método HTTP: DELETE
     * URL: /api/cargos/{id}
     * 
     * @param int $id O ID do cargo a ser excluído.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function delete($id = null)
    {
        $cargoModel = new Cargo();

        //Verifica se existe o cargo antes de ser excluido
        $cargo = $cargoModel->where('ativo', '1')->find($id);
        if (!$cargo) {
            $responseData = [
                'status' => 'error',
                'message' => 'Cargo não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        $data = [
            'ativo' => '0',
        ];

        //Realiza a desativação do cargo
        if ($cargoModel->update($id, $data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Cargo excluido com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        }
    }
}
