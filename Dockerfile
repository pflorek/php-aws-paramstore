FROM php:alpine
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /project