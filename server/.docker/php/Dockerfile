FROM php:7.4-fpm

MAINTAINER Chandan Sharma <chandan.sharma@introcept.co>

RUN addgroup --system www-data; exit 0
RUN adduser --system -G www-data -s /bin/sh -D www-data; exit 0

WORKDIR /var/www/superadmin

RUN sed -i "s/user = www-data/user = www-data/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = www-data/g" /usr/local/etc/php-fpm.d/www.conf

RUN apt-get update && apt-get install -y \
    wget \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    unzip \
    fontconfig \
    libfreetype6 \
    libjpeg62-turbo \
    libxrender1 \
    xfonts-75dpi \
    xfonts-base \
    default-mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd  zip xml dom
RUN apt-get update && \
    apt-get install -y \
        libc-client-dev libkrb5-dev && \
    rm -r /var/lib/apt/lists/*

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install -j$(nproc) imap
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    chmod +x /usr/local/bin/composer; \
    rm -rf /var/cache/apk/*;


WORKDIR /var/www/superadmin

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
