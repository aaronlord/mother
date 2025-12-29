FROM php:8.3-cli

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apt-get update \
    && apt-get install -y git unzip \
    && install-php-extensions pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
