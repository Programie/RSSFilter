FROM node:18 AS webpack

WORKDIR /app

COPY package.json package-lock.json /app/
RUN npm install

COPY webpack.config.js /app/
COPY src/main/resources /app/src/main/resources
RUN npm run build


FROM composer AS composer

WORKDIR /app

COPY composer.* /app/
RUN composer install --no-dev --ignore-platform-reqs


FROM ghcr.io/programie/dockerimages/php

ENV WEB_ROOT=/app/httpdocs

WORKDIR /app

RUN install-php 8.2 dom intl pdo-mysql && \
    a2enmod rewrite

COPY --from=composer /app/vendor /app/vendor
COPY --from=webpack /app/httpdocs/assets /app/httpdocs/assets
COPY --from=webpack /app/webpack.assets.json /app/webpack.assets.json

COPY bootstrap.php /app/bootstrap.php
COPY httpdocs /app/httpdocs
COPY src /app/src