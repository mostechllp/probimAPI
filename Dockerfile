FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    netcat-openbsd \
    libgd-dev && \
    rm -rf /var/lib/apt/lists/*

# Generate locales
RUN locale-gen en_US.UTF-8

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl mysqli gd

# Set working directory
WORKDIR /var/www/html

# Copy composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set correct permissions
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]