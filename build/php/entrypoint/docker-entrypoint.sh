#!/usr/bin/env bash

set -e

echo "🛠️ Ajustando permissões de pasta de cache e storage"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ ! -f vendor/autoload.php ]; then
    echo "📦 Instalando dependências"

    composer install --no-interaction --prefer-dist --optimize-autoloader || {
        echo "❌ Falha na instalação das dependências"
        exit 1
    }
fi

if [ ! -f .env ]; then
    echo "⚙️ Criando arquivo .env"
    cp .env.example .env

    echo "🔑 Gerando chave da aplicação"
    php artisan key:generate

    echo "🆙 Rodando migrations"
    php artisan migrate --seed
fi

echo "📚 Atualizando doc da api"
php artisan l5-swagger:generate

echo "🚀 Iniciando o container"

exec "$@"
