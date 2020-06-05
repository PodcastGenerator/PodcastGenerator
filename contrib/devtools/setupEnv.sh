#!/bin/sh
# This script is for developer use
# Sets folder permissions so that the webserver can write on it
# Made for Debian 10, adjust user groups if neccesary
chmod 775 PodcastGenerator
chmod 775 PodcastGenerator/media
chmod 775 PodcastGenerator/images
chown $USER:www-data PodcastGenerator
chown $USER:www-data PodcastGenerator/media
chown $USER:www-data PodcastGenerator/images
