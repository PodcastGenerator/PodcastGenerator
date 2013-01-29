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

### If admin pages, start a PHP session
if (isset($_GET['p'])) if ($_GET['p']=="admin") { session_start(); }

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



include("$absoluteurl"."core/themes.php");




echo $theme_file_contents;




?>