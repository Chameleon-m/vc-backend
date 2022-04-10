FROM nginx:1.21.4

RUN apt-get update -y && apt-get install -y --no-install-recommends \
        tree \
        nano

# Create group "d"
# set group id "1000"
RUN groupadd -g 1000 d
# Create user "d"
# set user id "1000"
# assign to existing group id "1000"
# set home directory "/home/d"
# set shell "/bin/bash"
RUN useradd -d /home/d -s /bin/bash -u 1000 -g 1000 d
# Create home directory
RUN mkdir /home/d
# User and group own home directory
RUN chown -R d:d /home/d

# Necessary steps to avoid permission errors
RUN touch /var/run/nginx.pid \
 && chown -R d:d /var/run/nginx.pid /var/cache/nginx

USER d

#ADD ./conf.d/vmeste.conf /etc/nginx/conf.d/vmeste.conf


WORKDIR /usr/share/nginx/vmeste