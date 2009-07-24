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
if (isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

if (!file_exists("config.php")) { //if config.php doesn't exist stop the script

header("HTTP/1.1 301 Moved Permanently");

header("Location: setup/"); // open setup script

} 

include("config.php"); 


if (!isset($defined)) include("$absoluteurl"."core/functions.php"); //LOAD ONCE

include("$absoluteurl"."core/supported_media.php");

include("$absoluteurl"."core/language.php");


########## START SESSION PHP IF ADMIN REQUIRED 
//if (isset($_GET['p']) AND $_GET['p']=="admin") { 
	//echo "avvio sessione <br><br>";
	//include("$absoluteurl"."core/login.php");
	//}
	#########

	?>