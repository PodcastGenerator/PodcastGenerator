<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

## MEDIA FORMATS SUPPORTED BY PODCAST GENERATORS
##
## Here you can add new formats :-)


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


## NOTE: each $podcast_filetypes[k] must have a corresponding $filemimetypes[k] below, containing its "mime type"

$filemimetypes = array();

$filemimetypes[0]="audio/mpeg";
$filemimetypes[1]="video/mpeg";
$filemimetypes[2]="video/mpeg";
$filemimetypes[3]="video/quicktime";
$filemimetypes[4]="audio/x-wav";
$filemimetypes[5]="audio/x-ms-wma";
$filemimetypes[6]="video/x-ms-wmv";
$filemimetypes[7]="application/ogg";
$filemimetypes[8]="audio/x-ms-wma";
$filemimetypes[9]="video/3gpp";
$filemimetypes[10]="audio/amr";
$filemimetypes[11]="video/mp4";
$filemimetypes[12]="video/x-ms-asf";
$filemimetypes[13]="video/x-msvideo";
$filemimetypes[14]="video/x-flv";
$filemimetypes[15]="image/jpeg";
$filemimetypes[16]="image/jpeg";
$filemimetypes[17]="application/pdf";
$filemimetypes[18]="audio/x-aiff";
$filemimetypes[19]="audio/x-aiff";
$filemimetypes[20]="audio/x-m4a";
$filemimetypes[21]="video/x-m4v";

?>