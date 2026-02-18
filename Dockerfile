FROM php:8.4-cli

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev

# Install extensions
RUN docker-php-ext-install pdo pdo_sqlite pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Create SQLite DB
RUN touch database/database.sqlite
RUN php artisan migrate --force

EXPOSE 8000
