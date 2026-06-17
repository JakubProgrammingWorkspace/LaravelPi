FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev libwebp-dev libargon2-dev \
    libssl-dev libtidy-dev libcurl4-openssl-dev libicu-dev \
    libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd mysqli pdo_mysql zip xml mbstring bcmath ctype curl \
    intl opcache bcmath exif pcntl soap

# Install extensions for Laravel
RUN pecl install redis && docker-php-ext-enable redis

# Copy composer files
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files (will be overwritten by volume mount in docker-compose)
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
