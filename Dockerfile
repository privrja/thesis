FROM php:7.2-apache

# replace bourne shell to bash
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# install libraries
RUN apt-get update && apt-get install -y git unzip libxml2-dev zlib1g-dev

# install php needed extensions
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-install -j$(nproc) soap
RUN docker-php-ext-install -j$(nproc) pdo_mysql

#RUN apt-get install -y mysql-server

COPY wait-for-it.sh /usr/bin/wait-for-it
COPY vhost.conf /etc/apache2/sites-available/bbdgnc.conf

RUN chmod +x /usr/bin/wait-for-it

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD composer install ; wait-for-it 127.0.0.1:3307 -- bin/console doctrine:mig:mig ; bin/console doctrine:fixtures:load -n

ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN a2ensite bbdgnc.conf
RUN a2dissite 000-default.conf
RUN a2enmod rewrite
EXPOSE 80
