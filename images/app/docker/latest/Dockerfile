FROM --platform=linux/amd64 docker:latest

WORKDIR /var/www/html
USER root
RUN apk update && apk upgrade

RUN apk update && apk add wget \
		     curl \
		     git \
		     php \
		     php-common \
		     php-curl \
		     php-openssl \
		     php-iconv \
		     php-json \
		     php-mbstring \
		     php-phar \
		     php-ctype \
		     php-tokenizer \
		     php-gd \
		     php-zip \
		     php-xml \
		     php-xmlwriter \
		     php-simplexml \
		     php-session \
		     php-posix \
		     php-dom --repository http://nl.alpinelinux.org/alpine/edge/testing/ && rm /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN apk add nodejs-current npm
RUN npm install --global yarn
RUN mkdir -p /.cache && mkdir -p /.yarn && chown -R 1000:1000 /.cache && chown -R 1000:1000 /.yarn && touch /.yarnrc && chown -R 1000:1000 /.yarnrc