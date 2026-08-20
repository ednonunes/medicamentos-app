#!/bin/sh
set -e

# Executa as migrações sempre no deploy
echo "Executando migrações do banco de dados..."
php /var/www/artisan migrate --force

# Cria o link do storage
echo "Criando link do storage..."
php /var/www/artisan storage:link --force

# Verifica se existe a variável PORT para iniciar o servidor interno ou o PHP-FPM
if [ -n "$PORT" ]; then
    echo "Iniciando servidor PHP na porta $PORT..."
    exec php -S 0.0.0.0:$PORT -t /var/www/public
else
    echo "Iniciando PHP-FPM padrão..."
    exec php-fpm
fi