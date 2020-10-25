FROM php:7.4.11-cli-alpine3.12

WORKDIR /www/challenge

RUN apk --update add curl && rm /var/cache/apk/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ENTRYPOINT ["sh", "/entrypoint.sh"]
