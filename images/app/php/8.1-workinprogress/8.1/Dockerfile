FROM --platform=linux/amd64 registry.gitlab.scoding.com/scoding-internal/docker-images/app_php:8.1

# Set working directory
WORKDIR /var/www/html

USER root

## TODO: Needs to be splitted into different docker image as a service.
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash
RUN apt install -y nodejs
RUN curl https://www.npmjs.com/install.sh | sh

## TODO: Needs to be splitted into different docker image as a service.
RUN apt-get update && apt-get install -y supervisor
COPY start-container /usr/local/bin/start-container
COPY supervisord-production.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x /usr/local/bin/start-container

### TODO: it is not needed if you use a proper user. USER 1000 - which is www-data
COPY --chown=www-data:www-data ./ /var/www/html/
COPY local.ini /usr/local/etc/php/conf.d/local.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY .env.production .env
# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

## TODO: Needs to be splitted into different docker image as a service.
COPY etaplius-crontab /etc/cron.d/etaplius-crontab
RUN apt-get update && apt-get -y install cron \
    && chmod 0644 /etc/cron.d/etaplius-crontab \
    && crontab /etc/cron.d/etaplius-crontab \
    && touch /var/log/cron.log \
    && service cron start  \

## TODO: Needs to be splitted into different docker image as a service.
RUN apt-get update -y \
    && apt-get install -y nginx
COPY nginx/conf.d/app.prod.conf /etc/nginx/sites-enabled/default
COPY nginx/ssl/fullchain1.pem /etc/nginx/ssl/server.crt
COPY nginx/ssl/privkey1.pem /etc/nginx/ssl/server.key

COPY elasticsearch/certs /usr/share/elasticsearch/config/certs

EXPOSE 80 443

ENTRYPOINT ["start-container"]


