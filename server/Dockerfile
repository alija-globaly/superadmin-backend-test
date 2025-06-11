# Stage 1: Build PHP dependencies
FROM php:7.4-fpm AS build

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    wget \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libxslt1-dev \
    libreadline-dev \
    passwd \
 && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    xsl \
    soap \
    tokenizer \
    opcache

# Install Composer
RUN wget https://getcomposer.org/download/2.4.4/composer.phar && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Set proper ownership
RUN chown -R www-data:www-data /app

# Switch to non-root user
USER www-data

# Copy Laravel composer files and install dependencies
COPY --chown=www-data composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader


# Copy the rest of the code
COPY --chown=www-data:www-data . .

# RUN composer dump-autoload --optimize

RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Stage 2: Final Image
FROM php:7.4-fpm AS superadmin

WORKDIR /superadmin


# Install runtime dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    wget \
    vim \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libxslt1-dev \
 && rm -rf /var/lib/apt/lists/*

# Copy compiled PHP extensions and PHP config from build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy composer binary from build stage
COPY --from=build /usr/local/bin/composer /usr/local/bin/composer

# Copy application files from build stage
COPY --from=build --chown=www-data:www-data /app /superadmin

# Fix nginx directories for PID and body temp
RUN mkdir -p /var/lib/nginx/body && chown -R www-data:www-data /var/lib/nginx

# Remove default site
RUN rm -f /etc/nginx/sites-enabled/default

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf
COPY superadmin-backend.conf /etc/nginx/sites-available/superadmin-backend.conf
RUN ln -s /etc/nginx/sites-available/superadmin-backend.conf /etc/nginx/sites-enabled/


# Use non-root user
USER www-data