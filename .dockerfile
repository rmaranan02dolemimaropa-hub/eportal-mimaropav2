FROM php:8.2-apache

RUN apt-get update && apt-get install -y libicu-dev libzip-dev unzip git \
    && docker-php-ext-install intl mysqli pdo_mysql zip \
    && a2enmod rewrite

# Serve from CI4's /public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data writable

EXPOSE 80
# Copy the .env from Render's Secret Files, then start Apache
CMD ["sh", "-c", "cp /etc/secrets/.env /var/www/html/.env 2>/dev/null; apache2-foreground"]