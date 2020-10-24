#!/bin/bash

composer install
php /application/bin/console doctrine:migrations:migrate -nq --env=dev
php /application/bin/console hautelook:fixtures:load -nq --no-bundles --env=dev

/usr/sbin/php-fpm7.4
