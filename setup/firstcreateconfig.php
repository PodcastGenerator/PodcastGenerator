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

if (file_exists("../config.php")) { //if config.php already exists stop the script

	echo "<font color=\"red\">"._("Config.php file already exists!")."</font><br />"._("Please delete this file before proceeding with the installation...");

	exit;

}

$currenturl = str_replace("?step=4", "", $currenturl); //set script URL to be saved in the config.php file

$configfiletocreate = '<?php

#################################################################
# Podcast Generator
# http://podcastgen.sourceforge.net
# developed by Alberto Betella
#
# Config.php file created automatically - v.'.$podcastgen_version.'


$podcastgen_version = "'.$podcastgen_version.'"; // Version

$first_installation = '.time().'; 

$installationKey = "'.random_str(8).'";

$scriptlang = "'.$_POST['setuplanguage'].'";

$url = "'.$currenturl.'"; // Complete URL of the script (Trailing slash REQUIRED)

$absoluteurl = "'.addslashes($absoluteurl).'"; // Absolute path on the server (Trailing slash REQUIRED)

$theme_path = "themes/default_modern/";

$username = "'.$user.'";

$userpassword = "'.md5($pwd).'";

$max_upload_form_size = "104857600"; //e.g.: "30000000" (about 30MB)

$upload_dir = "media/"; // "media/" the default folder (Trailing slash required). Set chmod 755

$img_dir = "images/";  // (Trailing slash required). Set chmod 755

$feed_dir = ""; // Where to create feed.xml (empty value = root directory). Set chmod 755

$max_recent = 4; // How many file to show in the home page

$recent_episode_in_feed = "All"; // How many file to show in the XML feed (1,2,5 etc.. or "All")

$episodeperpage = 10;

$enablestreaming = "yes"; // Enable mp3 streaming? ("yes" or "no")

$enablesocialnetworks = array(1,1,1); // Enable social networks integration? value 1 (true) or 0 (false) for each social network. Array order: Facebook, Twitter, G+

$dateformat = "d-m-Y"; // d-m-Y OR m-d-Y OR Y-m-d 

$freebox = "yes"; // enable freely customizable box

$enablehelphints = "yes";

$enablepgnewsinadmin = "yes";

$strictfilenamepolicy = "yes"; // strictly rename files (just characters A to Z and numbers) 

$categoriesenabled = "yes";

$firsttimehere = "yes";

$cronAutoIndex = 1; //Auto Index New Episodes via Cron

$cronAutoRegenerateRSS = 1; //Auto regenerate RSS via Cron

$cronAutoRegenerateRSScacheTime = 21600; //Cache (in seconds)

###################
# XML Feed elements
# The followings specifications will be included in your podcast "feed.xml" file.

$feed_iTunes_LINKS_Website = "";

$feed_URL_replace = "";

$podcast_title = "'._("Podcast Title").'";

$podcast_subtitle = "'._("Subtitle").'";

$podcast_description = "'._("A little description of your podcast.").'";

$author_name = "Test"; 

$author_email = "test@nospam.com"; 

$itunes_category[0] = "Arts"; // iTunes categories (mainCategory:subcategory)
$itunes_category[1] = "";
$itunes_category[2] = "";

$link = $url."?name="; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

$feed_language = "'.$_POST['setuplanguage'].'"; // Language used in the XML feed (can differ from the script language).

$copyright = "'._("Your copyright notice").'"; // Copyright notice

$feed_encoding = "utf-8"; // Feed Encoding (e.g. "iso-8859-1", "utf-8"). UTF-8 is strongly suggested

$explicit_podcast = "no"; //does your podcast contain explicit language? ("yes", "no" or "clean")

// END OF CONFIGURATION

?>';

$createcf = fopen("$absoluteurl"."config.php",'w'); //open config file
fwrite($createcf,$configfiletocreate); //write content into the config file
fclose($createcf);

// $PG_mainbody .= '<b>'._("Config.php created!").'</b><br />';


?>