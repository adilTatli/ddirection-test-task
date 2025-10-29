FROM alpine:3.20

RUN apk add --no-cache \
    php82 php82-fpm php82-opcache php82-session \
    php82-mbstring php82-pdo php82-pdo_mysql \
    php82-json php82-ctype php82-fileinfo php82-tokenizer \
    php82-xml php82-xmlwriter php82-xmlreader \
    php82-curl php82-zip php82-gd php82-bcmath \
    php82-pecl-redis \
    mariadb-client \
    bash curl git zip unzip composer

WORKDIR /var/www/html

COPY docker/php/zz-listen.conf /etc/php82/php-fpm.d/zz-listen.conf
COPY docker/php/entrypoint.sh /usr/local/bin/docker-entrypoint

RUN chmod +x /usr/local/bin/docker-entrypoint \
    && sed -i 's/\r$//' /usr/local/bin/docker-entrypoint


ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]

CMD ["php-fpm82","-F"]
