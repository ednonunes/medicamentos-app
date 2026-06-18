FROM php:8.3-fpm

# Instala dependências do sistema (com suporte a fontes e imagens para o Dompdf)
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

# Limpa o cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# CONFIGURAÇÃO DO GD: Ativa suporte a JPEG e Freetype para geração de PDFs
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer mais recente copiando da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================================================
# O PULO DO GATO: Copia o Node.js e NPM da imagem oficial!
# =========================================================
COPY --from=node:20-slim /usr/local/bin /usr/local/bin
COPY --from=node:20-slim /usr/local/lib/node_modules /usr/local/lib/node_modules

WORKDIR /var/www

# Copia todos os arquivos do seu projeto para dentro do container
COPY . .

# Instala as dependências do Composer para Produção
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instala as dependências do Node e compila os arquivos do Vite
RUN npm install
RUN npm run build

# Mantém o comando padrão da imagem base (php-fpm na porta 9000)
CMD ["php-fpm"]