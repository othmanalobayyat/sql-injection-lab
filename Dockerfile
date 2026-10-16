# PHP 8.2 + Apache with the mysqli extension enabled.
# The PHP source code is NOT copied in here - it is mounted as a
# volume by docker-compose.yml, so code changes never need a rebuild.
FROM php:8.2-apache

RUN docker-php-ext-install mysqli
