
FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip


RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www


COPY . /var/www


RUN composer install --optimize-autoloader --no-dev


RUN chown -R www-data:www-data /var/www
RUN chmod 755 /var/www


EXPOSE 8000


CMD ["php-fpm"]