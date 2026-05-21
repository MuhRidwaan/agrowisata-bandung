#!/bin/sh
set -eu

php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

php artisan optimize:clear || true
php artisan optimize || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
