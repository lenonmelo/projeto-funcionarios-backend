
# Projeto Funcionários
### Visão Geral
Este projeto visa simplificar e otimizar a gestão de funcionários/usuários de forma eficaz.

# Desenvolvimento
Para o desenvolvimento da aplicação foram utilizadas as seguintes tecnologias web.

* PHP versão 7.4.33;
* Framework Codeigniter versão 4.4.5
* Biblioteca firebase/php-jwt versão 6.10
* Biblioteca league/csv versão 9.8
* Mysql versão 15.1
   
# Referências da API
A aplicação frontend desse projeto se encontra neste link: https://github.com/lenonmelo/projeto-funcionarios-frontend

# Rodar a aplicação
Para executar a aplicação, é necessário que o ambiente/máquina tenha as seguintes aplicações instaladas e configuradas:
* PHP 7.4.33, instalado e configurado nas variáveis de ambiente do sistema;
<br><b>OBS:</b> Você pode instalá-lo por meio de aplicações com ambientes prontos, como Xampp ou Wampp.
* Composer: Será utilizado para a instalação dos pacotes necessários.
* MySQL 15.X ou Maria DB instalado e executando.
* Já ter criado no seu banco de dados a base para ser utilizada no projeto.
  
Após concluir as instalações necessárias, siga estas etapas:
* Clonar o projeto na pasta que pretende executar o sistema;
* Acesse a pasta principal do sistema através do terminal.
* Execute o seguinte comando para instalar os pacotes necessários;

      composer install

* Acesse o arquivo "App.php" contido na pasta "/app/config/" e inclua na variável $baseURL o endereço que irá rodar a aplicação conforme mostrado abaixo.

      public string $baseURL = 'http://localhost:8080';

* Acesse o arquivo "Database.php" contido na pasta "/app/config/" e inclua nas configurações os dados do seu banco de dados MYSQL, conforme mostrado abaixo.

        public array $default = [
            'DSN'          => '',
            'hostname'     => 'localhost',
            'username'     => 'root',
            'password'     => '',
            'database'     => 'projeto_funcionarios',
            'DBDriver'     => 'MySQLi',
            'DBPrefix'     => '',
            'pConnect'     => false,
            'DBDebug'      => true,
            'charset'      => 'utf8',
            'DBCollat'     => 'utf8_general_ci',
            'swapPre'      => '',
            'encrypt'      => false,
            'compress'     => false,
            'strictOn'     => false,
            'failover'     => [],
            'port'         => 3306,
            'numberNative' => false,
        ];
    
* Acesse a pasta principal do sistema através do terminal e execute o seguinte comando para criar as tabelas necessárias para o projeto.
  
        php spark migrate
        
* As tabelas serão criadas conforme o MER abaixo.

  ![MER](https://github.com/lenonmelo/projeto-funcionarios-backend/blob/main/MER.png?raw=true)


* Além disso, execute o seguinte comando para executar a seed, que incluirá os dados do usuário admin na tabela de usuários.

      php spark db:seed

* Agora, mais uma vez, através do terminal, na pasta principal do sistema, execute o seguinte comando para iniciar o servidor de teste:

      php spark serve 

* Acesse o endereço local do servidor no seu navegador, conforme exibido no terminal.
      
      http://localhost:8080

# Arquivo de apoio

*Coleção do Postman com os endpoints utilizados na aplicação.

[Postman Collection](https://github.com/lenonmelo/projeto-funcionarios-backend/blob/main/Projeto.postman_collection.json)


# Acesso

* O usuário e senha padrão de teste são as seguintes:

      Usuário: admin@teste.com.br
      Senha: admin
