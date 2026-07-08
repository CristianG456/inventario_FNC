FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev

RUN docker-php-ext-install zip pdo_mysql mbstring exif pcntl bcmath gd opcache

RUN {
    echo 'opcache.enable=1';
    echo 'opcache.enable_cli=1';
    echo 'opcache.memory_consumption=192';
    echo 'opcache.interned_strings_buffer=16';
    echo 'opcache.max_accelerated_files=20000';
    echo 'opcache.validate_timestamps=1';
    echo 'opcache.revalidate_freq=0';
    echo 'opcache.save_comments=1';
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

CMD ["php-fpm"]