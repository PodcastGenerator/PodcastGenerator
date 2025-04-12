# Use Ubuntu 22.04 as base image
FROM ubuntu:22.04

# Prevent interactive prompts during package installation
ENV DEBIAN_FRONTEND=noninteractive

# Set Podcast Generator version
ENV PG_VERSION=3.2.6

# Install required packages
RUN apt-get update && apt-get install -y \
	apache2 \
	libapache2-mod-fcgid \
	php8.1 \
	php8.1-fpm \
	php8.1-common \
	php8.1-zip \
	php8.1-gd \
	php8.1-mbstring \
	php8.1-curl \
	php8.1-xml \
	php-pear \
	php8.1-bcmath \
	php8.1-gettext \
	php8.1-fileinfo \
	unzip \
	wget \
	&& rm -rf /var/lib/apt/lists/*

# Configure PHP
RUN sed -i \
	-e 's/memory_limit = .*/memory_limit = 514M/' \
	-e 's/post_max_size = .*/post_max_size = 513M/' \
	-e 's/upload_max_filesize = .*/upload_max_filesize = 512M/' \
	/etc/php/8.1/fpm/php.ini

# Enable Apache modules and configurations
RUN a2enconf php8.1-fpm && \
	a2enmod proxy proxy_fcgi rewrite

# Download and install Podcast Generator
WORKDIR /var/www
RUN rm -rf html/index*.html && \
	wget https://github.com/PodcastGenerator/PodcastGenerator/releases/download/v${PG_VERSION}/PodcastGenerator-v${PG_VERSION}.zip && \
	unzip PodcastGenerator-v${PG_VERSION}.zip -d PodcastGenerator-v${PG_VERSION} && \
	mv PodcastGenerator-v${PG_VERSION}/PodcastGenerator/* /var/www/html/ && \
	rm -rf PodcastGenerator-v${PG_VERSION}/ PodcastGenerator-v${PG_VERSION}.zip

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html && \
	chmod -R 755 /var/www/html/images && \
	chmod -R 755 /var/www/html/media

# Configure Apache for Podcast Generator
COPY podcastgenerator-apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache and PHP-FPM using JSON format for CMD
CMD ["sh", "-c", "service php8.1-fpm start && apache2ctl -D FOREGROUND"]
