FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

RUN php artisan storage:link || true

RUN php artisan config:clear || true
RUN php artisan cache:clear || true

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000