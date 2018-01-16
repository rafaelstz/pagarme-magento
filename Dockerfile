FROM pagarme/magento

RUN apt-get update; exit 0
RUN apt-get -y install vim sendmail


COPY ./magentosmtp /opt/docker/magentosmtp
RUN /opt/docker/magentosmtp

EXPOSE 25

ENTRYPOINT ["/opt/docker/scripts/start"]
