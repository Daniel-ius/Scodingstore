FROM --platform=linux/amd64 registry.gitlab.scoding.com/scoding-internal/docker-images/app_php:8.1

# Set working directory
WORKDIR /var/www/etaplius

USER root

# NPM and Node Latest
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash
RUN apt install -y nodejs
RUN curl https://www.npmjs.com/install.sh | sh

RUN apt-get update -y \
    && apt-get install -y nginx

RUN apt-get update && apt-get install -y supervisor
COPY start-container /usr/local/bin/start-container
COPY supervisord-production.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x /usr/local/bin/start-container

COPY --chown=www-data:www-data ./ /var/www/etaplius/
COPY local.ini /usr/local/etc/php/conf.d/local.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY .env.production .env
# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

COPY etaplius-crontab /etc/cron.d/etaplius-crontab

ARG CRON_ENABLED=true
RUN if [ $CRON_ENABLED = true ]; then \
    apt-get update && apt-get -y install cron \
    && chmod 0644 /etc/cron.d/etaplius-crontab \
    && crontab /etc/cron.d/etaplius-crontab \
    && touch /var/log/cron.log \
    && service cron start  \
; fi

RUN if [ -f "composer.json" ]; then \
   /usr/local/bin/composer install && \
   npm install && \
   php bin/console messenger:stop-workers && \
   php bin/console c:c && \
   npm run build \
   && chmod -R 777 ./var/cache/ \
; fi

COPY nginx/conf.d/app.prod.conf /etc/nginx/sites-enabled/default

COPY nginx/ssl/fullchain1.pem /etc/nginx/ssl/server.crt
COPY nginx/ssl/privkey1.pem /etc/nginx/ssl/server.key

COPY elasticsearch/certs /usr/share/elasticsearch/config/certs

EXPOSE 80 443

ENTRYPOINT ["start-container"]


