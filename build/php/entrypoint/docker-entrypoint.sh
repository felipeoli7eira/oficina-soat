#!/bin/bash

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

    php artisan migrate --seed
fi

if grep -q "^DB_CONNECTION=sqlite" .env; then
    if [ ! -f database/database.sqlite ]; then
        echo "💾 Criando database.sqlite"
        touch database/database.sqlite
    fi

    echo "🔧 Corrigindo permissões do database/database.sqlite"
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
fi

echo "🚀 Iniciando o container"

exec "$@"
