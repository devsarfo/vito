FROM dunglas/frankenphp:latest-php8.4

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive

# Install PHP extensions required by Vito
# Note: pcntl is already included in FrankenPHP image
RUN install-php-extensions \
    intl \
    mbstring \
    xml \
    curl \
    zip \
    bcmath \
    gd \
    redis \
    pdo_sqlite \
    opcache

# Install additional tools (same as 3.x)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    supervisor \
    redis-server \
    openssh-client \
    cron \
    && rm -rf /var/lib/apt/lists/*

# php config
COPY docker/php.ini /usr/local/etc/php/conf.d/99-vito.ini

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# app
COPY . /var/www/html
RUN rm -rf /var/www/html/.git /var/www/html/vendor /var/www/html/node_modules
RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# cron
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/log/cron.log 2>&1" | crontab -

# supervisord
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# start
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80 443

CMD ["/start.sh"]
