FROM node:18-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    curl \
    git

# Criar usuário não-root
RUN addgroup -g 1001 cko && \
    adduser -D -s /bin/sh -u 1001 -G cko cko

# Configurar diretório de trabalho
WORKDIR /app

# Copiar package.json
COPY package*.json ./

# Instalar dependências globalmente
RUN npm install -g vite @vitejs/plugin-vue

# Criar diretório e definir permissões
RUN mkdir -p /app && \
    chown -R cko:cko /app

# Mudar para usuário não-root
USER cko

# Expor porta
EXPOSE 3000

# Comando padrão (será sobrescrito pelo docker-compose)
CMD ["npm", "run", "dev"]
