# Base Image
FROM php:8.4-apache

# Set working directory
WORKDIR /var/www/html

# Install required extensions
RUN apt-get update && apt-get install -y \
    wget curl \
    libpq-dev libzip-dev unzip sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mysqli session

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Download and install wait-for-it
RUN curl -o /usr/local/bin/wait-for-it https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /usr/local/bin/wait-for-it

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache config
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# First, copy the entire project (including docker/composer.json)
COPY . /var/www/html/

# Run composer.install
RUN cd /var/www/html && composer clear-cache && composer install --no-dev --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
