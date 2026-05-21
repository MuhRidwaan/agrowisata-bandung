#!/bin/sh
set -eu

if [ "${DB_CONNECTION:-}" = "mysql" ]; then
  if [ -z "${DB_HOST:-${MYSQLHOST:-}}" ] && [ -z "${DB_URL:-${MYSQL_URL:-}}" ]; then
    echo "MySQL is selected but no database host or URL is configured. Set Railway MySQL variables or DB_* variables."
    exit 1
  fi
fi

php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi

if [ "${RUN_SEEDERS:-true}" = "true" ]; then
  php artisan db:seed --force
fi

php artisan optimize:clear || true
php artisan optimize || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
