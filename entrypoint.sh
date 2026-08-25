#!/bin/sh
set -e

# Se estiver rodando localmente com Docker Compose (onde o host 'db' existe)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" = "db" ]; then
    echo "Aguardando o banco de dados local ficar pronto..."
    until nc -z -v -w30 db 3306; do
      echo "Banco de dados ainda indisponível, aguardando..."
      sleep 2
    done
    echo "Banco de dados pronto!"
fi

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