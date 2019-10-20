FROM phagrancy-nginx:latest as nginx

FROM php:7.3-fpm-buster

COPY --from=nginx /srv/phagrancy /srv/phagrancy
