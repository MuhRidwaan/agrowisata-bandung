#!/bin/sh
set -eu

if [ -n "${RAILWAY_PROJECT_ID:-}" ] || [ -n "${RAILWAY_ENVIRONMENT_ID:-}" ] || [ -n "${RAILWAY_PUBLIC_DOMAIN:-}" ]; then
  EFFECTIVE_DB_HOST="${MYSQLHOST:-${DB_HOST:-}}"
  EFFECTIVE_DB_PORT="${MYSQLPORT:-${DB_PORT:-}}"
  EFFECTIVE_DB_NAME="${MYSQLDATABASE:-${DB_DATABASE:-}}"

  echo "Railway boot env: APP_ENV=${APP_ENV:-unset} APP_DEBUG=${APP_DEBUG:-unset} DB_CONNECTION=${DB_CONNECTION:-unset} DB_HOST=${EFFECTIVE_DB_HOST:-unset} DB_PORT=${EFFECTIVE_DB_PORT:-unset} DB_DATABASE=${EFFECTIVE_DB_NAME:-unset}"

  if [ "${EFFECTIVE_DB_HOST:-}" = "127.0.0.1" ] || [ "${EFFECTIVE_DB_HOST:-}" = "localhost" ]; then
    echo "Railway is still using a localhost database host. Remove local DB_* variables from the Railway app service and use Railway MySQL reference variables instead."
    exit 1
  fi
fi

if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
  if [ -z "${DB_HOST:-${MYSQLHOST:-}}" ] && [ -z "${DB_URL:-${MYSQL_URL:-}}" ]; then
    echo "MySQL variables are missing in Railway. Set MYSQL_URL or DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD."
    exit 1
  fi
fi

php artisan optimize:clear || true
php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force || echo "Migration skipped or failed; continuing startup."
fi

if [ "${RUN_SEEDERS:-true}" = "true" ]; then
  php artisan db:seed --force || echo "Seeder skipped or failed; continuing startup."
fi

php artisan optimize || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
