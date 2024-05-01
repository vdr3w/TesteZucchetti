# Teste Zucchetti-Andre Vieira

## Instruções de Instalação
1. 🛠️ **Baixar o Projeto**:
   - Acesse o repositório do GitHub onde o projeto está hospedado.
   - Clique no botão "Code" e escolha a opção desejada.
   - Extraia o arquivo ZIP em um diretório de sua escolha.
2. 🛠️ **Instalação das Dependências**:
   - Abra um terminal ou prompt de comando.
   - Navegue até o diretório onde você extraiu o projeto.
   - Selecione a pasta do projeto
   - Execute o seguinte comando para instalar todas as dependências necessárias:
     ```bash
     npm install
     ```
3. 🛠️ **Executando o Projeto**:
   - Uma vez que todas as dependências estejam instaladas, você pode iniciar o projeto de desenvolvimento com o seguinte comando:
     ```bash
     npm run dev
     ```
   - Após iniciar o servidor, você pode acessar o aplicativo em seu navegador usando o endereço: `http://localhost:5173/` (a menos que você tenha especificado uma porta diferente).
4. 🛠️ **Acessando o Projeto**:
   - Abra seu navegador e vá para `http://localhost:5173/` (ou a porta especificada).
   - Agora você deve ver a interface do projeto e poderá navegar e testar suas funcionalidades.
***
# Execução dos Scripts de Seed

Este guia explica como executar os scripts de inicialização do banco de dados (seed) para o projeto TESTEZUCCHETTI. Esses scripts são úteis para configurar o banco de dados com dados iniciais necessários para o funcionamento do sistema.

## Pré-requisitos

Antes de começar, certifique-se de que o PHP está instalado em seu sistema e que está acessível através do terminal ou PowerShell (o comando `php` deve estar no PATH do sistema). Você pode verificar isso executando:

```bash
php -v
```

Se o PHP não estiver instalado, instale-o a partir do site oficial: [PHP.net](https://www.php.net/)

## Instruções de Execução

1. **Navegue até o diretório de scripts**  
   Abra seu terminal ou PowerShell e navegue até o diretório de scripts do projeto:

   ```bash
   cd \TesteZucchetti\Zucchetti-BackEnd\src\scripts
   ```

2. **Execute o script de seed**  
   No diretório de scripts, há um arquivo chamado `run_seeds.php` que é responsável por executar todos os scripts de seed na ordem correta. Para executar este script, use o seguinte comando:

   ```bash
   php run_seeds.php
   ```

   Este comando irá iniciar a execução de cada script de seed, e você verá as mensagens de progresso no terminal.

## O que Esperar

Ao executar o `run_seeds.php`, os seguintes scripts serão executados em sequência, cada um criando uma parte específica dos dados iniciais necessários:

- `create_admin_user.php`: Cria um usuário administrador padrão no sistema.
- `create_customer.php`: Insere clientes iniciais no banco de dados.
- `create_paymentmethod.php`: Configura métodos de pagamento padrão.
- `create_product.php`: Adiciona produtos iniciais ao banco de dados.
- `create_sale.php`: Gera vendas de exemplo para teste inicial.

Cada script será executado com um intervalo de 5 segundos entre eles para garantir que o sistema não seja sobrecarregado durante a inicialização dos dados.

## Problemas Comuns

- **PHP não reconhecido**: Se o comando `php` não for reconhecido, certifique-se de que o PHP está instalado e corretamente adicionado ao PATH do seu sistema. Reinicie seu terminal após fazer alterações no PATH.
***
# Configuração do Banco de Dados

Este guia descreve como configurar e iniciar o banco de dados PostgreSQL para o projeto TESTEZUCCHETTI usando Docker.

## Pré-requisitos

Antes de iniciar, certifique-se de que o Docker está instalado e rodando em seu sistema. Você pode verificar isso executando:

```bash
docker -v
docker-compose -v
```

Isso exibirá as versões do Docker e do Docker Compose instaladas, indicando que eles estão prontos para uso.

## Configuração e Execução

1. **Navegue até o diretório do Docker Compose**  
   Abra seu terminal ou PowerShell e navegue até o diretório onde o arquivo `docker-compose.yml` está localizado:

   ```bash
   cd G:\TesteZucchetti\Zucchetti-BackEnd
   ```

2. **Inicie o banco de dados**  
   Execute o seguinte comando para construir e iniciar o serviço de banco de dados:

   ```bash
   docker-compose up --build
   ```

   Este comando irá baixar a imagem necessária do PostgreSQL, configurar o banco de dados com as variáveis de ambiente especificadas, e iniciar o serviço. Os dados do banco de dados serão persistidos no volume `postgres_data`.

## O que Esperar

Ao executar o comando `docker-compose up --build`, o Docker irá preparar e rodar uma instância do PostgreSQL com as seguintes características:

- **Nome do banco de dados**: `DBZucchetti`
- **Usuário**: `admin`
- **Senha**: `admin`

O serviço será acessível através da porta `5432` no seu computador local.

## Dicas Úteis

- **Parar o serviço**: Para parar o banco de dados, você pode usar o comando `docker-compose down` no mesmo diretório do arquivo `docker-compose.yml`.
- **Limpar dados**: Se precisar limpar os dados e começar novamente, você pode remover o volume com o comando `docker volume rm G:\TesteZucchetti\Zucchetti-BackEnd_postgres_data` após parar os serviços com `docker-compose down`.
- **Conexão ao Banco de Dados**: Para se conectar ao banco de dados usando um cliente como o DBeaver ou qualquer outro cliente de banco de dados, use as seguintes informações de conexão:
  - **Host**: `localhost` (ou o IP da máquina Docker, se estiver usando Docker Toolbox)
  - **Porta**: `5432`
  - **Usuário**: `admin`
  - **Senha**: `admin`
  - **Banco de Dados**: `DBZucchetti`

  Essas informações permitem que você estabeleça uma conexão direta ao banco de dados PostgreSQL. Certifique-se de ter o cliente de banco de dados instalado e simplesmente crie uma nova conexão usando esses detalhes.


## Problemas Comuns

- **Porta já em uso**: Se a porta 5432 já estiver sendo usada por outro serviço, você terá problemas para iniciar o PostgreSQL. Certifique-se de que a porta esteja livre antes de tentar iniciar o banco de dados.
