FROM php:8.3-fpm

# Instala dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpa o cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões do PHP necessárias para o Laravel e MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer mais recente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala o Node.js e NPM
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www

# ==========================================
# ADICIONE ESTAS LINHAS ABAIXO NO SEU ARQUIVO:
# ==========================================

# 1. Copia todos os arquivos do seu projeto para dentro do container
COPY . .

# 2. Instala as dependências do Composer (essencial para produção)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 3. Altera o comando padrão da imagem (FPM) para o servidor embutido do Laravel,
#    fazendo ele escutar dinamicamente a porta que o Railway exigir.
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}