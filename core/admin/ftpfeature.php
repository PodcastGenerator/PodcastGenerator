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

	$PG_mainbody .= "<h3>"._("FTP Feature")."</h3>";
	$PG_mainbody .= "<p><span class=\"alert\">"._("Looking for manually uploaded podcast into directory:")." $upload_dir</span></p>";

	if (!isset($_GET['c'])) { //show "Continue" Button

	//include ("$absoluteurl"."components/loading_indicator/loading.js");

	$PG_mainbody .= '<br /><br />

		<form method="GET" action="index.php">
		<input type="hidden" name="p" value="'.$_GET['p'].'">
		<input type="hidden" name="do" value="'.$_GET['do'].'">
		<input type="hidden" name="c" value="ok">
		<input type="submit" value="'._("Continue").'" onClick="showNotify(\''._("Searching...").'\');">
		</form>
		';

	} elseif (isset($_GET['c']) AND isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="ftpfeature") {

	
	$episodesCounter = autoIndexingEpisodes();


					$PG_mainbody .= '<p><b>'._("Scan finished:").'</b> '.$episodesCounter.' '._("new episode(s) added.").'</p>';

					$PG_mainbody .= "<p><a href=\"$url\">"._("Go to the homepage")."</a></p>";

					//REGENERATE FEED ...
					if ($episodesCounter > 0) {
					generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
					}



			} // if continue button is pressed

		} // if is called from admin
		?>