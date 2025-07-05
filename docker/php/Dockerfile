FROM php:8.3.22-fpm

RUN echo "🔄 Rodando atualizações."
RUN apt-get update && apt-get install -y \
    nano \
    vim \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

RUN echo "🔄 Copiando arquivos."
COPY ./app .
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN echo "🔄 Executando permissões."
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

USER www-data

RUN echo "🔄 Executando comandos entrypoint, aguarde ..."
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
