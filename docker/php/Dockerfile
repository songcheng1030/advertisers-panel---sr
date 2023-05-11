# ./docker/php/Dockerfile
FROM php:7.3-fpm

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends \
    && apt-get install -y vim cron unzip git curl libxml2-dev g++ make autoconf icu-devtools libzip-dev ghostscript libgs-dev\
    && docker-php-source extract \
    && pecl install xdebug \
    && pecl install imagick \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable imagick \
    && docker-php-source delete \
    && docker-php-ext-install pdo_mysql soap intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/*

WORKDIR /usr/src/app

COPY . /usr/src/app

RUN composer install

RUN echo "* * * * * cd /usr/src/app && vendor/bin/crunz schedule:run > /proc/1/fd/1 2>/proc/1/fd/2" >> /var/spool/cron/crontabs/root
