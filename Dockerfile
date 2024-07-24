FROM php:8.3-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip\
    libzip-dev
    # build-essential \
    # libpng-dev \
    # libjpeg62-turbo-dev \
    # libfreetype6-dev \
    # locales \
    # zip \
    # jpegoptim optipng pngquant gifsicle \
    # vim \
    # unzip \
    # git \
    # # oniguruma-dev \
    # curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
# RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-configure gd
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 g1
RUN useradd -u 1001 -ms /bin/bash -g g1 u1

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=g1:u1 . /var/www

# Change current user to www
USER u1

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]