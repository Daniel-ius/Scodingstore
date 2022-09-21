## About project
This project stores all the images required for Scoding Technical stack.

### Creating & pushing docker images:
```shell
cd app_php/8.1
docker build -t registry.gitlab.com/internal7/symfony_app/app_php:8.1 .
docker push registry.gitlab.com/internal7/symfony_app/app_php:8.1
```