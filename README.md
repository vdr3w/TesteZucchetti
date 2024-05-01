![Zucchetti](https://www.zucchettibrasil.com.br/templates/website/img/logo.png)

# Teste Zucchetti - André Vieira

Bem-vindo ao meu projeto Teste Zucchetti! Abaixo você encontrará instruções detalhadas sobre como configurar e executar tanto o frontend quanto o backend deste projeto. 

Inicialmente, apresentarei um aviso sobre minha tentativa de dockerização da aplicação. 

Após isso, fornecerei um passo a passo detalhado para iniciar e configurar o projeto sem utilizar a dockerização, garantindo que tudo funcione perfeitamente.


## 🚧 Aviso sobre Dockerização

Durante o desenvolvimento, tentei dockerizar completamente a aplicação, incluindo tanto o backend quanto o frontend. Consegui levantar os servidores para ambos, e o backend estava respondendo às requisições normalmente(mesmo que com erro, por conta do schema nao aplicado). No entanto, apesar do servidor frontend indicar que estava operacional, não foi possível acessar a aplicação pelo navegador, como se o serviço estivesse inacessível.

Além disso, não consegui implementar uma forma automatizada de rodar os comandos do Doctrine SchemaTool para migração do banco de dados dentro do Docker. Portanto, a parte da dockerização ficou incompleta. Deixo claro que os servidores de backend e frontend foram iniciados, mas as funcionalidades completas não estavam disponíveis através desta configuração.

Essas mudanças estão disponíveis apenas na branch `feat-docker-setup`. Para acessar e ver como ficou minha tentativa de dockerização, é necessário mudar para essa branch.

### Dockerfiles e docker-compose.yml

- **Frontend Dockerfile** (Localizado em `\TesteZucchetti\Zucchetti-BackEnd\Dockerfile`):
  ```dockerfile
  FROM node:16
  WORKDIR /app
  RUN apt-get update && apt-get install -y git make g++
  COPY package*.json ./
  RUN npm cache clean --force
  RUN rm -rf node_modules package-lock.json
  RUN npm install
  COPY . .
  EXPOSE 5173
  CMD ["npm", "run", "dev"]
  ```

- **Backend Dockerfile** (Localizado em `\TesteZucchetti\Zucchetti-FrontEnd\Dockerfile`):
  ```dockerfile
  FROM php:8.3-apache
  RUN apt-get update and apt-get install -y git unzip libpq-dev libzip-dev netcat-openbsd \
      && docker-php-ext-install pdo_pgsql zip \
      && apt-get clean and rm -rf /var/lib/apt/lists/*
  COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
  WORKDIR /var/www/html
  COPY composer.json composer.lock ./
  RUN composer install --no-interaction --optimize-autoloader --no-plugins
  COPY . .
  COPY entrypoint.sh /usr/local/bin/entrypoint.sh
  RUN chmod +x /usr/local/bin/entrypoint.sh
  EXPOSE 8000
  ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
  ```

- **docker-compose.yml** (Localizado em `\TesteZucchetti\docker-compose.yml`):
  ```yaml
  version: '3.8'
  services:
    frontend:
      build: ./Zucchetti-FrontEnd
      ports:
        - "5173:5173"
      volumes:
        - ./Zucchetti-FrontEnd:/app
        - node_modules:/app/node_modules
    backend:
      build: ./Zucchetti-BackEnd
      ports:
        - "8000:8000"
      volumes:
        - ./Zucchetti-BackEnd:/var/www/html
      depends_on:
        - postgres
    postgres:
      image: postgres:latest
      restart: always
      environment:
        POSTGRES_DB: DBZucchetti
        POSTGRES_USER: admin
        POSTGRES_PASSWORD: admin
      ports:
        - "5432:5432"
      volumes:
        - postgres_data:/var/lib/postgresql/data
  volumes:
    postgres_data:
    node_modules:
  ```

***
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
   cd seu-diretorio\TesteZucchetti\Zucchetti-BackEnd
   ```

2. Execute o seguinte comando para construir e iniciar o serviço de banco de dados:

   ```bash
   docker-compose up --build
   ```

   Isso configurará o banco de dados com as variáveis de ambiente necessárias e iniciará o serviço. Os dados serão persistidos no volume `postgres_data`.

### Dicas Úteis

- Para parar o banco de dados, use o comando `docker-compose down`.
- Se precisar limpar os dados e começar de novo, remova o volume com o comando `docker volume rm  seu-diretorio\TesteZucchetti\Zucchetti-BackEnd_postgres_data`.
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
  cd  seu-diretorio\TesteZucchetti\Zucchetti-BackEnd\src\scripts
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
