# Teste Zucchetti - André Vieira

Bem-vindo ao meu projeto Teste Zucchetti! Abaixo você encontrará instruções detalhadas sobre como configurar e executar tanto o frontend quanto o backend deste projeto. Siga estes passos para garantir que tudo funcione perfeitamente.

## 🗃️ Configuração do Banco de Dados

Antes de começar a trabalhar com o backend, é importante configurar o banco de dados PostgreSQL usando Docker.

### Pré-requisitos

Certifique-se de que o Docker está instalado e funcionando em seu sistema. Você pode verificar isso com os comandos:

```bash
docker -v
docker-compose -v
```

### Configuração e Execução

1. Navegue até o diretório onde está localizado o `docker-compose.yml`:

   ```bash
   cd G:\TesteZucchetti\Zucchetti-BackEnd
   ```

2. Execute o seguinte comando para construir e iniciar o serviço de banco de dados:

   ```bash
   docker-compose up --build
   ```

   Isso configurará o banco de dados com as variáveis de ambiente necessárias e iniciará o serviço. Os dados serão persistidos no volume `postgres_data`.

### Dicas Úteis

- Para parar o banco de dados, use o comando `docker-compose down`.
- Se precisar limpar os dados e começar de novo, remova o volume com o comando `docker volume rm G:\TesteZucchetti\Zucchetti-BackEnd_postgres_data`.
- Para conectar-se ao banco de dados usando um cliente como o DBeaver, utilize as seguintes informações:
  - **Host**: `localhost`
  - **Porta**: `5432`
  - **Usuário**: `admin`
  - **Senha**: `admin`
  - **Banco de Dados**: `DBZucchetti`

## 💻 Instruções de Instalação BACKEND

Agora que o banco de dados está pronto, vamos configurar o backend.

### Baixar o Projeto

- Acesse o repositório do GitHub onde o projeto está hospedado.
- Clique no botão "Code" e escolha "Download ZIP".
- Extraia o arquivo ZIP em um diretório de sua escolha.

### Instalação das Dependências

- Abra um terminal ou prompt de comando.
- Navegue até o diretório do backend extraído.
- Execute o comando abaixo para instalar todas as dependências necessárias:

  ```bash
  composer install
  ```

### Executando o Projeto

- Configure as variáveis de ambiente no arquivo `.env`.
- Inicie o servidor de desenvolvimento com o comando:

  ```bash
  php -S localhost:8000 -t public
  ```

- Acesse `http://localhost:8000/` para ter acesso à API.

## 🛠️ Gerenciamento do Banco de Dados com Doctrine ORM

Vamos gerenciar o esquema do banco de dados utilizando o Doctrine SchemaTool.

### Comandos de Migração

- Para aplicar as migrações ao banco de dados:

  ```bash
  vendor/bin/doctrine orm:schema-tool:update --force --complete
  ```

- Para visualizar o SQL das migrações:

  ```bash
  vendor/bin/doctrine orm:schema-tool:update --dump-sql --complete
  ```

### Dicas Úteis

- Sempre faça um backup do banco de dados antes de executar migrações que alterem o esquema.

## 🌱 Execução dos Scripts de Seed

Para configurar o banco de dados com dados iniciais:

### Instruções de Execução

- Navegue até o diretório de scripts:

  ```bash
  cd \TesteZucchetti\Zucchetti-BackEnd\src\scripts
  ```

- Execute o arquivo `run_seeds.php`:

  ```bash
  php run_seeds.php
  ```

### O que Esperar

Os scripts criam dados iniciais como usuários, clientes, métodos de pagamento, produtos e vendas.

## 🌐 Instruções de Instalação FRONTEND

Finalmente, configure o frontend do projeto.

### Baixar o Projeto e Instalar Dependências

- Acesse o diretório do frontend e execute:

  ```bash
  npm install
  ```

### Executando o Projeto

- Inicie o servidor de desenvolvimento:

  ```bash
  npm run dev
  ```

- Acesse `http://localhost:5173/` para visualizar o frontend do projeto.
