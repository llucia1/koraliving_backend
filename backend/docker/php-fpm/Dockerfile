FROM alpine:3.21

# ash (default shell of Alpine) will only read .profile if started as a login shell with sh -l
# This must be done first of all so Alpine loads the .profile file
ENV ENV="/root/.profile"
RUN echo 'alias ll="ls -lah --color"' | tee "$ENV" /root/.bashrc

ENV TERM xterm-256color

RUN apk add --no-cache \
    coreutils \
    gettext \
    php84=8.4.5-r0 \
    php84-fpm=8.4.5-r0 \
    php84-ctype \
    php84-curl \
    php84-dom \
    php84-gd \
    php84-mbstring \
    php84-iconv \
    php84-json \
    php84-intl \
    php84-fileinfo\
    php84-bcmath \
    php84-opcache \
    php84-openssl \
    php84-mysqli \
    php84-pgsql \
    php84-sqlite3 \
    php84-pdo \
    php84-pdo_mysql \
    php84-pdo_sqlite \
    php84-pecl-imagick \
    php84-sockets \
    php84-xml \
    php84-xsl \
    php84-soap \
    php84-zlib \
    php84-phar \
    php84-tokenizer \
    php84-session \
    php84-simplexml \
    php84-zip \
    php84-xmlreader \
    php84-xmlwriter \
    php84-sodium \
    php84-pecl-apcu \
    php84-pcntl \
    php84-xdebug \
    make \
    git \
    curl \
    util-linux \
    shadow \
    openrc \
    sudo \
    bash \
    bash-completion \
    vim

RUN ln -s `which php84` /usr/local/bin/php

# Install and enable php84-xdebug if $PHP_XDEBUG_MODE is not empty
ARG PHP_XDEBUG_MODE=off
ARG PHP_XDEBUG_CLIENT_PORT=9003
ARG PHP_XDEBUG_CLIENT_HOST=host.docker.internal
COPY xdebug.ini /etc/php84/conf.d/xdebug.ini.template
RUN if [ "$PHP_XDEBUG_MODE" != "" ]; then \
        apk add --no-cache php84-xdebug; \
        export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
        export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
        export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
        envsubst < /etc/php84/conf.d/xdebug.ini.template > /etc/php84/conf.d/50_xdebug.ini; \
    fi
RUN rm -f /etc/php84/conf.d/xdebug.ini.template

# Install composer
RUN curl -s https://getcomposer.org/installer | php84 \
  && chmod a+x composer.phar \
  && mv composer.phar /usr/local/bin/composer \
  && composer completion bash | sudo tee /etc/bash_completion.d/composer >/dev/null

# Install Symfony CLI
ARG PHP_FPM_SYMFONY_VERSION="latest"
 RUN if [ "$PHP_FPM_SYMFONY_VERSION" != "latest" ]; then \
         wget https://github.com/symfony-cli/symfony-cli/releases/download/v$PHP_FPM_SYMFONY_VERSION/symfony-cli_linux_amd64.tar.gz; \
     else \
         wget https://github.com/symfony-cli/symfony-cli/releases/latest/download/symfony-cli_linux_amd64.tar.gz; \
     fi \
     && tar xzvf symfony-cli_linux_amd64.tar.gz symfony \
     && mv symfony /usr/local/bin/






RUN apk add --no-cache --update nodejs npm \
    && npm install --global yarn

# Add personalized PHP configurations
COPY symfony.ini /etc/php84/conf.d/99_symfony.ini
COPY symfony.ini /etc/php84/cli/conf.d/99_symfony.ini

#COPY symfony.pool.conf /etc/php84/php-fpm.d/
ARG PHP_FPM_PORT=9001
COPY symfony.pool.conf /tmp/symfony.pool.conf.template
RUN envsubst '\$PHP_FPM_PORT' < /tmp/symfony.pool.conf.template > /etc/php84/php-fpm.d/symfony.pool.conf; \
    rm -fR /tmp/symfony.pool.conf.template;

#RUN rm -f /etc/php84/php-fpm.d/www.conf

# Add user and group
ARG HOST_USER_ID=1000
ARG HOST_GROUP_ID=1000
RUN addgroup --gid $HOST_GROUP_ID symfony \
  && adduser --ingroup symfony -D -g "" -h /home/symfony -s /bin/bash -u $HOST_USER_ID symfony \
  && mkdir -p /var/www/symfony \
  && chown -R symfony:symfony /var/www/symfony \
  && echo 'symfony:symfony' | chpasswd

WORKDIR /var/www/symfony
RUN chmod 777 /var/log/php84

CMD ["php-fpm84", "-F"]
