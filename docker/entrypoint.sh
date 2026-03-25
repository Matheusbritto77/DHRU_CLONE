#!/bin/sh
set -eu

APP_USER="${APP_USER:-www}"
APP_GROUP="${APP_GROUP:-www}"

mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

touch /var/www/html/storage/logs/laravel.log

chown -R "${APP_USER}:${APP_GROUP}" /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R o+rwX /var/www/html/storage /var/www/html/bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
