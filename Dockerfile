# =============================================================================
# Stage 1 : Build des assets frontend (Node)
# =============================================================================
FROM node:20-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ resources/
RUN npm run build

# =============================================================================
# Stage 2 : Application PHP (Laravel)
# =============================================================================
FROM php:8.2-fpm-alpine AS base

# Installer les extensions PHP nécessaires
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    intl \
    mbstring \
    gd \
    opcache \
    pcntl \
    bcmath

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier composer files et installer les dépendances
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier le reste de l'application
COPY . .

# Copier les assets buildés depuis le stage frontend
COPY --from=frontend /app/public/build public/build

# Finaliser l'installation Composer (post-scripts)
RUN composer dump-autoload --optimize

# Créer les répertoires de stockage nécessaires
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configuration Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Configuration Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configuration PHP OPcache
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configuration PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Script d'entrée
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
