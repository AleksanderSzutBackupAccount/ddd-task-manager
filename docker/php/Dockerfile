FROM php:8.4-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libonig-dev \
    libpq-dev libxml2-dev libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP deps early for better caching
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress || true

# Copy app source
COPY . .

RUN composer dump-autoload --no-interaction

RUN chmod +x bin/console

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
