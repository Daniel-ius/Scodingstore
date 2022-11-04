FROM scodocker/app_php_xdebug:8.1

USER root

RUN docker-php-ext-install sysvsem

#1000 = www-data
USER 1000
