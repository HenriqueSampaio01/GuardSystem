FROM php:8.2-cli
RUN apt-get update && apt-get install -y libpq-dev curl unzip libzip-dev && docker-php-ext-install pdo pdo_pgsql zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . /var/www/html
RUN cd /var/www/html && composer install --no-dev --optimize-autoloader
CMD php -S 0.0.0.0:${PORT:-80} -t /var/www/html