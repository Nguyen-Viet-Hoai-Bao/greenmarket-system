FROM php:8.2-fpm-alpine

# Cài đặt các extension PHP cần thiết
RUN apk update && apk add --no-cache \
    nginx \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    git \
    build-base \
    autoconf \
    openssl-dev \
    onig-dev \
    icu-dev \
    shadow \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql zip pcntl bcmath gd intl opcache \
    && docker-php-ext-enable opcache \
    && rm -rf /var/cache/apk/*

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Thiết lập người dùng và quyền
RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -s /bin/sh -D laravel
RUN chown -R laravel:laravel /var/www/html

USER laravel

WORKDIR /var/www/html

# Sao chép các tệp cần thiết (không sao chép node_modules và vendor nếu chúng lớn)
COPY --chown=laravel:laravel . .

# Cài đặt dependencies của Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Cấp quyền cho các thư mục lưu trữ và bộ nhớ cache
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R laravel:laravel storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
