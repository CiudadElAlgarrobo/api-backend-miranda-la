#!/bin/bash

# Asegurar que los directorios tengan los permisos correctos
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Usamos el archivo .env existente
echo "Usando archivo .env existente con la configuración de DB: ${DB_CONNECTION}@${DB_HOST}:${DB_PORT}/${DB_DATABASE}"

# Ejecutar migraciones si está habilitado
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Ejecutando migraciones de base de datos..."
    php /var/www/html/artisan migrate --force
fi

# Limpiar caché
php /var/www/html/artisan cache:clear

# Optimizaciones para producción (solo si no estamos en modo debug)
if [ "${APP_DEBUG:-false}" = "false" ]; then
    echo "Aplicando optimizaciones para producción..."
    php /var/www/html/artisan config:cache
    php /var/www/html/artisan route:cache
    php /var/www/html/artisan view:cache
fi

# Iniciar supervisor (que a su vez inicia Nginx y PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
