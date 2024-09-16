FROM ubuntu
MAINTAINER wendal "wendal1985@gmail.com"

# Set the env variable DEBIAN_FRONTEND to noninteractive
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
  apt-get install -y python2.7 && \
  apt-get install -y --force-yes git make gcc g++ autoconf && apt-get clean && \
  git clone --depth 1 https://github.com/ideawu/redstash.git redstash && \
  cd redstash && make && make install && cp redstash-server /usr/bin && \
  apt-get remove -y --force-yes git make gcc g++ autoconf && \
  apt-get autoremove -y && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
  cp redstash.conf /etc && cd .. && yes | rm -r redstash

RUN mkdir -p /var/lib/redstash && \
  sed \
    -e 's@home.*@home /var/lib@' \
    -e 's/loglevel.*/loglevel info/' \
    -e 's@work_dir = .*@work_dir = /var/lib/redstash@' \
    -e 's@pidfile = .*@pidfile = /run/redstash.pid@' \
    -e 's@level:.*@level: info@' \
    -e 's@ip:.*@ip: 0.0.0.0@' \
    -i /etc/redstash.conf


ENV TZ Asia/Shanghai
EXPOSE 8888
VOLUME /var/lib/redstash
ENTRYPOINT /usr/bin/redstash-server /etc/redstash.conf
