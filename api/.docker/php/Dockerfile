# ./docker/php/Dockerfile
FROM php:8.2-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu
RUN docker-php-ext-install sockets
RUN docker-php-ext-install opcache

RUN apt-get update && \
    apt-get install -y \
    ssh openssl bash wget htop\
    zlib1g-dev libicu-dev g++ libzip-dev zip && docker-php-ext-install zip

RUN apt-get update -y && apt-get install -y librabbitmq-dev

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

RUN wget https://get.symfony.com/cli/installer -O - | bash

RUN mkdir -p /var/www/.composer && chown -R www-data /var/www/.composer

RUN usermod -u 1000 www-data

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"
#COPY docker/php/uploads.ini /usr/local/etc/php/conf.d
COPY api/.docker/php/opcache.ini "$PHP_INI_DIR/conf.d"
#COPY api/.docker/php/php-fpm-custom.conf "$PHP_INI_DIR/conf.d"
#COPY api/.docker/php/php-fpm-custom.conf /usr/local/etc/php-fpm.d

WORKDIR /usr/src/api/app