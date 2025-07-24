FROM php:8.2-fpm


WORKDIR /var/www


RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


COPY . .


RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache


CMD ["php-fpm"]
