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

## Here we read Podcast Generator RSS news feed to display in the admin section the latest news about the script

// Try to load and parse podcastgen RSS news file
$rssurl = 'http://feeds.podcastgenerator.net/podcastgenerator';

// include lastRSS
include("$absoluteurl"."components/lastRSS/lastRSS.php");

// Create lastRSS object
$rss = new lastRSS;

// Set cache dir and cache time limit (1200 seconds)
// (don't forget to chmod cache dir to 777 to allow writing)
### In podcastgen I set the ROOT directory as RSS cache dir, as it should already haver writing permissions:
$rss->cache_dir = $absoluteurl;
$rss->cache_time = 43200; // 12 hours cache time between each update of podcastgen news

if ($rs = $rss->get($rssurl)) {

	// I could use also ['author']['guid']['link']
	$RSSnews_title = $rs['items']['0']['title'];
	$RSSnews_date = $rs['items']['0']['pubDate'];
	$RSSnews_description = html_entity_decode($rs['items']['0']['description']); // I use html_entity_decode to enable html tags

	//output RSS last item
	$PG_mainbody .= '<p><b>'.$RSSnews_title.'</b><br /><span class ="admin_hints">
		'.$RSSnews_date.'</span><br /><br />
		'.$RSSnews_description.'
		</p>';

}
else {
	$PG_mainbody .= _("Error: It's not possible to get Podcast Generator news feed. News will be automatically disabled.");
	
	//DISABLE news display if the server doesn't allow
	$enablepgnewsinadmin = "no";
	include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php
}

?>