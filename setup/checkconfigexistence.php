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

//check if config.php is already present

if (!isset($absoluteurl)) {
	include ('set_path.php'); //define URL and absolute path on the server
}

if (file_exists("$absoluteurl"."config.php")) {

	header("HTTP/1.1 301 Moved Permanently");

	header("Location: ../"); // open homepage

	exit;
}

?>