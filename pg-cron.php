<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//Checks for previously uploaded episodes with a future date, and regenerate automatically RSS feed if needed
//NB. THIS SCRIPT CAN BE CALLED THROUGH A CRON OR ACT AS PSEUDO CRON CALLED BY INDEX.PHP

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

if (!isset($absoluteurl)) { //if absoluteurl is not known, this file is called directly
include("core/includes.php"); 
$includedInIndexPHP = FALSE; //called through the URL (direct call or cron)
} else {
$includedInIndexPHP = TRUE; //called via index.php (pseudo cron)
}
//echo "URL: ".$absoluteurl; //Debug

$RSSFeedURL = $absoluteurl.$feed_dir."feed.xml";
$lastRSSFeedBuild = NULL;
if (file_exists($RSSFeedURL)) {
//Timestamp last time main RSS feed was generated
$lastRSSFeedBuild = filemtime($RSSFeedURL);
//echo " FEED LAST BUILT: ".$lastRSSFeedBuild; //Debug
}

$feedCacheTime = 5; 
//$feedCacheTime can be set in config.php to overwrite the default value below
if (!isset($feedCacheTime)) $feedCacheTime = 86400; //86400 seconds = 24 hours

if (time() - $lastRSSFeedBuild > $feedCacheTime)  {

	if (!$includedInIndexPHP) echo "PG Feed Cache expired: regenerating the feed."; //Debug
	
} else {
	if (!$includedInIndexPHP) echo "PG Feed in cache."; //Debug
}

?>