FROM --platform=linux/amd64 php:7.4.32-fpm

# Set working directory
WORKDIR /var/www/html

ARG APCU_VERSION=5.1.21

RUN runtimeDeps=" \
        curl \
        git \
        zip \
        libc-client-dev \
        libkrb5-dev \
        libxml2-dev \
        libonig-dev \
        libjpeg62-turbo-dev \
    " \
    && apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y $runtimeDeps \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install iconv mbstring imap \
    && rm -r /var/lib/apt/lists/*

RUN apt-get update -y && apt-get install -y sendmail

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev
RUN docker-php-ext-install zip

RUN docker-php-ext-install exif
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis
RUN apt-get install -y --no-install-recommends libssl-dev zlib1g-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-install opcache
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp
RUN pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	;
## xDebug
#RUN pecl install xdebug
#RUN install-php-extensions xdebug
# BCMath
RUN docker-php-ext-install bcmath
# Symfony requirement
RUN docker-php-ext-install intl
# SSH
RUN install-php-extensions ssh2
# Change current user to www
USER root

RUN cat /etc/hosts

RUN if ! grep -q "gitlab.scoding.com" /etc/hosts; then echo "172.17.2.161 gitlab.scoding.com" | sudo tee -a /etc/hosts; fi

COPY local.ini /usr/local/etc/php/conf.d/local.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN chown -R www-data:www-data ./

RUN apt-get update -y && apt-get install -y sendmail libpng-dev

RUN apt-get install -y \
        libwebp-dev \
        libpng-dev

RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg && \
    docker-php-ext-install gd

# Installing node
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install nodejs -y
RUN npm install --global yarn
RUN mkdir -p /.cache && mkdir -p /.yarn && chown -R 1000:1000 /.cache && chown -R 1000:1000 /.yarn && chown -R 1000:1000 /usr/bin/yarn && touch /.yarnrc && chown -R 1000:1000 /.yarnrc

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

#1000 = www-data
USER 1000
