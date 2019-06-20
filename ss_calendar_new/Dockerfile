FROM php:5.6-apache

LABEL maintaine gunjianpan '<iofu728@163.com>'

ENV THINKPHP_VERSION=5.0.21
ENV APACHE_DIR=/etc/apache2/

# WORKDIR /var/www/html

EXPOSE 80

RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-install pdo pdo_mysql

RUN ln -s ${APACHE_DIR}mods-available/rewrite.load ${APACHE_DIR}mods-enabled/rewrite.load \
    && sed -i 's/AllowOverride None/AllowOverride All/g' ${APACHE_DIR}apache2.conf \
    && sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/g' ${APACHE_DIR}sites-enabled/000-default.conf

RUN curl -OL https://github.com/top-think/framework/archive/v${THINKPHP_VERSION}.tar.gz \
    && tar xvf v${THINKPHP_VERSION}.tar.gz && mv framework-${THINKPHP_VERSION} thinkphp



