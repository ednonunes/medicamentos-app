#!/bin/sh

# Se a variável PORT existir (Railway), roda o servidor embutido do PHP
if [ -n "$PORT" ]; then
    echo "Ambiente de Produção (Railway) detectado!"
    php artisan migrate --force
    exec php -S 0.0.0.0:$PORT -t public
else
    # Se não houver PORT (Ambiente Local), roda o PHP-FPM padrão para o Nginx
    echo "Ambiente de Desenvolvimento Local detectado!"
    exec php-fpm
fi