# Imagem base do Node.js com Alpine por ser mais leve
FROM node:16-alpine

# Define o diretório de trabalho no container
WORKDIR /app

# Copia os arquivos de configuração do projeto
COPY package*.json ./

# Instala as dependências do projeto
RUN npm install

# Copia os arquivos restantes do projeto
COPY . .

# Define a porta que o servidor irá expor
EXPOSE 8080

# Comando para executar o aplicativo
CMD ["npm", "run", "serve"]
