FROM php:8.3-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libjpeg62-turbo-dev \
    libfreetype6-dev

# Limpa o cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuração GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js
COPY --from=node:20-slim /usr/local/bin /usr/local/bin
COPY --from=node:20-slim /usr/local/lib/node_modules /usr/local/lib/node_modules

WORKDIR /var/www

# Permissões para o usuário www-data (importante para Miget/PaaS)
RUN chown -R www-data:www-data /var/www

COPY . .

# Instala dependências
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build

# Ajuste crítico: garante que o script de entrypoint seja executado pelo PHP-FPM
# Garante que o PHP-FPM rode em primeiro plano
CMD ["php-fpm"]