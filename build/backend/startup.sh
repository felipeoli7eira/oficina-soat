#!/bin/sh

set -e

# echo "🛠️ Ajustando permissões"
# chown -R www-data:www-data storage bootstrap/cache
# chmod -R 775 storage bootstrap/cache

# mkdir -p /tmp
# touch /tmp/xdebug.log
# chmod 777 /tmp/xdebug.log

# echo "📦 Instalando dependências"
# composer install --optimize-autoloader || {
#     echo "❌ Falha na instalação das dependências"
#     exit 1
# }

# if [ ! -f .env ]; then
    # echo "⚙️ Criando arquivo .env"
    # cp .env.example .env

    # echo "🔑 Gerando chave da aplicação"
    # php artisan key:generate

    # echo "🔑 Gerando chave do JWT"
    # php artisan jwt:secret --force
# fi

# echo "🆙 Preparando banco de dados"
# php artisan migrate:fresh --seed

echo "🚀 Iniciando o container"

exec php-fpm
