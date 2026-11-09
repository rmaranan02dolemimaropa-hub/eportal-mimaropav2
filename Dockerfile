FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl mysqli pdo_mysql zip gd bcmath exif \
    && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data writable

EXPOSE 80
CMD ["sh", "-c", "cp /etc/secrets/.env /var/www/html/.env && chown www-data:www-data /var/www/html/.env && chmod 644 /var/www/html/.env; apache2-foreground"]