# syntax=docker/dockerfile:1

# ---- 1) Base PHP + tools ----
FROM php:8.3-cli-alpine AS base
WORKDIR /app

# System deps (php extensions + node for Vite)
RUN apk add --no-cache \
    git bash curl icu-dev oniguruma-dev libzip-dev libxml2-dev \
    nodejs npm

# PHP extensions required by composer.json
RUN docker-php-ext-install -j$(nproc) intl pdo pdo_mysql zip xml

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ---- 2) Install PHP deps ----
FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-ansi --no-progress
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-ansi --no-progress

# ---- 3) Build frontend assets ----
FROM base AS assets
COPY package*.json* ./
RUN [ -f package-lock.json ] && npm ci || npm install
COPY . .
RUN npm run build || true

# ---- 4) Runtime ----
FROM base AS runtime
ENV PORT=10000
WORKDIR /app

# Copy app code, vendor, and built assets
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

# Ensure writable dirs
RUN mkdir -p storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Expose port used by Render
EXPOSE 10000

# Serve Laravel from /public using PHP's built-in server
CMD php -S 0.0.0.0:$PORT -t public
