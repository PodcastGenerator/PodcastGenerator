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


include("core/includes.php");


if (isset($_GET['p'])) {

	if ($_GET['p']=="admin") {
		include("$absoluteurl"."core/admin/admin.php");
	}

	elseif ($_GET['p']=="archive") {
		if ($categoriesenabled == "yes") {

			if (isset($_GET['cat']) AND $_GET['cat'] == "all" ) {
				include("$absoluteurl"."core/archive_nocat.php");
			} else {
				include("$absoluteurl"."core/archive_cat.php");
			}
		} else {
			include("$absoluteurl"."core/archive_nocat.php");
		}
	}

	elseif ($_GET['p']=="episode") {
		include("$absoluteurl"."core/episode.php");
	}


	elseif ($_GET['p']=="ftpfeature") {//DA METTERE IN ADMIN
		include("$absoluteurl"."core/ftpfeature.php");
	}

	else {
		include("$absoluteurl"."core/recent_list.php");
	}
}
else { // if no p= specifies, e.g. just index.php with no GET
	include("$absoluteurl"."core/recent_list.php");
}



//If the theme folder contains theme.xml then it's a theme for PG 2.0+ with new features and we use the new template engine templates.php
if (useNewThemeEngine($theme_path)) { //if function is TRUE
include("$absoluteurl"."core/templates.php");
} else { //otherwise use the old theme engine (themes.php) for retrocompatibility
include("$absoluteurl"."core/themes.php");
}




echo $theme_file_contents;




?>