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

include("pg-cron.php"); //Act as a pseudo cron when someone visits the page

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
			
				
					$PG_mainbody .= showPodcastEpisodes(1,avoidXSS(($_GET['cat']))); //parameter, is bool yes or not (all episodes?), the second parameter is the category (NULL = all categories)
			
					
			} 
			//END CATEGORIES
			
			else {

	//SHOW CATEGORIES LIST
	$existingCategories = readPodcastCategories ($absoluteurl);
		//var_dump($existingCategories); //Debug
	
	$PG_mainbody .= '<h3>'._("Select a category:").'</h3>';
	$PG_mainbody .= '<ul>';
	ksort($existingCategories);	//sort array by key alphabetically
	for ($i = 0; $i <  count($existingCategories); $i++) {
    $key=key($existingCategories);
    $val=$existingCategories[$key];
		if ($val<> ' ') {
		$PG_mainbody .= '<li><a href="?p=archive&amp;cat='.$key.'">'.$val.'</a></li>';
		}
     next($existingCategories);
    }	
		$PG_mainbody .= '</ul>';
		
		
		//If old themes then show also the option "All Episodes"
		if (!useNewThemeEngine($theme_path)) {
		$PG_mainbody .= '<a href="?p=archive&amp;cat=all">'._("All Episodes").'</a>';
		}
			
			
			}
		} else {
			//include("$absoluteurl"."core/archive_nocat.php");
			$PG_mainbody .= showPodcastEpisodes(1,NULL); 
		}
	}

	//elseif ($_GET['p']=="episode" AND isset($_GET['name'])) {
	
	

	elseif ($_GET['p']=="ftpfeature") {//To place in admin
		include("$absoluteurl"."core/ftpfeature.php");
	}

	// Home page
	else {
	//show recent episodes (don't show all episodes) - no categories distinction
		$PG_mainbody .= showPodcastEpisodes(0,0); //parameter, is bool yes or not (all episodes?), the second parameter is the category 
		
	
	$PG_mainbody .= '<div style="clear:both;"><p><a href="'.$url.'?p=archive&cat=all"><i class="fa fa-archive"></i> '._("Go to episodes archive").'</a></p></div>';
		
	}
}

//if a single episode page is specified (important for SEO etc... social network, search 
//engines etc.. don't like more than one get vars e.g. myurl.com?p=episode&name=name
//better to use just one GET for the single episode page
elseif (isset($_GET['name'])) {

		//include("$absoluteurl"."core/episode.php");
		
		$PG_mainbody .= showSingleEpisode(avoidXSS($_GET['name']),NULL); 
		
	}

// Home page (with no ?p= in GET)
else { // if no p= specifies, e.g. just index.php with no GET
//show recent episodes (don't show all episodes) - no categories distinction
		$PG_mainbody .= showPodcastEpisodes(0,0); //parameter, is bool yes or not (all episodes?), the second parameter is the category 
		
		$PG_mainbody .= '<div style="clear:both;"><p><a href="'.$url.'?p=archive&cat=all"><i class="fa fa-archive"></i> '._("Go to episodes archive").'</a></p></div>';
}



//If the theme folder contains theme.xml then it's a theme for PG 2.0+ with new features and we use the new template engine templates.php
if (useNewThemeEngine($theme_path)) { //if function is TRUE
include("$absoluteurl"."core/templates.php");
} else { //otherwise use the old theme engine (themes.php) for retrocompatibility
include("$absoluteurl"."core/themes.php");
}




echo $theme_file_contents;




?>