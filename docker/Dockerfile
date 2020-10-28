FROM php:7.4.11-cli-alpine3.12

WORKDIR /www/challenge

RUN apk --update add curl && rm /var/cache/apk/* && \
    docker-php-ext-install pdo_mysql && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ENTRYPOINT ["sh", "/entrypoint.sh"]
