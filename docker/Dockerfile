FROM php:8.1-apache-bullseye

WORKDIR /var/www/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Setup context
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"

# Quelques library necessaires
RUN apt-get update \
    &&  apt-get install -y --no-install-recommends \
    locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip

# les locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen  \
    &&  echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
    &&  locale-gen

# On télécharge et deplace composer
RUN  curl -sS https://getcomposer.org/installer | php -- \
    &&  mv composer.phar /usr/local/bin/composer

# On télécharge et deplace le symfony cli
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin \
    && docker-php-ext-configure \
    intl \
    && docker-php-ext-install \
    pdo pdo_mysql opcache intl zip calendar dom mbstring gd xsl \
    \
    && pecl install apcu && docker-php-ext-enable apcu \
    && pecl install xdebug && docker-php-ext-enable xdebug
#    && echo 'zend_extension=xdebug' >> /usr/local/etc/php/php.ini \
#    && echo 'xdebug.mode=develop,debug' >> /usr/local/etc/php/php.ini \
#    && echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/php.ini \
#    && echo 'xdebug.start_with_request=yes' >> /usr/local/etc/php/php.ini \
#    && echo 'session.save_path = "/tmp"' >> /usr/local/etc/php/php.ini


# On créé un utilisateur avec le même gid/uid que le local
RUN addgroup --system redpanda --gid 1000 && adduser --system redpanda --uid 1000 --ingroup redpanda
RUN #usermod -u 1000 www-data

#zend_extension=xdebug
#xdebug.mode=develop,debug
#xdebug.client_host=host.docker.internal
#xdebug.start_with_request=yes
#session.save_path = "/tmp"
#xdebug.discover_client_host=1