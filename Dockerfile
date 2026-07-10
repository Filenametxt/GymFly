# Usa un'immagine ufficiale di PHP con Apache
FROM php:8.2-apache

# Installa le estensioni necessarie per il tuo database e le funzionalità base
RUN docker-php-ext-install pdo pdo_mysql

# Abilita il modulo rewrite di Apache (spesso necessario per le rotte dei web app)
RUN a2enmod rewrite

# Copia tutto il contenuto della tua cartella locale nel server
COPY . /var/www/html/

# IMPORTANTE: Cambia la document root di Apache per puntare alla cartella /public
# Questo farà sì che il sito carichi automaticamente il file index.php in /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf



# Assicurati che i permessi siano corretti per Smarty (cartella Templates_c)
RUN chown -R www-data:www-data /var/www/html/src/View/Templates_c

# Espone la porta 80
EXPOSE 80