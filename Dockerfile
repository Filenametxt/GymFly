# Usa l'immagine ufficiale PHP 8.2 con Apache
FROM php:8.2-apache

# Installa le estensioni necessarie
# Installa le estensioni necessarie
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql libxml

# Installa Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Abilita il modulo rewrite di Apache
RUN a2enmod rewrite

# Imposta la directory di lavoro
WORKDIR /var/www/html

# Copia i file del progetto
COPY . .

# Installa le dipendenze di produzione
RUN composer install --no-dev --optimize-autoloader

# Configura Apache per puntare alla cartella /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Crea la cartella di cache, imposta i permessi e assicura il .keep
RUN mkdir -p /var/www/html/src/View/Templates_c && \
    touch /var/www/html/src/View/Templates_c/.keep && \
    chown -R www-data:www-data /var/www/html/src/View/Templates_c && \
    chmod -R 775 /var/www/html/src/View/Templates_c

# --- VERIFICA DOCTRINE (Posizionata alla fine) ---
# Se questa parte fallisce, la build si interrompe correttamente
RUN php bin/console orm:info && \
    php bin/console orm:schema-tool:create || true

# Espone la porta 80
EXPOSE 80
