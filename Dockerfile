FROM php:8.3-fpm

# Instala dependências do sistema (incluindo netcat para o healthcheck do banco)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    netcat-openbsd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuração GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer e Node
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node:20-slim /usr/local/bin /usr/local/bin
COPY --from=node:20-slim /usr/local/lib/node_modules /usr/local/lib/node_modules

WORKDIR /var/www

# Copia arquivos e ajusta permissões
COPY . .
RUN chown -R www-data:www-data /var/www

# Instala dependências
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build

# Configuração do Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copia a configuração customizada do PHP-FPM para aceitar conexões TCP na porta 9000
COPY local.ini /usr/local/etc/php-fpm.d/zz-local.ini

# Define o script como CMD
CMD ["/usr/local/bin/entrypoint.sh"]