FROM php:8.1-fpm

# Copy composer.lock and composer.json into the working directory
COPY composer.json /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    nano \
    xterm \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    unixodbc unixodbc-dev \
    curl \
    xdotool \
    expect

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions for php
RUN docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl pdo_odbc
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN getent group www || groupadd -g 1000 www
RUN getent passwd www || useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www/html/
COPY --chown=www:www-data . /var/www/html/storage/logs

# Copy all scripts into working directory
COPY ./docker/php/scripts /usr/scripts/

# Expose port 9000 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000
CMD ["php-fpm"]