FROM alpine:3.16

RUN apk update && apk add --no-cache \
    php81 \
    php81-common \
    php81-fpm \
    php81-pdo \
    php81-opcache \
    php81-gd \
    php81-zip \
    php81-phar \
    php81-iconv \
    php81-cli \
    php81-curl \
    php81-openssl \
    php81-mbstring \
    php81-tokenizer \
    php81-fileinfo \
    php81-json \
    php81-xml \
    php81-xmlwriter \
    php81-simplexml \
    php81-dom \
    php81-pdo_mysql \
    php81-pdo_sqlite \
    php81-tokenizer \
    php81-pecl-redis \
    php81-pcntl \
    php81-posix

RUN ln -sf /usr/bin/php81 /usr/bin/php

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

ARG userid=1000
ARG groupid=1000

WORKDIR /app
COPY . .

RUN apk add curl
RUN apk add nano

COPY .docker/config/php8.1/php.ini-development /etc/php81/php.ini

RUN addgroup -g $userid appgroup
RUN adduser -D -u $groupid appuser -G appgroup
RUN chown -R appuser:appgroup /app

USER appuser

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]