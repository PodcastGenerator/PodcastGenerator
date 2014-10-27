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

// check if user is already logged in
if(isUserLogged()) {

	$PG_mainbody .= '<h3>'._("Change Podcast Generator Configuration").'</h3>
		';

	if (isset($_GET['action']) AND $_GET['action']=="change") { // if action is set

		//streaming
		$streaming = $_POST['streaming'];
		if ($streaming != "") {
			$enablestreaming = $streaming;
		}

		
		// social networks integration
		$socialnetworks = $_POST['socialnetworks'];
		if ($socialnetworks != "") {
			$enablesocialnetworks = array($socialnetworks); //create an array with values 1 or 0 for each social networks
		}
		
		
		
		
		//freebox
		$fbox = $_POST['fbox'];
		if ($fbox != "") {
			$freebox = $fbox;
		}

		//categories
		$cats = $_POST['cats'];
		if ($cats != "") {
			$categoriesenabled = $cats;
		}



		//news display
		$newsinadmin = $_POST['newsinadmin'];
		if ($newsinadmin != "") {
			$enablepgnewsinadmin = $newsinadmin;
		}	


		// strict rename
		$strictfilename = $_POST['strictfilename'];
		if ($strictfilename != "") {
			$strictfilenamepolicy = $strictfilename;
		}			

		// recent in home
		$recent = $_POST['recent'];
		if ($recent != "") {
			$max_recent = $recent;
		}

		// recent in FEED
		$recentinfeed = $_POST['recentinfeed'];
		if ($recentinfeed != "") {
			$recent_episode_in_feed = $recentinfeed;
		}				

		// date format
		$selectdateformat = $_POST['selectdateformat'];
		if ($selectdateformat != "") {
			$dateformat = $selectdateformat;
		}


		// script language
		$scriptlanguage = $_POST['scriptlanguage'];
		if ($scriptlanguage != "") {
			$scriptlang = $scriptlanguage;
		}

		include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

		$PG_mainbody .= '<p>'._("The information has been successfully sent.").'</p>';

		//REGENERATE FEED ...
		//include ("$absoluteurl"."core/admin/feedgenerate.php");
		$episodesCounter = generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
		$PG_mainbody .= '<br /><br />';

	}
	else { // if action not set


		$PG_mainbody .=	'<form name="podcastdetails" method="POST" enctype="multipart/form-data" action="?p=admin&do=config&action=change">';

		##########streaming

		$PG_mainbody .= '<br /><br /><p><label for="streaming"><b>'._("Enable Audio and Video Player?").'</b></label></p>
			<span class="alert">'._("Enable Audio and Video web player for supported files and browsers.").'</span>
			<p>'._("Yes").' <input type="radio" name="streaming" value="yes" ';

		if ($enablestreaming == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '._("No").' <input type="radio" name="streaming" value="no" ';

		if ($enablestreaming == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		####
		
		
		
		########## social networks integration

		$PG_mainbody .= '<br /><br /><p><label for="socialnetworks"><b>'._("Enable Social Networks Integration?").'</b></label></p>
			<span class="alert">'._("Display Facebook, Twitter and Google+ buttons for each episode.").'</span>
			<p>'._("Yes").' <input type="radio" name="socialnetworks" value="1,1,1" ';

		if (in_array(TRUE,$enablesocialnetworks)) { //if at least one is true
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '._("No").' <input type="radio" name="socialnetworks" value="0,0,0" ';

		if (!in_array(TRUE,$enablesocialnetworks)) { //if all false
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		####
		


		########## freebox

		$PG_mainbody .= '<br /><br /><p><label for="fbox"><b>'._("Enable Freebox?").'</b></label></p>
			<span class="alert">'._("Freebox allows you to write freely what you wish, add links or text through a visual editor in the admin section.").'</span>
			<p>'._("Yes").' <input type="radio" name="fbox" value="yes" ';

		if ($freebox == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '._("No").' <input type="radio" name="fbox" value="no" ';

		if ($freebox == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		####

		########## categories

		$PG_mainbody .= '<br /><br /><a name="setcategoriesfeature" id="setcategoriesfeature"></a><p><label for="cats"><b>'._("Enable categories?").'</b></label></p>
			<span class="alert">'._("Enable categories feature to make thematic lists of your podcasts.").'</span>
			<p>'._("Yes").' <input type="radio" name="cats" value="yes" ';

		if ($categoriesenabled == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '._("No").' <input type="radio" name="cats" value="no" ';

		if ($categoriesenabled == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		####

		########## newsinadmin

		$PG_mainbody .= '<br /><br /><p><label for="newsinadmin"><b>'._("Enable news display?").'</b></label></p>
			<span class="alert">'._("Displays Podcast Generator latest news in the main administration page of your podcast.").'</span>
			<p>'._("Yes").' <input type="radio" name="newsinadmin" value="yes" ';

		if ($enablepgnewsinadmin == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '._("No").' <input type="radio" name="newsinadmin" value="no" ';

		if ($enablepgnewsinadmin == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		####

		########## strictfilename

		$PG_mainbody .= '<br /><br /><p><label for="strictfilename"><b>'._("Enable strict episode renaming policy?").'</b></label></p>
			<span class="alert">'._("The uploaded episode files will be automatically renamed using just alphanumeric characters and the current date.").'</span>
			<p>'._("Yes").' <input type="radio" name="strictfilename" value="yes" ';

		if ($strictfilenamepolicy == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '._("No").' <input type="radio" name="strictfilename" value="no" ';

		if ($strictfilenamepolicy == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		####

		########## recent in home

		$PG_mainbody .= '<br /><br /><p><label for="recent"><b>'._("How many recent podcasts in the home page?").'</b></label></p>

			<select name="recent" id="recent">

			<option value=\'2\'';
		if ($max_recent == 2) { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>2</option>

			<option value=\'4\'';
		if ($max_recent == 4) { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>4</option>  

			<option value=\'6\'';
		if ($max_recent == 6) { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>6</option>
		
			<option value=\'8\'';
		if ($max_recent == 8) { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>8</option>
		
			<option value=\'10\'';
		if ($max_recent == 10) { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>10</option>

			<option value=\'20\'';
		if ($max_recent == 20) { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>20</option>
			</select>
			';

		####


		########## recent in feed

		$PG_mainbody .= '<br /><br /><br /><p><label for="recentinfeed"><b>'._("How many episodes indexed in the podcast feeds?").'</b></label></p>

			<select name="recentinfeed" id="recentinfeed">

			<option value=\'5\'';
		if ($recent_episode_in_feed == "5") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>5</option>

			<option value=\'10\'';
		if ($recent_episode_in_feed == "10") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>10</option>

			<option value=\'15\'';
		if ($recent_episode_in_feed == "15") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>15</option>

			<option value=\'20\'';
		if ($recent_episode_in_feed == "20") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>20</option>


			<option value=\'All\'';
		if ($recent_episode_in_feed == "All") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'._("All").'</option>  

			</select>
			';

		####



		########## date format

		$PG_mainbody .= '<br /><br /><br /><p><label for="selectdateformat"><b>'._("Select date format").'</b></label></p>

			<select name="selectdateformat" id="selectdateformat">

			<option value=\'d-m-Y\'';
		if ($dateformat == "d-m-Y") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'._("Day").' / '._("Month").' / '._("Year").'</option>

			<option value=\'m-d-Y\'';
		if ($dateformat == "m-d-Y") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'._("Month").' / '._("Day").' / '._("Year").'</option>

			<option value=\'Y-m-d\'';
		if ($dateformat == "Y-m-d") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'._("Year").' / '._("Month").' / '._("Day").'</option>

			</select>
			';

		####

$listWithLanguages = languagesList($absoluteurl,TRUE);

		## SCRIPT LANGUAGES LIST

		$PG_mainbody .= '<br /><br /><br /><p><label for="scriptlanguage"><b>'._("Podcast Generator Language").'</b></label></p>
			<p><span class="alert">'._("Choose among available languages *").'</span></p>
			';
		$PG_mainbody .= '<select name="scriptlanguage">';


		natcasesort($listWithLanguages); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($listWithLanguages as $key => $val) {

			$PG_mainbody .= '
				<option value="' . $key. '"';

			if ($scriptlang == languageISO639($key) OR $scriptlang == $key) {
				$PG_mainbody .= ' selected';
			}

			$PG_mainbody .= '>' . $val . '</option>';


		}
		$PG_mainbody .= '</select>
		
				<p><a href="http://podcastgen.sourceforge.net/documentation/FAQ-localization" target="_blank"><i class="fa fa-hand-o-right"></i> '._("Looking for another language?").'</a></p>
		';	


		if (isset($installationKey) AND isset($cronAutoIndex) AND $cronAutoIndex == TRUE){
		$PG_mainbody .= '<br /><br /><p><label for="cronURL"><b>'._("Use cron to auto index episodes").'</b></label></p>
			<p><span class="alert">'._("This feature is enabled.")." "._("By calling periodically Podcast Generator via a cron job, you can check automatically the media folder for new episodes and regenerate the RSS feed.").'</span></p>
			<p>'._("Copy and paste the URL below (including your unique key):").'</p>
			<input type="text" name="cronURL" value="'.$url.'pg-cron.php?key='.$installationKey.'" style="width:80%;" readonly>
			<p><a href="http://podcastgen.sourceforge.net/documentation/FAQ-cron-job" target="_blank"><i class="fa fa-hand-o-right"></i> '._("Visit the documentation for more information on how to setup a cron job").'</a></p>';
		}


		$PG_mainbody .= '<br /><br /><input type="submit" name="'._("Send").'" class="btn btn-success btn-small" value="'._("Send").'" onClick="showNotify(\''._("Updating").'\');"></p><br />';
	}

}

?>