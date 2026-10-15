FROM php:8.3-fpm-alpine
RUN sed -i 's|dl-cdn.alpinelinux.org|mirror.arvancloud.ir|g' /etc/apk/repositories \
    && apk add --no-cache icu-libs libzip oniguruma freetype libjpeg-turbo libpng libxml2 unzip git \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev freetype-dev libjpeg-turbo-dev libpng-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 bcmath exif gd intl mbstring opcache pdo_mysql zip pcntl \
    && apk del .build-deps
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/zz-phonenotify.ini
WORKDIR /var/www/html
USER www-data
EXPOSE 9000
CMD ["php-fpm", "-F"]
