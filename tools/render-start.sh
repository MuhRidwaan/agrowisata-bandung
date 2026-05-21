#!/bin/sh
set -eu

php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || echo "Migration skipped or failed; continuing startup."
fi

if [ "${RUN_SEEDERS:-true}" = "true" ]; then
  php artisan db:seed --force || echo "Seeder skipped or failed; continuing startup."
fi

php artisan optimize:clear || true
php artisan optimize || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
