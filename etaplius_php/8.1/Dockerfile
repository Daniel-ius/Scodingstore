FROM registry.gitlab.com/sc-rep/scoding/internal7/docker-images/app_php:8.1

# Set working directory
WORKDIR /var/www/etaplius

# NPM and Node Latest
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash
RUN apt install -y nodejs
RUN curl https://www.npmjs.com/install.sh | sh

RUN apt-get update -y \
    && apt-get install -y nginx

RUN apt-get update && apt-get install -y supervisor
COPY docker/php/start-container /usr/local/bin/start-container
COPY docker/php/supervisord-production.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x /usr/local/bin/start-container

COPY --chown=www-data:www-data ./ /var/www/etaplius/
COPY ./docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

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

RUN /usr/local/bin/composer install && \
    npm install && \
    php bin/console messenger:stop-workers && \
    php bin/console c:c && \
    npm run build \
    && chmod -R 777 ./var/cache/

COPY ./docker/nginx/conf.d/app.prod.conf /etc/nginx/sites-enabled/default

COPY ./docker/nginx/ssl/fullchain1.pem /etc/nginx/ssl/server.crt
COPY ./docker/nginx/ssl/privkey1.pem /etc/nginx/ssl/server.key

COPY docker/elk/elasticsearch/config/certs /usr/share/elasticsearch/config/certs

EXPOSE 80 443

ENTRYPOINT ["start-container"]

