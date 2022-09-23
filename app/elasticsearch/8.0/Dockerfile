FROM docker.elastic.co/elasticsearch/elasticsearch:8.0.0

USER root
RUN echo "vm.max_map_count=262144" >> /etc/sysctl.conf

# https://github.com/elastic/elasticsearch-docker

# Add your app_elasticsearch plugins setup here
# Example: RUN app_elasticsearch-plugin install analysis-icu
USER 1000