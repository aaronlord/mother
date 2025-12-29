FROM php:8.4-cli

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apt-get update && install-php-extensions pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
