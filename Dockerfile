FROM php:8.2-fpm

# Instalar dependencias necesarias y extensiones
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip pdo_mysql mbstring exif pcntl bcmath gd opcache

RUN printf "%s\n" \
    "opcache.enable=1" \
    "opcache.enable_cli=1" \
    "opcache.memory_consumption=192" \
    "opcache.interned_strings_buffer=16" \
    "opcache.max_accelerated_files=20000" \
    "opcache.validate_timestamps=1" \
    "opcache.revalidate_freq=2" \
    "opcache.file_update_protection=0" \
    "opcache.save_comments=1" \
    > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-interaction && php artisan package:discover --ansi

RUN mkdir -p /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]