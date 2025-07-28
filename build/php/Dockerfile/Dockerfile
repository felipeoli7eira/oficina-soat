FROM php:8.3.22-fpm

RUN apt-get update && apt-get install -y \
    nano \
    vim \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

COPY app .

COPY ./build/php/entrypoint/docker-entrypoint.sh /usr/local/bin
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

EXPOSE 9000

CMD ["php-fpm"]
