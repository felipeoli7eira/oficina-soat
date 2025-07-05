#!/bin/sh

set -e

echo "🔄 Clonando .env.example para .env"
cp -n .env.example .env

echo "🔄 Rodando permissão para o .env"
chmod 777 /var/www/html/.env

echo "🔄 Instalando dependências com Composer"
composer install

echo "🔄 Gerando chave da aplicação"
php artisan key:generate --ansi

echo "🔄 Criando arquivo SQLite"
touch /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database/database.sqlite

echo "🔄 Executando migrations"
php artisan migrate --force

echo "🔄 Iniciando PHP-FPM"
exec php-fpm
