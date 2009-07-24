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

### here don't check if user is logged as this file is included also in pages which don't need admin privileges ###

//Get the XML document loaded into a variable (The xml parser must be previously included)

if (file_exists("$absoluteurl"."categories.xml")) {

	$xml = file_get_contents("$absoluteurl"."categories.xml");
	//Set up the parser object
	$parser = new XMLParser($xml);

	//Parse the XML file with categories data...
	$parser->Parse();

}



?>