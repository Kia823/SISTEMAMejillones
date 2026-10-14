FROM php:8.3-cli

# Instalar dependencias del sistema y Node.js
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_mysql zip gd mbstring

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node.js y compilar Vite
RUN npm install && npm run build

# Limpiar y cachear configuración
# Usar drivers en memoria durante el build (no hay .env, SQLite sin tablas)
ENV CACHE_STORE=array \
    SESSION_DRIVER=array \
    QUEUE_CONNECTION=sync
RUN touch /var/www/html/database/database.sqlite
RUN php artisan config:clear
RUN php artisan cache:clear --no-interaction || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
