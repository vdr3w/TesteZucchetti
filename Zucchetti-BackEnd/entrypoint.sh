#!/bin/bash

# Esperar o PostgreSQL ficar disponível
echo "Aguardando o PostgreSQL ficar disponível..."
while ! nc -z postgres 5432; do
  sleep 1
done

echo "PostgreSQL está disponível."

# Gerar e aplicar o esquema do banco de dados
php vendor/bin/doctrine orm:schema-tool:update --dump-sql --complete
php vendor/bin/doctrine orm:schema-tool:update --force --complete

# Iniciar o servidor Apache em modo de desenvolvimento
php -S 0.0.0.0:8000 -t public
