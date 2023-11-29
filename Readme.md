# Scoding store

## Overview
This was my internships final project.

## Prerequisites
1. Docker or Symfony cli
2. PHP 8.2



## Installation
```bash
cp .env.dist .env
```

1. For docker run
```bash
cp docker-compose.dist.yml docker-compose.yml 
```
```bash
docker-compose up -d
```
```bash 
docker-compose exec images_php composer install
docker-compose exec images_php yarn install
```
```bash
docker-compose exec images_php php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec images_php php bin/console doctrine:fixtures:load --no-interaction 
```
2. For symfony cli run
```bash 
composer install
yarn install
```
```bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction 
```