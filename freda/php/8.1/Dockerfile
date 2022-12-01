FROM scodocker/app_php:8.1

USER root

RUN apt-get update && \
apt-get install libldap2-dev -y && \
docker-php-ext-configure ldap && \
docker-php-ext-install ldap && \
apt-get install ldap-utils -y

#1000 = www-data
USER 1000
