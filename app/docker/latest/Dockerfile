FROM docker:latest

WORKDIR /var/www/html

USER root

RUN apk update && apk upgrade && apk add --no-cache bash
RUN apk --update add wget \
		     curl \
		     git \
		     php \
		     php-curl \
		     php-openssl \
		     php-iconv \
		     php-json \
		     php-mbstring \
		     php-phar \
		     php-dom --repository http://nl.alpinelinux.org/alpine/edge/testing/ && rm /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

CMD ["/bin/bash"]

ENTRYPOINT ["/bin/bash", "-c"]