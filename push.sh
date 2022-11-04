#!/bin/bash

#REGISTRY_URL='registry.gitlab.com/sc-rep/scoding/internal7/docker-images'
REGISTRY_URL='scodocker'

docker build -t $REGISTRY_URL/app_elasticsearch:8.0 app/elasticsearch/8.0/.
docker push $REGISTRY_URL/app_elasticsearch:8.0

docker build -t $REGISTRY_URL/app_kibana:8.0 app/kibana/8.0/.
docker push $REGISTRY_URL/app_kibana:8.0

docker build -t $REGISTRY_URL/app_mailhog:latest app/mailhog/latest/.
docker push $REGISTRY_URL/app_mailhog:latest

docker build -t $REGISTRY_URL/app_mariadb:latest app/mariadb/latest/.
docker push $REGISTRY_URL/app_mariadb:latest

docker build -t $REGISTRY_URL/app_nginx:latest app/nginx/latest/.
docker push $REGISTRY_URL/app_nginx:latest

docker build -t $REGISTRY_URL/app_php:8.1 app/php/8.1/.
docker push $REGISTRY_URL/app_php:8.1

docker build -t $REGISTRY_URL/app_php_xdebug:8.1 app/php_xdebug/8.1/.
docker push $REGISTRY_URL/app_php_xdebug:8.1

docker build -t $REGISTRY_URL/app_rabbitmq:latest app/rabbitmq/latest/.
docker push $REGISTRY_URL/app_rabbitmq:latest

docker build -t $REGISTRY_URL/app_redis:latest app/redis/latest/.
docker push $REGISTRY_URL/app_redis:latest

docker build -t $REGISTRY_URL/etaplius_elasticsearch:8.4.1 etaplius/elasticsearch/8.4.1/.
docker push $REGISTRY_URL/etaplius_elasticsearch:8.4.1

docker build -t $REGISTRY_URL/etaplius_kibana:8.4.1 etaplius/kibana/8.4.1/.
docker push $REGISTRY_URL/etaplius_kibana:8.4.1

docker build -t $REGISTRY_URL/etaplius_php:8.1 etaplius/php/8.1/.
docker push $REGISTRY_URL/etaplius_php:8.1

docker build -t $REGISTRY_URL/etaplius_rabbitmq:latest etaplius/rabbitmq/latest/.
docker push $REGISTRY_URL/etaplius_rabbitmq:latest

docker build -t $REGISTRY_URL/inventory_x_php:8.1 inventory-x/php/8.1/.
docker push $REGISTRY_URL/inventory_x_php:8.1