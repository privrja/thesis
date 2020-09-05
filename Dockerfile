FROM php:7.2-apache

WORKDIR /var/www/html

# replace bourne shell to bash
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# install libraries
RUN apt-get update && \
    apt-get install -y \
        libxml2-dev \
        zlib1g-dev \
        git \
        unzip

# install php needed extensions
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-install -j$(nproc) soap

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '8a6138e2a05a8c28539c9f0fb361159823655d7ad2deecb371b04a83966c61223adc522b0189079e3e9e277cd72b8897') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"

# copy code to right dir
COPY . /var/www/html

# install php dependecies
RUN /var/www/html/composer.phar install --no-dev

# configure Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN cp vhost.conf /etc/apache2/sites-available/bbdgnc.conf
RUN a2ensite bbdgnc.conf
RUN a2dissite 000-default.conf
RUN a2enmod rewrite
EXPOSE 80

# run apache
CMD ["apachectl", "-D",  "FOREGROUND"]
