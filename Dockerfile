FROM composer:2 AS composer

FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libonig-dev libxml2-dev default-mysql-client \
    && docker-php-ext-install bcmath mbstring pdo_mysql pcntl xml zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY docker/start.sh /usr/local/bin/start-app
RUN chmod +x /usr/local/bin/start-app

EXPOSE 8000

CMD ["start-app"]
