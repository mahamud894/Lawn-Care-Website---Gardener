# syntax=docker/dockerfile:1
#
# Gardener — production image (used by Render, Fly.io, Railway, Coolify, any Docker host).
# Build locally with:  docker build -t gardener .
# Run locally with:    docker run -p 8080:8080 -e PORT=8080 -v gardener-data:/data gardener

# ---------------------------------------------------------------------------
# 1. PHP dependencies
# ---------------------------------------------------------------------------
FROM php:8.3-cli AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

COPY composer.json composer.lock ./
# --no-scripts: artisan runs later, in the full runtime image
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# ---------------------------------------------------------------------------
# 2. CSS and JS (Vite + Tailwind)
#    Tailwind scans vendor/, so the packages from stage 1 are copied in first.
# ---------------------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY --from=vendor /app/vendor ./vendor
COPY . .
RUN npm run build

# ---------------------------------------------------------------------------
# 3. Runtime: Apache + PHP
# ---------------------------------------------------------------------------
FROM php:8.3-apache

# GD (with WebP) powers the image optimiser; exif fixes rotated phone photos.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libzip-dev libpq-dev libonig-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd zip exif opcache pdo_mysql pdo_pgsql \
    && apt-get purge -y --auto-remove \
    && rm -rf /var/lib/apt/lists/*

# .htaccess rules (rewrites, gzip, caching) need these modules
RUN a2enmod rewrite headers deflate expires

# Phone photos are 3–8 MB; they are shrunk after upload
RUN { \
        echo 'upload_max_filesize = 10M'; \
        echo 'post_max_size = 32M'; \
        echo 'memory_limit = 256M'; \
        echo 'max_execution_time = 120'; \
        echo 'opcache.enable = 1'; \
        echo 'opcache.memory_consumption = 128'; \
        echo 'opcache.max_accelerated_files = 10000'; \
        echo 'expose_php = Off'; \
    } > /usr/local/etc/php/conf.d/gardener.ini

COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache public/uploads

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Render (and most hosts) pass the port to listen on in $PORT
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]
