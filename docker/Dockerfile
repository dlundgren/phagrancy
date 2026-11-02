FROM serversideup/php:8.5-fpm-nginx-alpine

ARG APP_VERSION=master

LABEL org.opencontainers.image.title="phagrancy" \
      org.opencontainers.image.version="${APP_VERSION}" \
      org.opencontainers.image.description="phagrancy" \
      org.opencontainers.image.authors="David Lundgren <dlundgren@syberisle.com>" \
      org.opencontainers.image.vendor="" \
      org.opencontainers.image.licenses="MIT" \
      org.opencontainers.image.source="https://github.com/dlundgren/phagrancy" \
      org.opencontainers.image.url="https://github.com/dlundgren/phagrancy"

ENV PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

USER root

WORKDIR /var/www/html

RUN chown root:root /var/www/html
RUN mkdir -p /var/www/html/storage/boxes && \
    chown -R www-data:www-data /var/www/html/storage/boxes

COPY composer.json composer.lock* ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-autoloader \
    --no-scripts

COPY web/ ./public/
COPY src/ ./src/

RUN composer dump-autoload \
    --classmap-authoritative \
    --no-dev

USER www-data
