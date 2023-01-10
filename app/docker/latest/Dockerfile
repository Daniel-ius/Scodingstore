FROM --platform=$BUILDPLATFORM docker:latest
ARG BUILDPLATFORM

WORKDIR /var/www/html
USER root
RUN apk update && apk upgrade && apk add php