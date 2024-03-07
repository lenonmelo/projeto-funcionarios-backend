<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    private $chave_token = "kjaskasjkasjdshdjshkhasjhasjhsalaslajdhdjshsdk";

    /**
     * Valida um token JWT.
     *
     * @param string $token O token JWT a ser validado.
     *
     * @return string|bool Retorna uma string indicando o estado do token:
     *                     - "VALID" se o token for válido.
     *                     - "EXPIRED" se o token estiver expirado.
     *                     - "INVALID" se o token for inválido devido ao número errado de segmentos.
     *                     - false se ocorrer um erro não especificado durante a validação.
     */
    public function validarToken($token)
    {
        try {
            $dadosDecodificados = JWT::decode($token, new Key($this->chave_token, 'HS256'));
            if ($dadosDecodificados) {
                return "VALID";
            }
        } catch (\Exception $e) {
            //Caso o toke estiver expirado retorna o termo EXPIRED
            if ($e->getMessage() == 'Expired token') {
                return "EXPIRED";
            } else {
                //Caso o toke for invalido, retorna o terno INVALID
                if ($e->getMessage() == "Wrong number of segments") {
                    return "INVALID";
                }

                return false;
            }
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
