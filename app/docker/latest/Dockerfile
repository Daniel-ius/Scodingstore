FROM docker:latest

WORKDIR /var/www/html

USER root

RUN apk update && apk upgrade && apk add --no-cache bash

CMD ["/bin/bash"]

ENTRYPOINT ["/bin/bash", "-c"]