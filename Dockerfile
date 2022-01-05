FROM node:15 AS webpack

WORKDIR /app

COPY package.json package-lock.json /app/
RUN npm install

COPY webpack.config.js /app/
COPY src/main/resources /app/src/main/resources
RUN npm run build


FROM composer AS composer

COPY composer.* /app/

WORKDIR /app

RUN composer install --no-dev --ignore-platform-reqs && \
    rm /app/composer.json /app/composer.lock


FROM php:8.0-apache

WORKDIR /app

RUN sed -ri -e 's!/var/www/html!/app/httpdocs!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/app/httpdocs!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    echo "ServerTokens Prod" > /etc/apache2/conf-enabled/z-server-tokens.conf && \
    a2enmod rewrite && \
    apt-get -y update && \
    apt-get install -y libicu-dev gosu && \
    docker-php-ext-install intl pdo_mysql && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer /app/vendor /app/vendor
COPY --from=webpack /app/httpdocs/assets /app/httpdocs/assets
COPY --from=webpack /app/webpack.assets.json /app/webpack.assets.json

COPY bootstrap.php /app/bootstrap.php
COPY httpdocs /app/httpdocs
COPY src /app/src