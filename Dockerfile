# Usa un'immagine ufficiale di PHP 8.2 con Apache
FROM php:8.2-apache

# Installa le estensioni necessarie (pdo, pdo_mysql per Doctrine)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Installa Composer per gestire le dipendenze
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Abilita il modulo rewrite di Apache per le rotte del tuo progetto
RUN a2enmod rewrite

# Imposta la directory di lavoro nel container
WORKDIR /var/www/html

# Copia i file del tuo progetto nel container
COPY . .

# Installa le dipendenze PHP escludendo quelle di sviluppo
RUN composer install --no-dev --optimize-autoloader

# Configura Apache per puntare correttamente alla cartella /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Crea la cartella di cache per Smarty se manca e imposta i permessi corretti
RUN mkdir -p /var/www/html/src/View/Templates_c && \
    chown -R www-data:www-data /var/www/html/src/View/Templates_c && \
    chmod -R 775 /var/www/html/src/View/Templates_c

# Espone la porta 80 per il web traffic
EXPOSE 80