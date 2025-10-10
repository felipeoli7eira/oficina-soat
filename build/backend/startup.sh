#!/bin/sh

set -e

echo "🛠️ Ajustando permissões"
chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage
# chown -R www-data:www-data storage bootstrap/cache
# chmod -R 775 storage bootstrap/cache

mkdir -p /tmp
touch /tmp/xdebug.log
chmod 777 /tmp/xdebug.log

echo "📦 Instalando dependências"
mkdir -p vendor
composer install --optimize-autoloader || {
    echo "❌ Falha na instalação das dependências"
    exit 1
}

if [ ! -f .env ]; then
    echo "⚙️ Criando arquivo .env"
    cp .env.example .env

    echo "🔑 Gerando chave da aplicação"
    php artisan key:generate

    # echo "🔑 Gerando chave do JWT"
    # php artisan jwt:secret --force
fi

echo "🆙 Preparando banco de dados"
php artisan migrate:fresh --force || {
    echo "❌ Falha na execução das migrations"
    exit 1
}

echo "🌱 Executando seeders"
php artisan db:seed || {
    echo "❌ Falha na execução dos seeders"
    exit 1
}

echo "🚀 Iniciando o container"

exec php-fpm
