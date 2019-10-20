FROM composer:1.9.0 as composer

RUN git clone https://github.com/dlundgren/phagrancy.git \
  && cd phagrancy \
  && composer install \
  && rm -rvf .git .gitignore .travis.yml

FROM nginx:1.16.1

COPY --from=composer /app/phagrancy /srv/phagrancy
COPY nginx.conf /etc/nginx/nginx-phagrancy.conf
COPY certs /etc/nginx/certs

RUN chmod -R 0750 /etc/nginx/certs \
  && chmod 0600 /etc/nginx/certs/*.crt \
  && chmod 0400 /etc/nginx/certs/*.key \
  && chown -R www-data. /srv/phagrancy

WORKDIR /srv/phagrancy
ENTRYPOINT ["/usr/sbin/nginx", "-c", "/etc/nginx/nginx-phagrancy.conf"]
