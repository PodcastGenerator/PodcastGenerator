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

if(isUserLogged()) { //if logged

	if(strpos(PHP_OS, "WIN") !== false) { //if we are in a windows environment...
			$absoluteurl = addslashes($absoluteurl); // double slashes
	}


	include ("$absoluteurl"."core/admin/VERSION.php"); //read PodcastGenerator version

	
	// Social networks are handled independently (through array) in the config.php file, so more can be added in future and some can also be excluded in the config file.
	$social_networks_active_buttons = NULL;
	foreach ($enablesocialnetworks as $singlesocialnetwork) {$social_networks_active_buttons .= $singlesocialnetwork.","; }
	
	
	//New vars introduced in 2.2 This avoid a notice when upgrade
	if (!isset($feed_iTunes_LINKS_Website)) $feed_iTunes_LINKS_Website = "";
	if (!isset($feed_URL_replace)) $feed_URL_replace = "";
	
	
	if (!isset($first_installation)) $first_installation = time();
	//If not existing, generate 8 character key for this PG installation
	if (!isset($installationKey)) $installationKey = random_str(8);

	// Pg cron
	if (!isset($cronAutoIndex)) $cronAutoIndex = 1;
	if (!isset($cronAutoRegenerateRSS)) $cronAutoRegenerateRSS = 1;
	if (!isset($cronAutoRegenerateRSScacheTime)) $cronAutoRegenerateRSScacheTime = 21600;

	//Regenerate the config.php file
	$configfiletocreate = '<?php

	#################################################################
	# Podcast Generator
	# http://podcastgen.sourceforge.net
	# developed by Alberto Betella
	#
	# Config.php file created automatically - v.'.$podcastgen_version.'


	$podcastgen_version = "'.$podcastgen_version.'"; // Version

	$first_installation = '.$first_installation.';
	
	$installationKey = "'.$installationKey.'";
	
	$scriptlang = "'.$scriptlang.'";

	$url = "'.$url.'"; // Complete URL of the script (Trailing slash REQUIRED)

	$absoluteurl = "'.$absoluteurl.'"; // Absolute path on the server (Trailing slash REQUIRED)

	$theme_path = "'.$theme_path.'";

	$username = "'.$username.'";

	$userpassword = "'.$userpassword.'";

	$max_upload_form_size = "'.$max_upload_form_size.'"; //e.g.: "30000000" (about 30MB)

	$upload_dir = "'.$upload_dir.'"; // "media/" the default folder (Trailing slash required). Set chmod 755

	$img_dir = "'.$img_dir.'";  // (Trailing slash required). Set chmod 755

	$feed_dir = "'.$feed_dir.'"; // Where to create feed.xml (empty value = root directory). Set chmod 755

	$max_recent = '.$max_recent.'; // How many file to show in the home page

	$recent_episode_in_feed = "'.$recent_episode_in_feed.'"; // How many file to show in the XML feed (1,2,5 etc.. or "All")

	$episodeperpage = '.$episodeperpage.';

	$enablestreaming = "'.$enablestreaming.'"; // Enable mp3 streaming? ("yes" or "no")
	
	$enablesocialnetworks = array('.$social_networks_active_buttons.'); // Enable social networks integration? value 1 (true) or 0 (false) for each social network. Array order: Facebook, Twitter, G+

	$dateformat = "'.$dateformat.'"; // d-m-Y OR m-d-Y OR Y-m-d 

	$freebox = "'.$freebox.'"; // enable freely customizable box

	$enablehelphints = "'.$enablehelphints.'";

	$enablepgnewsinadmin = "'.$enablepgnewsinadmin.'";

	$strictfilenamepolicy = "'.$strictfilenamepolicy.'"; // strictly rename files (just characters A to Z and numbers) 

	$categoriesenabled = "'.$categoriesenabled.'";
	
	$cronAutoIndex = '.$cronAutoIndex.'; //Auto Index New Episodes via Cron
	
	$cronAutoRegenerateRSS = '.$cronAutoRegenerateRSS.'; //Auto regenerate RSS via Cron
	
	$cronAutoRegenerateRSScacheTime = '.$cronAutoRegenerateRSScacheTime.'; //Cache (in seconds)


	###################
	# XML Feed elements
	# The followings specifications will be included in your podcast "feed.xml" file.

	$feed_iTunes_LINKS_Website = "'.$feed_iTunes_LINKS_Website.'";

	$feed_URL_replace = "'.$feed_URL_replace.'";

	$podcast_title = "'.$podcast_title.'";

	$podcast_subtitle = "'.$podcast_subtitle.'";

	$podcast_description = "'.$podcast_description.'";

	$author_name = "'.$author_name.'"; 

	$author_email = "'.$author_email.'"; 

	$itunes_category[0] = "'.$itunes_category[0].'"; // iTunes categories (mainCategory:subcategory)
	$itunes_category[1] = "'.$itunes_category[1].'";
	$itunes_category[2] = "'.$itunes_category[2].'";

	$link = $url."?name="; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

	$feed_language = "'.$feed_language.'"; // Language used in the XML feed (can differ from the script language).

	$copyright = "'.$copyright.'"; // Copyright notice

	$feed_encoding = "'.$feed_encoding.'"; // Feed Encoding (e.g. "iso-8859-1", "utf-8"). UTF-8 is strongly suggested

	$explicit_podcast = "'.$explicit_podcast.'"; //does your podcast contain explicit language? ("yes", "no" or "clean")

	// END OF CONFIGURATION

	?>';

	$createcf = fopen("$absoluteurl"."config.php",'w'); //open config file
	fwrite($createcf,$configfiletocreate); //write content into the config file
	fclose($createcf);

	// $PG_mainbody .= '<b>'._("Config.php created!").'</b><br />';

} // end if logged

?>