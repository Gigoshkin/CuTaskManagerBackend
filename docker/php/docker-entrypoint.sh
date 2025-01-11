#!/bin/sh
set -e

if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
    php bin/console assets:install --no-interaction

    echo "Making sure public / private keys for JWT exist..."
    php bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction

    echo "Creating database for tests"
    php bin/console --env=test doctrine:database:create
    php bin/console --env=test doctrine:schema:create

fi

exec "$@"
