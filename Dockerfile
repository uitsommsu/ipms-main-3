# Use official PHP image with necessary extensions
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev zip unzip libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node and NPM (for Laravel Mix / Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install && npm run build

# Cache Laravel config, routes, and views
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port for web
EXPOSE 8000

# Default to Laravel dev server (web service)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
