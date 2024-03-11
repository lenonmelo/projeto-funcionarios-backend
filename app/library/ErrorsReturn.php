<?php

namespace App\library;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ErrorsReturn
{
    private $chave_token = "kjaskasjkasjdshdjshkhasjhasjhsalaslajdhdjshsdk";

    /**
     * Realiza o tratamento e formata a mensagem da maneira especifica para mostrar no front
     *
     * @param string $error Erray com os erros encontrados.
     *
     * @return array
     */
    public function errors($errors)
    {
        try {
            $retorno = [];
            foreach($errors AS $field => $error){
                $retorno[$field][] =  $error;
            }
            return $retorno;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Verifica a autorização com base em um token JWT.
     *
     * @param string $token O token JWT a ser verificado.
     *
     * @return void Esta função termina a execução do script e envia uma resposta JSON com o status e a mensagem apropriados.
     */
    public function autorizacao($token)
    {
        // Configura o cabeçalho para indicar que a resposta é JSON
        header('Content-Type: application/json');

        //Obrigatoriedade do parametro token_acesso
        if (!$token) {
            $responseData = [
                'status' => 'error',
                'message' => 'Token de acesso é obrigatório'
            ];
            http_response_code(404);
            echo json_encode($responseData);
            die;
        }

        //Realiza a validação do token
        $decodedToken = $this->validarToken($token);

        //Caso a validação retornar falso mostrar a mensagem abaixo
        if (!$decodedToken) {
            $responseData = [
                'status' => 'error',
                'message' => 'Erro na autenticação do token'
            ];
            http_response_code(404);
            echo json_encode($responseData);
            die;
        }

        //Caso o token é inválido mostrar a mensagem abaixo
        if ($decodedToken == "INVALID") {

            $responseData = [
                'status' => 'error',
                'message' => 'Tokem invalido'
            ];
            http_response_code(401);
            echo json_encode($responseData);
            die;
        }

        //Caso o token esta expirado mostrar a mensagem abaixo
        if ($decodedToken == "EXPIRED") {

            $responseData = [
                'status' => 'error',
                'message' => 'Tokem expirou'
            ];
            http_response_code(401);
            echo json_encode($responseData);
            die;
        }
    }
}
