FROM phpdockerio/php73-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Proxy config
COPY 99HttpProxy /etc/apt/apt.conf.d/
ENV HTTP_PROXY "http://proxy.intr.cortex.cz:3128"
ENV HTTPS_PROXY "http://proxy.intr.cortex.cz:3128"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php-memcached php7.3-pgsql php7.3-redis unzip php7.3-zip php7.3-http \
    && apt-get -y install php-xml php-soap php-http

# Install git
RUN apt-get -y install git

# Install Composer
#RUN curl -sS https://getcomposer.org/installer |\
#    php -- --install-dir=/usr/local/bin --filename=composer

RUN curl --proxy http://proxy.intr.cortex.cz:3128 -sS https://getcomposer.org/installer |\
    php -- --install-dir=/usr/local/bin --filename=composer


# Make translations directory writtable from web
#RUN chmod 0777 /application/translations/ -R

# Install Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update && apt install yarn -y

# Clean apt-get
RUN apt clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Cleanup
#RUN rm -rf /var/cache/apk/*\
#    && find / -type f -iname \*.apk-new -delete\
#    && rm -rf /var/cache/apk/*

# Download Browsercap.ini file:
#RUN php vendor/bin/browscap-php browscap:fetch