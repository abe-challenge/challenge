until nc -z ${MYSQL_SERVER_NAME} ${MYSQL_PORT}; do sleep 1; echo "Waiting for MySQL"; done

composer install --no-interaction

php -S 0.0.0.0:${SERVER_PORT} -t /www/challenge/public ./public/router.php
