# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# persistent / runtime deps
RUN apk add --no-cache \
      acl \
      fcgi \
      file \
      gettext \
      git \
      -U --no-cache -t .wodby-php-edge-run-deps -X http://dl-cdn.alpinelinux.org/alpine/edge/testing/ \
   ;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

# Optional, force UTC as server time
RUN echo "UTC" > /etc/timezone

# Install mbstring extension
#RUN apk add --no-cache \
#    && docker-php-ext-install mbstring mysqli pdo pdo_mysql tokenizer pcntl\
#    && docker-php-ext-enable mbstring mysqli pdo pdo_mysql tokenizer pcntl \
#    && rm -rf /tmp/*
RUN apk add --update \
    php7-apcu \
    php7-curl \
    php7-gd \
    php7-json \
    php7-intl \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_mysql \
    php7-pdo_pgsql \
    php7-tokenizer \
    php7-xml \
    php7-zlib \
    php7-pcntl \
    php7-xdebug \
    curl

# Set working directory
WORKDIR /var/www/html

# Install utilities and shadow for permissions chmod commands
RUN apk update && apk add apache2 bash nano shadow

# On créé un utilisateur avec le même gid/uid que le local
RUN addgroup --system redpanda --gid 1000 && adduser --system redpanda --uid 1000 --ingroup redpanda

# Install composer
ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#add symfony installator
RUN mkdir -p /usr/local/bin \
    && curl -sSL https://symfony.com/installer -o /usr/local/bin/symfony \
    && chmod a+x /usr/local/bin/symfony


#FROM php:7.0.4-apache
#
## install the PHP extensions we need
#RUN apt-get update && apt-get install -y libpng12-dev libjpeg-dev libpq-dev git wget\
#	&& rm -rf /var/lib/apt/lists/* \
#	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
#	&& docker-php-ext-install gd mbstring opcache pdo pdo_mysql pdo_pgsql zip
#
## Install composer
#ENV COMPOSER_HOME /composer
#ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
#ENV COMPOSER_ALLOW_SUPERUSER 1
#COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
#
#WORKDIR /root
#
## Optional, force UTC as server time
#RUN echo "UTC" > /etc/timezone
#
#WORKDIR /var/www/html