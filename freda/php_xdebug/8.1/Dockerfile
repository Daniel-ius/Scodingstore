FROM registry.gitlab.com/sc-rep/scoding/internal7/docker-images/freda_php_{{platform}}:8.1

# Set working directory
WORKDIR /var/www/html

USER root
## xDebug
RUN pecl install xdebug

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

#1000 = www-data
USER 1000
