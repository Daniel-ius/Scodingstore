## About project
This project stores all the images required for Scoding Technical stack.

### Creating & pushing docker images:
```shell
cd app_php/8.1
docker build -t registry.gitlab.scoding.com/scoding-internal/docker-images/app_php:8.1 .
docker push registry.gitlab.scoding.com/scoding-internal/docker-images/app_php:8.1
```