#!/bin/bash -x

mkdir -p ${APACHE_DOCUMENT_ROOT}/appdata/images
mkdir -p ${APACHE_DOCUMENT_ROOT}/appdata/media
mkdir -p ${APACHE_DOCUMENT_ROOT}/appdata/themes/default
cp -r ${APACHE_DOCUMENT_ROOT}/../theme-default/* ${APACHE_DOCUMENT_ROOT}/appdata/themes/default
chown -R www-data:www-data ${APACHE_DOCUMENT_ROOT}/appdata

apache2-foreground
