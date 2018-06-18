# Private Vagrant Box Hosting

[![Travis CI](https://secure.travis-ci.org/dlundgren/phagrancy.png)](https://travis-ci.org/dlundgren/phagrancy) [![Code Climate](https://codeclimate.com/github/dlundgren/phagrancy/badges/gpa.svg)](https://codeclimate.com/github/dlundgren/phagrancy)

Phagrancy implements a self-hosted subset of Vagrant Cloud. It allows you to build images in Packer, publish them and then share the images with your co-workers via Vagrant, all on-premise.

## Documentation

Please see the [wiki](https://github.com/dlundgren/phagrancy/wiki) for documentation.

## Security

Phagrancy is intended to be used in a trusted network. There are limited authentication options, that control access to the api and the frontend.

## Credits

The idea is based off of the [Vagrancy](https://github.com/ryandoyle/vagrancy) project, but has been updated for current packer releases.
