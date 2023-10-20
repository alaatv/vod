# Accepted values: 8.1 - 8.0
ARG PHP_VERSION=8.1

ARG COMPOSER_VERSION=2.3.10

###########################################
# PHP dependencies
###########################################

FROM composer:${COMPOSER_VERSION} AS vendor
WORKDIR /var/www/html
COPY composer* ./
RUN composer install \
	--no-dev \
	--no-interaction \
	--prefer-dist \
	--ignore-platform-reqs \
	--optimize-autoloader \
	--apcu-autoloader \
	--ansi \
	--no-scripts

###########################################

FROM node:16 as nodejs

WORKDIR /var/www/html

COPY webpack* ./
COPY public ./public
COPY resources ./resources
COPY package* ./

RUN npm ci
RUN npm run dev



##################################

FROM php:${PHP_VERSION}-alpine3.16

LABEL maintainer="Seyed Morteza Ebadi <seyed.me720@gmail.com>"

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG TZ=UTC

# Accepted values: app - horizon - scheduler
ARG CONTAINER_MODE=app

ARG APP_WITH_HORIZON=true
ARG APP_WITH_SCHEDULER=true

ENV TERM=xterm-color \
	CONTAINER_MODE=${CONTAINER_MODE} \
	APP_WITH_HORIZON=${APP_WITH_HORIZON} \
	APP_WITH_SCHEDULER=${APP_WITH_SCHEDULER}

ENV ROOT=/var/www/html
WORKDIR $ROOT

SHELL ["/bin/sh", "-eou", "pipefail", "-c"]

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
	&& echo $TZ > /etc/timezone

RUN apk update; \
	apk upgrade; \
	pecl -q channel-update pecl.php.net;

ENV GOSU_VERSION 1.14

RUN set -eux; \
	\
	apk add --no-cache --virtual .gosu-deps \
	wget \
	ca-certificates \
	dpkg \
	gnupg \
	; \
	\
	dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')"; \
	wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch"; \
	wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc"; \
	\
	# verify the signature
	export GNUPGHOME="$(mktemp -d)"; \
	gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4; \
	gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu; \
	command -v gpgconf && gpgconf --kill all || :; \
	rm -rf "$GNUPGHOME" /usr/local/bin/gosu.asc; \
	\
	# clean up fetch dependencies
	apk del --no-network .gosu-deps; \
	\
	chmod +x /usr/local/bin/gosu; \
	# verify that the binary works
	gosu --version; \
	gosu nobody true

###########################################
# pdo_mysql
###########################################

ARG INSTALL_PDO_MYSQL=true

RUN if [ ${INSTALL_PDO_MYSQL} = true ]; then \
	docker-php-ext-install pdo_mysql; \
	fi

###########################################
# mbstring
###########################################

RUN apk add --no-cache  oniguruma-dev; \
	docker-php-ext-install mbstring;

###########################################
# OPcache
###########################################

ARG INSTALL_OPCACHE=true

RUN if [ ${INSTALL_OPCACHE} = true ]; then \
	docker-php-ext-install opcache; \
	fi

###########################################
# PHP Redis
###########################################

ARG INSTALL_PHPREDIS=true

RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
	apk add --no-cache pcre-dev $PHPIZE_DEPS; \
	pecl install redis; \
	docker-php-ext-enable redis.so; \
	fi

###########################################
# PCNTL
###########################################

ARG INSTALL_PCNTL=true

RUN if [ ${INSTALL_PCNTL} = true ]; then \
	docker-php-ext-install pcntl; \
	fi

###########################################
# BCMath
###########################################

ARG INSTALL_BCMATH=true

RUN if [ ${INSTALL_BCMATH} = true ]; then \
	docker-php-ext-configure bcmath \
	&& docker-php-ext-install bcmath; \
	fi

###########################################
# OpenSwoole/Swoole extension
###########################################

ARG INSTALL_SWOOLE=true
ARG SERVER=swoole


