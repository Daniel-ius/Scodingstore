FROM registry.gitlab.com/sc-rep/scoding/internal7/docker-images/app_php_xdebug_{{platform}}:8.1

USER root

RUN docker-php-ext-install sysvsem

#1000 = www-data
USER 1000
