# Usando uma imagem base do Node.js mais padrão
FROM node:16

# Configuração do diretório de trabalho
WORKDIR /app

# Instalação das ferramentas necessárias
RUN apt-get update && apt-get install -y git make g++

# Copiando arquivos de definição de pacote
COPY package*.json ./

# Limpar o cache e instalar dependências
RUN npm cache clean --force
RUN rm -rf node_modules package-lock.json
RUN npm install

# Copiando os arquivos de fonte do projeto
COPY . .

# Expondo a porta 5173
EXPOSE 5173

# Comando para rodar o servidor de desenvolvimento do Vite
CMD ["npm", "run", "dev"]
