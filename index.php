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

### Start a PHP session
session_start();

$PG_mainbody = NULL; //erase variable which contains episodes data

include("core/includes.php");


if (isset($_GET['p'])) {

	if ($_GET['p']=="admin") {
		include("$absoluteurl"."core/admin/admin.php");
	}

	elseif ($_GET['p']=="archive") {
		if ($categoriesenabled == "yes") {

			//CATEGORIES
			if (isset($_GET['cat']) AND $_GET['cat'] == "all") {

					//show all episodes - no categories distinction
					$PG_mainbody .= showPodcastEpisodes(1,NULL); 
			} 
			elseif (isset($_GET['cat']) AND $_GET['cat'] != NULL) {

					$PG_mainbody .= showPodcastEpisodes(1,$_GET['cat']); //parameter, is bool yes or not (all episodes?), the second parameter is the category (NULL = all categories)
			} 
			//END CATEGORIES
			
			else {
				include("$absoluteurl"."core/archive_cat.php");
			}
		} else {
			//include("$absoluteurl"."core/archive_nocat.php");
			$PG_mainbody .= showPodcastEpisodes(1,NULL); 
		}
	}

	elseif ($_GET['p']=="episode") {
		include("$absoluteurl"."core/episode.php");
	}


	elseif ($_GET['p']=="ftpfeature") {//DA METTERE IN ADMIN
		include("$absoluteurl"."core/ftpfeature.php");
	}

	else {
	//show recent episodes (don't show all episodes) - no categories distinction
		$PG_mainbody .= showPodcastEpisodes(0,0); //parameter, is bool yes or not (all episodes?), the second parameter is the category 
	}
}
else { // if no p= specifies, e.g. just index.php with no GET
//show recent episodes (don't show all episodes) - no categories distinction
		$PG_mainbody .= showPodcastEpisodes(0,0); //parameter, is bool yes or not (all episodes?), the second parameter is the category 
}



//If the theme folder contains theme.xml then it's a theme for PG 2.0+ with new features and we use the new template engine templates.php
if (useNewThemeEngine($theme_path)) { //if function is TRUE
include("$absoluteurl"."core/templates.php");
} else { //otherwise use the old theme engine (themes.php) for retrocompatibility
include("$absoluteurl"."core/themes.php");
}




echo $theme_file_contents;




?>