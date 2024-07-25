FROM php:8.1-apache

RUN apt-get update && apt-get install -y locales wget nano curl libpq-dev libzip-dev zip unzip git libonig-dev libxml2-dev libcurl4-openssl-dev libpng-dev libc-client-dev libkrb5-dev libldap2-dev libedit-dev build-essential libtool libxslt-dev libmcrypt-dev\
        && CFLAGS="-I/usr/src/php"

RUN docker-php-ext-install curl \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install pgsql \
    && docker-php-ext-install pcntl
  
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

EXPOSE 80