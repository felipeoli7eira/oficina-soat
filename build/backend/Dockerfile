FROM php:8.4-fpm-alpine3.22

WORKDIR /var/www/html

RUN apk update && apk upgrade

RUN apk add --no-cache \
    curl \
    zip \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apk/lists/* \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install pdo_pgsql && docker-php-ext-enable pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./build/backend/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

COPY ./backend .

ENTRYPOINT ["/usr/local/bin/startup.sh"]
