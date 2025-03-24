# Base Image
FROM php:8.4-apache

# Install required extensions
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mysqli session

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache config
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
