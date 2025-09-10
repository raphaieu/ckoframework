FROM php:8.2-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && a2enmod rewrite \
    && a2enmod headers

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Criar usuário não-root
RUN groupadd -g 1000 cko && \
    useradd -u 1000 -g cko -m cko

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de configuração
COPY php.ini /usr/local/etc/php/
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Definir permissões
RUN chown -R cko:cko /var/www/html && \
    chmod -R 755 /var/www/html

# Mudar para usuário não-root
USER cko

# Expor porta
EXPOSE 80

# Comando padrão
CMD ["apache2-foreground"]
