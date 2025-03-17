FROM dunglas/frankenphp:1.4-php8.4.3

RUN apt update && apt install -y wget unzip sqlite3 cron tmux htop procps \
    wget \
    unzip \
    sqlite3 \
    build-essential \
    libssl-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-source extract

RUN install-php-extensions \
 pdo_mysql \
 gd \
 intl \
 zip \
 opcache \
 bcmath \
 amqp \
 mongodb

RUN docker-php-source extract \
    && curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/6.1.0.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mv phpredis-6.1.0 /usr/src/php/ext/redis

RUN install-php-extensions \
 redis

ENV SERVER_NAME=:8081
ENV APP_RUNTIME=Runtime\\FrankenPhpSymfony\\Runtime
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

COPY . /app/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

RUN composer install --no-dev --no-scripts --no-progress --prefer-dist

RUN chown -R root:root /app/var \
    && chmod -R 775 /app/var

EXPOSE 8081