FROM phpdockerio/php74-fpm:latest
WORKDIR "/application"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install git php-memcached php7.4-mysql php7.4-sqlite3 php7.4-gd php7.4-intl php7.4-xmlrpc php7.4-xsl php7.4-soap \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

CMD /application/docker/php-fpm/boot.sh
