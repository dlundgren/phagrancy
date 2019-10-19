# Deploy phagrancy with docker

Example how to run phagrancy app, on-premise Vagrant cloud, with docker.
Two containers phagrancy-nginx and phagrancy-php-fpm are used.

## Prepare a certificate and private key for the nginx web-server

    mkdir nginx/certs
    openssl \
        req \
        -nodes \
        -x509 \
        -newkey rsa:4096 \
        -keyout nginx/certs/phagrancy.local.key \
        -out nginx/certs/phagrancy.local.crt \
        -days 365 \
        -subj "/C=UA/L=Kyiv/O=Company name/OU=IT/CN=phagrancy.local"

## Build nginx and php-fpm docker images

    docker build -t phagrancy-nginx:latest nginx
    docker build -t phagrancy-php-fpm:latest php-fpm

## Prepare .env_phagrancy configuration file

See docs [wiki/.env-Configuration-File](https://github.com/dlundgren/phagrancy/wiki/.env-Configuration-File).
We can not use a phagrancy .env file alongside with docker-compose.yml
because docker-compose loads startup environment variables from .env file.

It is supposed that `storage_path=boxes`, this path will be mounted into
php-fpm docker container. See [docker-compose.yml](docker-compose.yml).

php-fpm is running with user:group `www-data:www-data`. Change file's owner:

    chown 33:33 .env_phagrancy

## Create a directory to store vagrant boxes

    mkdir boxes
    chown 33:33 boxes

## Start phagrancy docker containers

    docker-compose up
