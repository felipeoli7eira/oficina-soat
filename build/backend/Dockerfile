FROM php:8.4-fpm-alpine3.22

WORKDIR /var/www/html

RUN apk update && apk upgrade

RUN apk add --no-cache \
    curl \
    zip \
    git \
    unzip \
    libpq-dev \
    autoconf \
    g++ \
    make \
    linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=coverage,develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && apk del autoconf g++ make linux-headers \
    && rm -rf /var/lib/apk/lists/* \
    && rm -rf /var/cache/apk/*


RUN docker-php-ext-install pdo_pgsql && docker-php-ext-enable pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./build/backend/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

RUN sed -i 's/\r$//' /usr/local/bin/startup.sh

COPY ./backend .

# RUN chown -R www-data:www-data /var/www/html/storage \
#     && chmod -R 775 /var/www/html/storage

ENTRYPOINT ["/usr/local/bin/startup.sh"]