RUN if [ ${INSTALL_SWOOLE} = true ]; then \
	apk add --no-cache --virtual .build-deps \
	linux-headers; \
	pecl install ${SERVER}; \
	docker-php-ext-enable ${SERVER}; \
	apk del .build-deps  linux-headers; \
	rm -rf /var/cache/apk/*; \
	fi

###########################################################################
# Human Language and Character Encoding Support
###########################################################################

ARG INSTALL_INTL=true

RUN if [ ${INSTALL_INTL} = true ]; then \
	apk add --no-cache icu-dev \
	&& docker-php-ext-configure intl \
	&& docker-php-ext-install intl; \
	fi

###########################################
# Memcached
###########################################

ARG INSTALL_MEMCACHED=true

RUN if [ ${INSTALL_MEMCACHED} = true ]; then \
	apk add --no-cache zlib-dev libmemcached-dev \
	&& pecl install memcached \
	&& docker-php-ext-enable memcached; \
	fi

###########################################
# MySQL Client
###########################################

ARG INSTALL_MYSQL_CLIENT=true

RUN if [ ${INSTALL_MYSQL_CLIENT} = true ]; then \
	apk add --no-cache mysql-client; \
	fi

###########################################
# MongoDB client
###########################################

ARG INSTALL_MONGODB_CLIENT=true

RUN if [ ${INSTALL_MONGODB_CLIENT} = true ]; then \
	apk add --no-cache icu-dev cyrus-sasl-dev snappy-dev zlib-dev \
	&& pecl install mongodb \
	&& docker-php-ext-enable mongodb; \
	fi

###########################################
# gd
###########################################

ARG INSTALL_GD=true

RUN if [ ${INSTALL_GD} = true ]; then \
	apk add --no-cache freetype-dev libjpeg-turbo-dev libpng-dev libxpm-dev \
	&& docker-php-ext-install gd \
	&& docker-php-ext-enable gd; \
	fi

###########################################
# igbinary
###########################################

ARG INSTALL_IGBINARY=true
RUN if [ ${INSTALL_IGBINARY} = true ]; then \
	pecl install igbinary \
	&& docker-php-ext-enable igbinary; \
	fi

###########################################
# msgpack
###########################################

ARG INSTALL_MSGPACK=true

RUN if [ ${INSTALL_MSGPACK} = true ]; then \
	pecl install msgpack \
	&& docker-php-ext-enable msgpack; \
	fi

###########################################
# soap
###########################################

ARG INSTALL_SOAP=true

RUN if [ ${INSTALL_SOAP} = true ]; then \
	apk add --no-cache libxml2-dev \
	&& docker-php-ext-configure soap \
	&& docker-php-ext-install soap \
	&& docker-php-ext-enable soap; \
	fi

###########################################
# zip
###########################################

ARG INSTALL_ZIP=true

RUN if [ ${INSTALL_ZIP} = true ]; then \
	apk add --no-cache libzip cmake gnutls-dev libzip-dev zlib-dev \
	&& docker-php-ext-configure zip \
	&& docker-php-ext-install zip \
	&& docker-php-ext-enable zip; \
	fi


###########################################
# Laravel scheduler
###########################################

RUN if [ ${CONTAINER_MODE} = 'scheduler' ] || [ ${APP_WITH_SCHEDULER} = true ]; then \
	wget -q "https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64" \
	-O /usr/bin/supercronic \
	&& chmod +x /usr/bin/supercronic \
	&& mkdir -p /etc/supercronic \
	&& echo "*/1 * * * * php ${ROOT}/artisan schedule:run --verbose --no-interaction" > /etc/supercronic/laravel; \
	fi

###########################################

RUN addgroup -g $WWWGROUP octane \
	&& adduser -s /bin/sh -G octane -u $WWWUSER -D octane

RUN docker-php-source delete \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*;

COPY . .
COPY --from=vendor ${ROOT}/vendor vendor
COPY --from=nodejs ${ROOT}/public public

RUN apk add --no-cache supervisor

RUN mkdir -p \
	storage/framework/{sessions,views,cache} \
	storage/logs \
	bootstrap/cache \
	&& chown -R octane:octane \
	storage \
	bootstrap/cache \
	&& chmod -R ug+rwx storage bootstrap/cache \
	&& mkdir -p /var/log/supervisor \
	&& touch /var/log/supervisor/supervisord.log \
	&& chmod ug+rw /var/log/supervisor/supervisord.log;

COPY deployment/octane/supervisord* /etc/supervisor/conf.d/
COPY deployment/octane/php.ini /usr/local/etc/php/conf.d/octane.ini
COPY deployment/octane/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN chmod +x deployment/octane/entrypoint.sh
RUN cat deployment/octane/utilities.sh >> ~/.bashrc


RUN chmod 777 /var/www/html/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer;


EXPOSE 9000

ENTRYPOINT ["deployment/octane/entrypoint.sh"]

HEALTHCHECK --start-period=5s --interval=2s --timeout=5s --retries=8 CMD php artisan octane:status || exit 1
