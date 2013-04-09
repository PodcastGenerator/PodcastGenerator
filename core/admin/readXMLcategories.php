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

### here don't check if user is logged as this file is included also in pages which don't need admin privileges ###

//Get the XML document loaded into a variable (The xml parser must be previously included)

if (file_exists("$absoluteurl"."categories.xml")) {

	$xml = file_get_contents("$absoluteurl"."categories.xml");

	// define variables
	$arr = NULL;
	$arrid = NULL;
	$n = 0;

	
	$parser = simplexml_load_file($absoluteurl."categories.xml",'SimpleXMLElement',LIBXML_NOCDATA);

	//var_dump($parser); //Debug

//	$existingCategories = array();
	
			$n = 0;
			foreach($parser->category as $singlecategory) {

			//create array containing category id as seed and description for each id
			$catID = $singlecategory->id[0];
			$catDescription = $singlecategory->description[0];
		//	$existingCategories[$catID] = $catDescription;
			
			$arr[] .= $catDescription;
			$arrid[] .= $catID;
			
			$n++;
			}
	
}



?>