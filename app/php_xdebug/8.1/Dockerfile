FROM scodocker/app_php:8.1

# Set working directory
WORKDIR /var/www/html

USER root
## xDebug
RUN pecl install xdebug
RUN install-php-extensions xdebug

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

#1000 = www-data
USER 1000
