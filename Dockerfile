# Stage 1: Build Assets
FROM node:20-alpine as assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP Application
FROM php:8.4-fpm-alpine

# Use custom UID/GID for better security on VPS
ARG USER=www
ARG GROUP=www
RUN addgroup -S $GROUP && adduser -S $USER -G $GROUP

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk update && apk upgrade && \
    apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    postgresql-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    linux-headers

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath gd zip intl xml opcache

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy App code
COPY --chown=$USER:$GROUP . .
# Copy compiled assets
COPY --from=assets --chown=$USER:$GROUP /app/public/build ./public/build

# Install PHP dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    DB_CONNECTION=sqlite \
    DB_DATABASE=:memory:
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && php artisan package:discover --ansi

# Copy Nginx configuration
COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf
# Copy Supervisor configuration
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Set Permissions
RUN chown -R $USER:$GROUP /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Start with supervisor to handle both Nginx and PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
