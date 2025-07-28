FROM php:8.3.22-fpm

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

COPY app .

COPY ./build/php/entrypoint/docker-entrypoint.sh /usr/local/bin
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# COPY ./docker/php/docker-entrypoint.sh /var/www/sh/docker-entrypoint.sh
# COPY ./app .

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# RUN echo "🔄 Executando permissões."
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
#     && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
#     && chmod +x /var/www/sh/docker-entrypoint.sh

# USER www-data

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]
