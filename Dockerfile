FROM php:8.0-cli-alpine

RUN apk add --no-cache bash

RUN apt-get update && apt-get install -y curl unzip

##RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

##RUN chmod +x /usr/local/bin/composer

##COPY --from=composer:2.2.21 /usr/bin/composer /usr/bin/composer

WORKDIR /app

RUN apt-get update && apt-get install -y libpq-dev \
        && docker-php-ext-install pgsql pdo_pgsql

FROM php:8.0-fpm

RUN apt-get update && apt-get install -y curl unzip

##RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

##RUN chmod +x /usr/local/bin/composer

##ENV COMPOSER_ALLOW_SUPERUSER=1

##COPY --from=composer:2.2.21 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM composer:latest AS composer

FROM php:7.4-fpm

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN chmod +x /usr/local/bin/composer
