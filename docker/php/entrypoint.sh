#!/usr/bin/env sh
set -e

WWWUSER="${WWWUSER:-1000}"
WWWGROUP="${WWWGROUP:-1000}"
PHP_BIN="${PHP_BIN:-php82}"
PHP_FPM_BIN="${PHP_FPM_BIN:-php-fpm82}"

addgroup -g "$WWWGROUP" -S www 2>/dev/null || true
adduser  -u "$WWWUSER" -S -D -H -G www www 2>/dev/null || true

mkdir -p storage/framework/{cache,views,sessions} bootstrap/cache
chown -R www:www storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

if [ -f .env ]; then
  if ! grep -q '^APP_KEY=' .env || grep -q '^APP_KEY=\s*$' .env ; then
    echo "[entrypoint] Generating APP_KEY…"
    $PHP_BIN artisan key:generate --force || true
  fi

  DB_CONNECTION="$(grep -E '^DB_CONNECTION=' .env | cut -d= -f2- | tr -d '\r')"
  DB_HOST="$(grep -E '^DB_HOST=' .env | cut -d= -f2- | tr -d '\r')"
  DB_PORT="$(grep -E '^DB_PORT=' .env | cut -d= -f2- | tr -d '\r')"
  DB_PORT="${DB_PORT:-3306}"
  DB_USER="$(grep -E '^DB_USERNAME=' .env | cut -d= -f2- | tr -d '\r')"
  DB_PASS="$(grep -E '^DB_PASSWORD=' .env | cut -d= -f2- | tr -d '\r')"

  APP_ENV_VAL="$(grep -E '^APP_ENV=' .env | cut -d= -f2- | tr -d '\r')"
  APP_ENV_VAL="${APP_ENV_VAL:-local}"

  if [ "$DB_CONNECTION" = "mysql" ] && [ -n "$DB_HOST" ]; then
    echo "[entrypoint] Waiting for MySQL at ${DB_HOST}:${DB_PORT}…"
    PASS_OPT=""
    [ -n "$DB_PASS" ] && PASS_OPT="-p${DB_PASS}"
    for i in $(seq 1 120); do
      if mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USER}" ${PASS_OPT} --silent 2>/dev/null; then
        echo "[entrypoint] MySQL is up."
        break
      fi
      sleep 1
    done
  fi

  echo "[entrypoint] Running migrations…"
  $PHP_BIN artisan migrate --force --no-interaction || true

  $PHP_BIN artisan optimize:clear || true

  if [ "$APP_ENV_VAL" = "local" ]; then
    $PHP_BIN artisan l5-swagger:generate || true
  fi
fi

exec "$PHP_FPM_BIN" -F
