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
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### Check if user is logged ###
	if (!isUserLogged()) { exit; }
###

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue

	if (isset($_GET['do']) AND $_GET['do']=="generate" AND !isset($_GET['c'])) { //show "Continue" Button

	$PG_mainbody .= "<h3>"._("Generate XML feed")."</h3>";
	$PG_mainbody .= "<p><span class=\"admin_hints\">"._("Manually regenerate xml feed")."</span></p>";

//	include ("$absoluteurl"."components/loading_indicator/loading.js");

	$PG_mainbody .= '<br /><br />

		<form method="GET" action="index.php">
		<input type="hidden" name="p" value="'.$_GET['p'].'">
		<input type="hidden" name="do" value="'.$_GET['do'].'">
		<input type="hidden" name="c" value="ok">
		<input type="submit" value="'._("Continue").'" onClick="showNotify(\''._("Regenerating Feed").'\');">
		</form>
		';

	#########
}else{

	if (isset($_GET['do']) AND $_GET['do']=="generate") {	// do not show following text if included in other php files

		$PG_mainbody .= "<h3>"._("Generate XML feed")."</h3>";
		$PG_mainbody .= "<p><span class=\"admin_hints\">"._("Manually regenerate xml feed")."</span></p>";
	}
	
	

	/////////
	//Generate RSS Feed in a file (feed.xml)
	
	$episodesCounter = generatePodcastFeed(TRUE,NULL,TRUE); //Output in file
	////////
	
	

	$PG_mainbody .= "<br /><b>"._("Feed XML generated!")."</b><br />";

	if ($recent_episode_in_feed == "0") {

		$PG_mainbody .= "<br /><i>"._("All the episodes have been indexed in the feed")."</i><br /><span class=\"admin_hints\">"._("You can limit the feed to the last episodes")."</span>";	

	} else {

		if (!isset($episodesCounter)) $episodesCounter = 0; //avoid notice
		$PG_mainbody .= "<br /><i>$episodesCounter "._("episode(s) in the feed")."</i>";	

	}

	//$PG_mainbody .= "<p><a href=\"$url\">"._("Go to the homepage")."</a></p>";


	}
	}

	

	
	?>