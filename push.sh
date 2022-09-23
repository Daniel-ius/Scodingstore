#!/bin/bash

REGISTRY_URL='registry.gitlab.com/sc-rep/scoding/internal7/docker-images'

docker build -t $REGISTRY_URL/app_elasticsearch:8.0 app_elasticsearch/8.0/.
docker push $REGISTRY_URL/app_elasticsearch:8.0

docker build -t $REGISTRY_URL/app_kibana:8.0 app_kibana/8.0/.
docker push $REGISTRY_URL/app_kibana:8.0

docker build -t $REGISTRY_URL/app_mailhog:latest app_mailhog/latest/.
docker push $REGISTRY_URL/app_mailhog:latest

docker build -t $REGISTRY_URL/app_mariadb:latest app_mariadb/latest/.
docker push $REGISTRY_URL/app_mariadb:latest

docker build -t $REGISTRY_URL/app_nginx:latest app_nginx/latest/.
docker push $REGISTRY_URL/app_nginx:latest

docker build -t $REGISTRY_URL/app_php:8.1 app_php/8.1/.
docker push $REGISTRY_URL/app_php:8.1

docker build -t $REGISTRY_URL/app_php_xdebug:8.1 app_php_xdebug/8.1/.
docker push $REGISTRY_URL/app_php_xdebug:8.1

docker build -t $REGISTRY_URL/app_rabbitmq:latest app_rabbitmq/latest/.
docker push $REGISTRY_URL/app_rabbitmq:latest

docker build -t $REGISTRY_URL/app_redis:latest app_redis/latest/.
docker push $REGISTRY_URL/app_redis:latest