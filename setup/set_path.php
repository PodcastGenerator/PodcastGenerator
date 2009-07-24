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

##### Absolute path
$absoluteurl = realpath("./");

if(strpos(PHP_OS, "WIN") !== false) { //if we are in a windows environment...
$absoluteurl = str_replace("\setup", "", $absoluteurl); // works on Win32 hosts (thanks to Hans Fraiponts for this fix)
$absoluteurl .= "\\";
}
else{ // non windows server
$absoluteurl = str_replace("/setup", "", $absoluteurl); //the file seth_path.php is incorporated in index.php so the sub-folder /setup is not considered - this could not work if someone renames podcast generator root folder as “setup”
$absoluteurl .= "/";
}

//echo $absolutepath;

#####

## Current Url

$currenturl = "http"; 
if(isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']=='on') {
	$currenturl.= "s";
}
$currenturl.= "://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
if($_SERVER['QUERY_STRING']>' ') {
	$currenturl.= "?".$_SERVER['QUERY_STRING'];
}

$currenturl = str_replace("/setup/index.php", "", $currenturl); 
$currenturl = str_replace("/setup/upgrade.php", "", $currenturl);



$currenturl .= "/";

//echo $currenturl;

?>