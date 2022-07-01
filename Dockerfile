FROM php:7.4-cli

RUN apt update && apt upgrade -y
RUN apt install -y software-properties-common --fix-missing
RUN apt install -y curl nano zip unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /app
COPY . /app
WORKDIR /app
VOLUME /app/dataset

RUN composer install