#!/bin/sh
set -e

# Questo gira a RUNTIME (quindi DATABASE_URL è disponibile!)
echo "Verifico la configurazione di Doctrine..."
php bin/console orm:info || true

echo "Aggiorno lo schema del database..."
php bin/console orm:schema-tool:create || true

# Avvia Apache e mantiene il container attivo
echo "Avvio Apache..."
exec apache2-foreground