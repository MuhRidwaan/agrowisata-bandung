#!/bin/sh
set -eu

if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
  if [ -z "${DB_HOST:-${MYSQLHOST:-}}" ] && [ -z "${DB_URL:-${MYSQL_URL:-}}" ]; then
    echo "MySQL variables are missing in Railway. Set MYSQL_URL or DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD."
    exit 1
  fi
fi

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
