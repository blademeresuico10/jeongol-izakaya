FROM php:8.2-fpm

WORKDIR /var/www

# Install required packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd

# Copy composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
