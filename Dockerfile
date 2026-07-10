# Usa l'immagine ufficiale PHP 8.2 con Apache
FROM php:8.2-apache

# Installa le librerie di sistema necessarie
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

# Installa le estensioni PHP (pdo, pdo_mysql e zip)
RUN docker-php-ext-install pdo pdo_mysql zip

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

# Configura lo script di entrypoint per gestire il database all'avvio
RUN chmod +x /var/www/html/entrypoint.sh && \
    ln -s /var/www/html/entrypoint.sh /usr/local/bin/entrypoint

# Espone la porta 80
EXPOSE 80

# Esegue lo script di avvio al posto del classico apache2-foreground
ENTRYPOINT ["entrypoint"]