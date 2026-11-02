# Private Vagrant Box Hosting

![Tests](https://github.com/dlundgren/phagrancy/actions/workflows/tests.yml/badge.svg?event=push) [![Maintainability](https://qlty.sh/gh/dlundgren/projects/phagrancy/maintainability.svg)](https://qlty.sh/gh/dlundgren/projects/phagrancy)

Phagrancy implements a self-hosted subset of Vagrant Cloud. It allows you to build images in Packer, publish them and then share the images with your co-workers via Vagrant, all on-premise.

## Documentation

Please see the [wiki](https://github.com/dlundgren/phagrancy/wiki) for documentation.

## Docker usage

You can use the [docker-compose.yml](docker-compose.yml) as an example of how to use phagrancy via docker.

## Security

Phagrancy is intended to be used in a trusted network. There are limited authentication options, that control access to the api and the frontend.

## Credits

The idea is based off of the [Vagrancy](https://github.com/ryandoyle/vagrancy) project, but has been updated for current packer releases.
