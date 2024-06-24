# Use the official PHP 7.2 Apache image
FROM php:7.2-apache

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Install required dependencies
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip \
    unzip

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache modules
RUN a2enmod rewrite

# Create and set the working directory
WORKDIR /var/www

# Use Composer to install Slim framework
RUN composer require pecee/simple-router guzzlehttp/guzzle
WORKDIR /var/www/html

# Copy files
COPY www /var/www/html/


# Set rights
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
