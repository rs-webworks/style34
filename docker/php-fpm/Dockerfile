FROM php:7.4-fpm
WORKDIR '/app'

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update
RUN apt-get -y --no-install-recommends install \
    libcurl4-openssl-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    libxslt-dev

RUN docker-php-ext-install \
    zip \
    soap \
    mbstring \
    pdo \
    pdo_mysql \
    xsl

# Install other soft
RUN apt-get -y install \
    git \
    nano \
    mc \
    unzip \
    gnupg

# Install PHP Redis extension
RUN pecl install -o -f redis \
  &&  rm -rf /tmp/pear \
  &&  docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer |\
    php -- --install-dir=/usr/local/bin --filename=composer

# Install yarn
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
    && apt-get install -y nodejs \
    && curl -o- -L https://yarnpkg.com/install.sh | bash

# Clean apt-get
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Cleanup
RUN rm -rf /var/cache/apk/*\
    && find / -type f -iname \*.apk-new -delete\
    && rm -rf /var/cache/apk/*

RUN usermod -u 1000 www-data
COPY php.ini /usr/local/etc/php/
