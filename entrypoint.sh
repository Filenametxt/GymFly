#!/bin/sh
set -e

# Configura i limiti PHP usando le variabili d'ambiente (con fallback a valori di default sicuri)
PHP_INI_CONF="/usr/local/etc/php/conf.d/docker-php-custom-limits.ini"
echo "Configurazione dei limiti PHP in $PHP_INI_CONF..."
{
    echo "upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE:-20M}"
    echo "post_max_size = ${PHP_POST_MAX_SIZE:-25M}"
    echo "memory_limit = ${PHP_MEMORY_LIMIT:-512M}"
} > "$PHP_INI_CONF"
chmod 644 "$PHP_INI_CONF"

# Questo gira a RUNTIME (quindi DATABASE_URL è disponibile!)
echo "Verifico la configurazione di Doctrine..."
php bin/console orm:info || true

echo "Aggiorno lo schema del database..."
php bin/console orm:schema-tool:create || true

# Avvia Apache e mantiene il container attivo
echo "Avvio Apache..."
exec apache2-foreground