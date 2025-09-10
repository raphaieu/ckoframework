FROM nginx:alpine

# Criar usuário não-root
RUN addgroup -g 1000 cko && \
    adduser -D -s /bin/sh -u 1000 -G cko cko

# Copiar configuração do Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Criar diretório de trabalho
RUN mkdir -p /var/www/html && \
    chown -R cko:cko /var/www/html && \
    chown -R cko:cko /var/cache/nginx && \
    chown -R cko:cko /var/log/nginx && \
    chown -R cko:cko /etc/nginx/conf.d && \
    touch /var/run/nginx.pid && \
    chown -R cko:cko /var/run/nginx.pid

# Mudar para usuário não-root
USER cko

# Expor porta
EXPOSE 80

# Comando padrão
CMD ["nginx", "-g", "daemon off;"]
