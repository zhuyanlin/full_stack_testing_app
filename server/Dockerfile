# Use official PHP image as base
FROM php:8.0-apache

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Expose port 80 to the outside world
EXPOSE 80

# Set the working directory
WORKDIR /var/www/html

# Start Apache server in foreground
CMD ["apache2-foreground"]
