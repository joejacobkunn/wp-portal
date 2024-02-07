FROM --platform=linux/amd64 recybertech/php-mysql:8.1

# from compose args
ARG SRO_DB_PORT

# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    libonig-dev \
    unixodbc unixodbc-dev \
    xdotool \
    autossh \
    expect

# Install extensions for php
RUN docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr
RUN docker-php-ext-install pdo_odbc pdo_mysql

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

#expose SRO db port
EXPOSE ${SRO_DB_PORT}

CMD ["php-fpm"]