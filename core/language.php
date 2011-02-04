<?php 
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

## Below I include English translation by default (as I write new versions of Podcast Generator in English, I suppose this language and the associated variables will be always up to date)

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['scriptlang'])) { exit; } 
########### End
// I depurate scriptlang above


if (file_exists("language/en.php") AND $scriptlang != "en") {
	
	
	include ("language/en.php");

}	


##Now, I include the language file specified in $scriptlang var in config.php.

if (!file_exists("language/$scriptlang.php")) { //if language file doesn't exist

echo "<p class=\"error\">The language you specified in the config file is not available (default = en)</p><br />";
echo "Please correct <i>scriptlang</i> var in config.php";
exit;

}

else { // if language file exist

	include ("language/$scriptlang.php"); //include it and use the variables in the whole script

}

?>