FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies, Node.js, and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip gnupg libpng-dev libonig-dev libxml2-dev libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source
COPY . .

# Install PHP and Node dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Copy and set entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose PHP-FPM port
EXPOSE 9000

# Start with entrypoint script
CMD ["docker-entrypoint.sh"]
