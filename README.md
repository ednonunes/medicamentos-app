Aplicação para gerenciar medicamentos e seus horários de consumo.

Iniciando: 
$ php artisan migrate
$ docker compose up -d
$ npm run build

Atualmente configurado para a porta 8000 (http://localhost:8000/)

Execuitar os testes unitários da aplicação:

$ docker compose exec app php artisan test