# ==========================================
# Stage 1: Build Application (PHP 8.3 + Node 22 + Composer)
# ==========================================
FROM php:8.3-cli-alpine AS builder

WORKDIR /app

# Install Node.js, NPM, Composer, dan tools pendukung
RUN apk add --no-cache \
    nodejs \
    npm \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        intl \
        zip \
        pdo_sqlite

# Ambil binary Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 1. Install Composer dependencies
COPY composer*.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# 2. Install Node dependencies
COPY package*.json ./
RUN npm ci

# 3. Salin source code & buat .env dummy untuk artisan wayfinder
COPY . .
RUN cp .env.example .env && php artisan key:generate --force

# 4. Generate wayfinder types & build frontend
RUN php artisan wayfinder:generate --with-form || true
RUN npm run build

# ==========================================
# Stage 2: Production Runtime (PHP 8.3 FPM + Nginx)
# ==========================================
FROM php:8.3-fpm-alpine AS production

WORKDIR /var/www/html

# Install package sistem, Nginx, Supervisor & ekstensi PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_sqlite \
        bcmath \
        gd \
        intl \
        zip \
        opcache \
        exif \
        pcntl

# Optimasi Opcache untuk production
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.save_comments=1'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Salin konfigurasi Nginx dan Supervisord
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Salin hasil build lengkap dari builder (sudah include vendor dan public/build)
COPY --from=builder /app /var/www/html

# Hapus file .env dummy build agar tidak menimpa .env production VPS
RUN rm -f /var/www/html/.env

# Set permission file untuk user www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
