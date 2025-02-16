FROM php:8.4-fpm-alpine AS app_php

RUN apk update

RUN apk add --update --virtual procps autoconf g++ make libpq-dev icu-dev libzip-dev git openssh-client openssl nano \
    && apk add acl postgresql \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pgsql pdo_pgsql \
    && pecl channel-update pecl.php.net \
    && pecl install apcu \
    && docker-php-ext-enable apcu opcache \
    && docker-php-ext-install intl \
    && runDeps="$( \
    		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
    			| tr ',' '\n' \
    			| sort -u \
    			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    	)" \
    && apk add --no-cache --virtual .app-phpexts-rundeps $runDeps \
    && pecl clear-cache \
    && rm -rf /tmp/pear \
    && rm -rf /var/cache/apk/*


COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY composer.json composer.lock symfony.lock ./

COPY docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini

RUN mkdir -p /var/www/app
WORKDIR /var/www/app

RUN mkdir -p var/cache var/log \
    && setfacl -dR -m u:www-data:rwX -m u:$(whoami):rwX ./ \
    && setfacl -R -m u:www-data:rwX -m u:$(whoami):rwX ./

VOLUME /var/www/app

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]

FROM nginx:1.24-alpine AS app_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /var/www/app/public