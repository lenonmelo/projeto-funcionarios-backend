<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Usuario;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    use ResponseTrait;
    
    private $chave_token = "kjaskasjkasjdshdjshkhasjhasjhsalaslajdhdjshsdk";

    public function login()
    {
        $objectPuts = $this->request->getJSON();

        $email = $objectPuts->email;
        $senha = $objectPuts->password;
  
        $usuarioModel = new Usuario();

        //Caso não encontrado o email, retorna a mensagem abaixo
        if ($email == "" || !isset($email)) {
            $responseData = [
                'errors' => ['email' => ["O campo E-mail é obrigatorio"]]
            ];

            return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
        }

        //Caso não encontrado o senha, retorna a mensagem abaixo
        if ($senha == "" || !isset($senha)) {
            $responseData = [
                'errors' => ['password' => ["O campo Senha é obrigatorio"]]
            ];

            return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
        }
        
        //validação de e-mail ja existente no sistema
        $verificaEmail = $usuarioModel->where('ativo', '1')->where('email', $email)->first();
        
         //Caso não encontrado o email, retorna a mensagem abaixo
         if (!$verificaEmail) {
            $responseData = [
                'errors' => ['email' => ['E-mail ' . $email." não existe no sistema"]]
            ];

            return $this->response->setStatusCode( Response::HTTP_UNPROCESSABLE_ENTITY )->setJSON($responseData);
        }
        
         //Caso já exixtir usuario cadastrado com o email enviado, retorna a mensagem abaixo
         if (!password_verify($senha, $verificaEmail['senha'])) {
            $responseData = [
                'errors' => ['email' => ['E-mail ou senha incorreta']]
            ];

            return $this->response->setStatusCode( Response::HTTP_UNPROCESSABLE_ENTITY )->setJSON($responseData);
        }
        
        $dadosUsuario = [
            'id' => $verificaEmail["id"],
            'nome' => $verificaEmail["nome"],
            'email' => $verificaEmail["email"],
            'exp' =>  time() + 60 * 60,
            'iat' => time()
        ];
        
        $token = JWT::encode($dadosUsuario, $this->chave_token, 'HS256');
        
        $usuarioModel->update($verificaEmail['id'], ['token_acesso' => $token]);

        $responseData = [
            'status' => 'success',
            'message' => 'Login realizado com sucesso',
            'data' => ['token' =>  $token ]
        ];

        return $this->response->setJSON($responseData);

        
    }

}
