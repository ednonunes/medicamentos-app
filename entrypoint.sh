#!/bin/sh

# Se a variável PORT existir (Railway), roda o servidor embutido do PHP
if [ -n "$PORT" ]; then
    echo "Ambiente de Produção (Railway) detectado!"
    
    # Roda as migrations
    php artisan migrate --force
    
    # CRITICAL FIX: Garante que o link do storage seja criado no deploy
    php artisan storage:link --force
    
    exec php -S 0.0.0.0:$PORT -t public
else
    # Se não houver PORT (Ambiente Local), roda o PHP-FPM padrão para o Nginx
    echo "Ambiente de Desenvolvimento Local detectado!"
    exec php-fpm
fi