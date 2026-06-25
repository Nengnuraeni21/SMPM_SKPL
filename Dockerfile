FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN a2enmod rewrite

# Fix Apache MPM conflict
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

COPY . /var/www/html/

EXPOSE 80