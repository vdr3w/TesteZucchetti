# Usando uma imagem base do PHP com Apache
FROM php:8.3-apache

# Instalação de pacotes essenciais e extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalação do Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuração do diretório de trabalho
WORKDIR /var/www/html

# Instalação de dependências do Composer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-plugins

# Copiando o resto do código-fonte
COPY . .

# Copiando e definindo o entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expondo a porta 8000
EXPOSE 8000

# Definindo o entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
