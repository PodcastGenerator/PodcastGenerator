#!/bin/sh
# This script is for developer use
# Sets folder permissions so that the webserver can write on it
# Made for OpenBSD-current, adjust user groups if neccesary
export USER=$1
chmod 775 PodcastGenerator
chmod 775 PodcastGenerator/media
chmod 775 PodcastGenerator/images
chown $USER:www PodcastGenerator
chown $USER:www PodcastGenerator/media
chown $USER:www PodcastGenerator/images
