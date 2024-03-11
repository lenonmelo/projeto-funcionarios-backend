<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\library\ErrorsReturn;
use App\Library\JWTHandler;
use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Usuario;
use CodeIgniter\HTTP\Response;
use League\Csv\Reader;

class UsuariosController extends BaseController
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
     * Este método é responsável por recuperar todos os usuarios ativos existentes na base de dados.
     * 
     * Método HTTP: GET
     * URL: /api/usuarios
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        $usuariosModel = new Usuario();

        //Busca todos os departamentos com o seu respectivo centro de custo
        $usuarios = $usuariosModel->buscaUsuarios()->getResultArray();

        //Caso não conter nenhum departamento cadastrado na base, retorna a mensagem abaixo
        if (count($usuarios) == 0) {
            $responseData = [
                'status' => 'error',
                'message' => 'Nenhum usuario encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON(['data' => $responseData]);
        }

        return $this->response->setJSON(['data' => $usuarios]);
    }

    /**
     * Método searchUsuariosByDepartamentos
     *
     * Este método é responsável por recuperar todos os usuarios ativos por departamentos;
     * 
     * 
     * Método HTTP: GET
     * URL: /api/usuarios/searchUsuariosByDepartamentos/{id do departamento}
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function searchUsuariosByDepartamentos($departamentos_id = null)
    {
        $usuariosModel = new Usuario();

        //Busca todos os usuarios conforme o centro de custo recebido
        $usuarios = $usuariosModel->buscaUsuarios(0, $departamentos_id)->getResultArray();

        //Caso não conter nenhum usuarios cadastrado na base com o respectivo departamento, retorna a mensagem abaixo
        if (count($usuarios) == 0) {
            $usuarios = [];
        }

        return $this->response->setJSON(['data' => $usuarios]);
    }

    /**
     * Método show
     *
     * Este método é responsável por recuperar um unico usuarios conforme o id enviado via parametro.
     * 
     * Método HTTP: GET
     * URL: /api/usuarios/(id)
     * 
     * @param int $id O ID do usuario a ser recuperado.
     * @return \CodeIgniter\HTTP\Response
     */
    public function show($id = null)
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscaUsuarios($id)->getRowArray();

        //Caso não exixtir usuario cadastrado com o id enviado, retorna a mensagem abaixo
        if (!$usuario) {
            $responseData = [
                'status' => 'error',
                'message' => 'Usuario não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        return $this->response->setJSON(['data' => $usuario]);
    }

    /**
     * Método create
     *
     * Este método é responsável por criar um novo usuario com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: POST
     * URL: /api/usuarios
     * 
     * Parâmetros (POST):
     * {
     *     "nome": "Nome do usuário",
     *     "email": "Email do usuário",
     *     "senha": "Senha para a criação do novo usuário"
     *     "departamento_id": Id do departamente do usuário
     *     "cargo_id": Id do cargo do usuário
     * }
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function create()
    {

        $arrayPuts = $this->request->getJSON();

        // Obtenha os parâmetros via POST
        $nome = $arrayPuts->nome;
        $email = $arrayPuts->email;
        $senha = $arrayPuts->senha;
        $confirmar_senha = $arrayPuts->confirmar_senha;
        $departamento_id = $arrayPuts->departamento_id;
        $cargo_id = $arrayPuts->cargo_id;

        //Realizando a validação do departamento incorreto
        if ($departamento_id) {
            $departamentoModel = new Departamento();
            $departamento = $departamentoModel->where('ativo', '1')->find($departamento_id);

            //Caso não exixtir departamento cadastrado com o id enviado, retorna a mensagem abaixo
            if (!$departamento) {
                $responseData = [
                    'status' => 'error',
                    'message' => 'O parâmetro departamento_id esta incorreto, não foi encontrado o respectivo departamento'
                ];

                return $this->response->setStatusCode(404)->setJSON($responseData);
            }
        }

        //Realizando a validação do cargo incorreto
        if ($cargo_id) {
            $cargoModel = new Cargo();
            $cargo = $cargoModel->where('ativo', '1')->find($cargo_id);

            //Caso não exixtir cargo cadastrado com o id enviado, retorna a mensagem abaixo
            if (!$cargo) {
                $responseData = [
                    'status' => 'error',
                    'message' => 'O parâmetro cargo_id esta incorreto, não foi encontrado o respectivo cargo'
                ];

                return $this->response->setStatusCode(404)->setJSON($responseData);
            }
        }
        $usuarioModel = new Usuario();

        //validação de e-mail ja existente no sistema
        $usuarioExiste = $usuarioModel->where('ativo', '1')->where('email', $email)->find();

        //Caso já exixtir usuario cadastrado com o email enviado, retorna a mensagem abaixo
        if ($usuarioExiste) {
            $responseData = [
                'errors' => ['email' => ['Já existe um usuário com o e-mail escolhido']]
            ];

            return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
        }

        //Validação da confirmação da senha
        if ($senha && $confirmar_senha) {
            if ($senha != $confirmar_senha) {
                $responseData = [
                    'errors' => ['confirmar_senha' => ["Os campos Senha e CSonfirmar senha não conferêm"]]
                ];

                return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
            }
        }

        //Dados para o novo usuario
        $data = [
            'nome' => $nome,
            'email' => $email,
            'cargo_id' => $cargo_id,
            'departamento_id' => $departamento_id,
            'ativo' => '1'
        ];
        if ($senha) {
            $data['senha'] = password_hash($senha, PASSWORD_DEFAULT);
            $data['confirmar_senha'] = $senha;
        }
        //Salva um novo usuario e retorna mensagem de sucesso
        if ($usuarioModel->save($data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Usuario criado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $usuarioModel->errors(); // Obtenha os erros de validação

            $errosReturn = new ErrorsReturn();
            $responseData = [
                'errors' =>  $errosReturn->errors($errors)
            ];

            return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
        }
    }

    /**
     * Método update
     *
     * Este método é responsável por atualizar um usuário existente com base nos parâmetros fornecidos.
     * 
     * Exemplo de uso:
     * Método HTTP: PUT
     * URL: /api/usuarios/{id}
     * 
     * Parâmetros (PUT):
     * {
     *     "nome": "Nome do usuário",
     *     "email": "Email do usuário",
     *     "senha": "Senha para a criação do novo usuário"
     *     "departamento_id": Id do departamente do usuário
     *     "cargo_id": Id do cargo do usuário
     * }
     * 
     * @param int $id O ID do departamento a ser atualizado.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function update($id = null)
    {

        $objectPuts = $this->request->getJSON();

        // Obtenha os parâmetros via PUT
        $nome = $objectPuts->nome;
        $email = $objectPuts->email;
        $senha = $objectPuts->senha;
        $confirmar_senha = $objectPuts->confirmar_senha;
        $departamento_id = $objectPuts->departamento_id;
        $cargo_id = $objectPuts->cargo_id;

        //Realizando a validação do departamento incorreto
        if ($departamento_id) {
            $departamentoModel = new Departamento();
            $departamento = $departamentoModel->where('ativo', '1')->find($departamento_id);

            //Caso não exixtir departamento cadastrado com o id enviado, retorna a mensagem abaixo
            if (!$departamento) {
                $responseData = [
                    'status' => 'error',
                    'message' => 'O parâmetro departamento_id esta incorreto, não foi encontrado o respectivo departamento'
                ];

                return $this->response->setStatusCode(404)->setJSON($responseData);
            }
        }

        //Realizando a validação do cargo incorreto
        if ($cargo_id) {
            $cargoModel = new Cargo();
            $cargo = $cargoModel->where('ativo', '1')->find($cargo_id);

            //Caso não exixtir cargo cadastrado com o id enviado, retorna a mensagem abaixo
            if (!$cargo) {
                $responseData = [
                    'status' => 'error',
                    'message' => 'O parâmetro cargo_id esta incorreto, não foi encontrado o respectivo cargo'
                ];

                return $this->response->setStatusCode(404)->setJSON($responseData);
            }
        }

        $usuarioModel = new Usuario();

        //Verifica se existe o usuario antes de ser alterado
        $usuario = $usuarioModel->where('ativo', '1')->find($id);
        if (!$usuario) {
            $responseData = [
                'status' => 'error',
                'message' => 'Usuario não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        //Caso o e-mail enviado for diferente do e-mail já existente do usuario, é realizado a validação do email novamente
        if ($usuario['email'] != $email) {

            //validação de e-mail ja existente no sistema
            $usuarioExiste = $usuarioModel->where('ativo', '1')->where('email', $email)->find();

            //Caso já exixtir usuario cadastrado com o email enviado, retorna a mensagem abaixo
            if ($usuarioExiste) {

                $responseData = [
                    'errors' => ['email' => ['Já existe um usuário com o e-mail escolhido']]
                ];

                return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
            }
        }

        //Validação da confirmação da senha
        if ($senha && $confirmar_senha) {
            if ($senha != $confirmar_senha) {
                $responseData = [
                    'errors' => ['confirmar_senha' => ["Os campos Senha e CSonfirmar senha não conferêm"]]
                ];

                return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
            }
        }

        //Dados para a alteração do usuário
        $data = [
            'nome' => $nome,
            'email' => $email,
            'cargo_id' => $cargo_id,
            'departamento_id' => $departamento_id,
            'ativo' => '1'
        ];
        if ($senha) {
            $data['senha'] = password_hash($senha, PASSWORD_DEFAULT);
            $data['confirmar_senha'] = $confirmar_senha;
        }

        //Realiza a alteração do usuário
        if ($usuarioModel->update($id, $data)) {

            $responseData = [
                'status' => 'success',
                'message' => 'Usuário alterado com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        } else {
            //Caso ocorra um erro de validação, retorna o erro encontrado
            $errors = $departamentoModel->errors();

            $errosReturn = new ErrorsReturn();
            $responseData = [
                'errors' =>  $errosReturn->errors($errors)
            ];

            return $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->setJSON($responseData);
        }
    }

    /**
     * Método delete
     *
     * Este método é responsável por excluir(desativar) um usuario existente com base no ID fornecido.
     * 
     * Exemplo de uso:
     * Método HTTP: DELETE
     * URL: /api/usuarios/{id}
     * 
     * @param int $id O ID do usuario a ser excluído.
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function delete($id = null)
    {
        $usuarioModel = new Usuario();

        //Verifica se existe o usuario antes de ser excluido
        $usuario = $usuarioModel->where('ativo', '1')->find($id);
        if (!$usuario) {
            $responseData = [
                'status' => 'error',
                'message' => 'Usuário não encontrado'
            ];

            return $this->response->setStatusCode(404)->setJSON($responseData);
        }

        $data = [
            'ativo' => '0',
        ];

        //Realiza a desativação do usuário
        if ($usuarioModel->update($id, $data)) {
            $responseData = [
                'status' => 'success',
                'message' => 'Usuário excluido com sucesso',
                'data' => $data
            ];

            return $this->response->setJSON($responseData);
        }
    }

    /**
     * Método import
     *
     * Este método é responsável por importar uma lista usuário de um arquivo .csv.
     * 
     * Exemplo de uso:
     * Método HTTP: PUT
     * URL: /api/usuarios/importar
     * 
     * O arquivo deverá ter o seguinte formato
     * (cabeçalho) "Nome";"Email";"Senha";"Cargo";"Departamento"
     * (conteúdos) "usuario1";"usario1@teste.com.br";"123456";"Programador";"Soluções digitais"
     * 
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $arquivo 
     *
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function import()
    {

        //Model de usuários
        $usuarioModel = new Usuario();

        //Captura o arquivo
        $arquivo = $this->request->getFile('arquivo');

        //Realiza a validação de realizou o POST corretamente
        if ($arquivo->isValid() && !$arquivo->hasMoved()) {

            //Captura o caminho temporário 
            $caminhoTemporario = $arquivo->getTempName();

            //Captura os dados do arquivo para serem validados abaixo
            $csv = Reader::createFromPath($caminhoTemporario, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0); // Ignora a primeira linha (cabeçalho)
            $valoresCsv = iterator_to_array($csv->getRecords());

            //Variavel para capturar e retornar os erros encontrados
            $erros = [];

            //MOdels para as validações de cada linha
            $cargoModel = new Cargo();
            $departamentoModel = new Departamento();

            //Percorre todas as linhas e realiza as validações necessárias
            foreach ($valoresCsv as $linha => $valores) {

                //Variavel para capturar os erros encontrados de cada linha
                $errosLinha = [];

                //validação de e-mail ja existente no sistema
                $usuarioExiste = $usuarioModel->where('ativo', '1')->where('email', $valores['Email'])->first();

                //Caso já exixtir usuario cadastrado com o email enviado, retorna a mensagem abaixo
                if ($usuarioExiste) {
                    $errosLinha[] = [
                        "linha" => ($linha + 1),
                        'message' => "E-mail " . $valores['Email'] . " já existe no sistema"
                    ];
                }

                //Realizando a validação do cargo incorreto
                $cargo = $cargoModel->where('ativo', '1')->where('titulo', mb_convert_encoding($valores['Cargo'], 'UTF-8', 'ISO-8859-1'))->first();

                //Caso não exixtir cargo cadastrado com o titulo enviado, retorna a mensagem abaixo
                if (!$cargo) {
                    $errosLinha[] = [
                        "linha" => ($linha + 1),
                        'message' => "O cargo " . mb_convert_encoding($valores['Cargo'], 'UTF-8', 'ISO-8859-1') . " não existe no sistema"
                    ];
                } else {
                    $cargo_id = $cargo['id'];
                }

                //Realizando a validação do departamento incorreto
                $departamento = $departamentoModel->where('ativo', '1')->where('titulo', mb_convert_encoding($valores['Departamento'], 'UTF-8', 'ISO-8859-1'))->first();

                //Caso não exixtir departamento cadastrado com o titulo enviado, retorna a mensagem abaixo
                if (!$departamento) {
                    $errosLinha[] = [
                        "linha" => ($linha + 1),
                        'message' => "O departamento " . mb_convert_encoding($valores['Departamento'], 'UTF-8', 'ISO-8859-1') . " não existe no sistema"
                    ];
                } else {
                    $departamento_id = $departamento['id'];
                }

                //Caso não for encontrado nenhum erro na linha, os dados serão salvos na base
                if (count($errosLinha) == 0) {

                    //Dados para a inclusão do usuário
                    $data = [
                        'nome' => mb_convert_encoding($valores['Nome'], 'UTF-8', 'ISO-8859-1'),
                        'email' => mb_convert_encoding($valores['Email'], 'UTF-8', 'ISO-8859-1'),
                        'senha' => password_hash(mb_convert_encoding($valores['Senha'], 'UTF-8', 'ISO-8859-1'), PASSWORD_DEFAULT),
                        'cargo_id' => $cargo_id,
                        'departamento_id' => $departamento_id,
                        'ativo' => '1'
                    ];

                    //Salva um novo usuário
                    if (!$usuarioModel->save($data)) {

                        //Caso ocorra um erro de validação, retorna o erro encontrado
                        $errors = $usuarioModel->errors(); // Obtenha os erros de validação

                        $erros[] = [
                            "linha" => ($linha + 1),
                            'message' => $errors
                        ];
                    }
                } else {
                    $erros[] = $errosLinha;
                }
            }
        }

        $responseData = [
            'status' => 'success',
            'message' => 'Usuários com informações corretas foram importados com sucesso',
            'erros' => $erros
        ];

        return $this->response->setJSON($responseData);
    }
}
