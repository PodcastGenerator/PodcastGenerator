<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

## MEDIA FORMATS SUPPORTED BY PODCAST GENERATOR

## Specify file format supported by Podcast Generator: 

$podcast_filetypes = array(); //filetypes array to handle multiple filetypes 

$podcast_filetypes[0]="mp3";
$podcast_filetypes[1]="mpg";
$podcast_filetypes[2]="mpeg";
$podcast_filetypes[3]="mov";
$podcast_filetypes[4]="wav";
$podcast_filetypes[5]="wma";
$podcast_filetypes[6]="wmv";
$podcast_filetypes[7]="ogg";
$podcast_filetypes[8]="wma";
$podcast_filetypes[9]="3gp"; //video mobile phones
$podcast_filetypes[10]="amr"; //audio mobile phones
$podcast_filetypes[11]="mp4";
$podcast_filetypes[12]="asf";
$podcast_filetypes[13]="avi";
$podcast_filetypes[14]="flv"; //flash video
$podcast_filetypes[15]="jpg";
$podcast_filetypes[16]="jpeg";
$podcast_filetypes[17]="pdf";
$podcast_filetypes[18]="aif";
$podcast_filetypes[19]="aiff";
$podcast_filetypes[20]="m4a";
$podcast_filetypes[21]="m4v";
$podcast_filetypes[22]="epub";

## NOTE: each $podcast_filetypes[k] must have a corresponding $podcast_filemimetypes[k] below, containing its "mime type"

$podcast_filemimetypes = array();

$podcast_filemimetypes[0]="audio/mpeg";
$podcast_filemimetypes[1]="video/mpeg";
$podcast_filemimetypes[2]="video/mpeg";
$podcast_filemimetypes[3]="video/quicktime";
$podcast_filemimetypes[4]="audio/x-wav";
$podcast_filemimetypes[5]="audio/x-ms-wma";
$podcast_filemimetypes[6]="video/x-ms-wmv";
$podcast_filemimetypes[7]="application/ogg";
$podcast_filemimetypes[8]="audio/x-ms-wma";
$podcast_filemimetypes[9]="video/3gpp";
$podcast_filemimetypes[10]="audio/amr";
$podcast_filemimetypes[11]="video/mp4";
$podcast_filemimetypes[12]="video/x-ms-asf";
$podcast_filemimetypes[13]="video/x-msvideo";
$podcast_filemimetypes[14]="video/x-flv";
$podcast_filemimetypes[15]="image/jpeg";
$podcast_filemimetypes[16]="image/jpeg";
$podcast_filemimetypes[17]="application/pdf";
$podcast_filemimetypes[18]="audio/x-aiff";
$podcast_filemimetypes[19]="audio/x-aiff";
$podcast_filemimetypes[20]="audio/x-m4a";
$podcast_filemimetypes[21]="video/x-m4v";
$podcast_filemimetypes[22]="application/epub+zip";
?>