FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php-memcached php7.2-pgsql php-redis unzip php7.2-zip \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


# Install Composer
RUN curl -sS https://getcomposer.org/installer |\
    php -- --install-dir=/usr/local/bin --filename=composer

# Cleanup
RUN rm -rf /var/cache/apk/*\
    && find / -type f -iname \*.apk-new -delete\
    && rm -rf /var/cache/apk/*

RUN chmod 0777 translations/ -R