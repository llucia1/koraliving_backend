FROM nginx:1.19.6

RUN mkdir -p /etc/nginx/ssl

COPY nginx.conf /etc/nginx/

ARG NGINX_SYMFONY_SERVER_NAME
#RUN openssl req -x509 -new -sha256 -newkey rsa:2048 -nodes -days 365 -subj "/CN=$NGINX_SYMFONY_SERVER_NAME" -keyout /etc/nginx/ssl/ssl-cert.key -out /etc/nginx/ssl/ssl-cert.pem
COPY certs/* /etc/nginx/ssl
RUN update-ca-certificates

COPY symfony.local.conf /tmp/symfony.local.conf.template
RUN envsubst '\$NGINX_SYMFONY_SERVER_NAME' < /tmp/symfony.local.conf.template > /etc/nginx/conf.d/symfony.local.conf; \
    rm /etc/nginx/conf.d/default.conf; \
    rm -fR /tmp/symfony.local.conf.template;

#RUN echo "upstream php-upstream { server php-fpm:9001; }" > /etc/nginx/conf.d/upstream.conf
#COPY php-upstream.conf /etc/nginx/conf.d/upstream.conf
ARG PHP_FPM_PORT=9001
COPY php-upstream.conf /tmp/php-upstream.conf.template
RUN envsubst '\$PHP_FPM_PORT' < /tmp/php-upstream.conf.template > /etc/nginx/conf.d/upstream.conf; \
    rm -fR /tmp/php-upstream.conf.template;

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
