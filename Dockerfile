FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-interaction --optimize-autoloader
