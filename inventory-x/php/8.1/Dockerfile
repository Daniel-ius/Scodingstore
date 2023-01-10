FROM registry.gitlab.scoding.com/scoding-internal/docker-images/app_php:8.1

USER root

RUN docker-php-ext-install sysvsem

#1000 = www-data
USER 1000
