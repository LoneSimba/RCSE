FROM nginx/unit:1.26.1-php8.1

EXPOSE 8080
ARG ENV=development

COPY ./php.ini-${ENV} /usr/local/etc/php/php.ini
COPY ./nginx-config.json ./docker-entrypoint.d/config.json

RUN set -xe && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone

RUN apt update && \
    apt install nano git libzip-dev libicu-dev -y

RUN set -xe && \
    export DEBIAN_FRONTEND=noninteractive && \
    docker-php-ext-install zip pdo_mysql intl 

RUN if test "$ENV" = "development"; then \
        pecl install xdebug-3.1.2 && \
        docker-php-ext-enable xdebug && \
        echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
        echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
        echo "xdebug.start_with_request = yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
        echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; && \
        echo "xdebug.log_level = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN set -xe && \
    echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list && \
    apt update && \
    apt install symfony-cli -y

RUN apt install npm -y && \
    npm install --global yarn

