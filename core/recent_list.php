<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

/*
require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

$getID3 = new getID3; //initialize getID3 engine

//load XML parser for PHP4 or PHP5
include("$absoluteurl"."components/xmlparser/loadparser.php");
*/

//$PG_mainbody = NULL; //erase variable which contains episodes data
$PG_mainbody .= showPodcastEpisodes(0,NULL); //parameter, is bool yes or not (all episodes?), the second parameter is the category (NULL = all categories)


?>