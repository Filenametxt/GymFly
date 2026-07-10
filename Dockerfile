# Usa un'immagine ufficiale di PHP 8.2 con Apache
FROM php:8.2-apache

# Installa le estensioni necessarie
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Installa Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Abilita il modulo rewrite di Apache
RUN a2enmod rewrite

# Imposta la directory di lavoro
WORKDIR /var/www/html

# Copia i file del progetto
COPY . .

# Installa le dipendenze
RUN composer install --no-dev --optimize-autoloader

# Configura Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Crea la cartella di cache e imposta i permessi
RUN mkdir -p /var/www/html/src/View/Templates_c && \
    chown -R www-data:www-data /var/www/html/src/View/Templates_c && \
    chmod -R 775 /var/www/html/src/View/Templates_c

# --- AGGIUNTA COMANDI DI VERIFICA DOCTRINE ---
# Esegue il test di caricamento delle entità e la creazione dello schema
RUN php -r "error_reporting(E_ALL); ini_set('display_errors', 1); require 'vendor/autoload.php'; use App\Infrastructure\Doctrine\EntityManagerFactory; try { \$em = EntityManagerFactory::create(); \$meta = \$em->getMetadataFactory()->getAllMetadata(); var_dump(count(\$meta)); foreach(\$meta as \$m) echo \$m->getName() . PHP_EOL; } catch (\Throwable \$e) { echo get_class(\$e) . ': ' . \$e->getMessage() . PHP_EOL; exit(1); }" && \
    php bin/console orm:info && \
    php bin/console orm:schema-tool:create

# Espone la porta 80
EXPOSE 80