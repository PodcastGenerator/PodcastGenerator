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

### Check if user is logged ###
	if (!isUserLogged()) { exit; }
###

//include ("$absoluteurl"."components/xmlparser/loadparser.php");
include ("$absoluteurl"."core/admin/readXMLcategories.php");

$PG_mainbody .= '<h3>'._("Delete a category").'</h3>';

// define variables
$arrdesc = NULL;
$arrid = NULL;
$existsinthefeed = NULL;

$n = 0; // counter

$rem = $_GET['cat']; // the variable passed is the category ID

//$PG_mainbody .= "<p>Category: $rem</p>"; //debug

## add depuration here.......


// create unique and depurated id from the description (use the function here) - the variable should already be in this format, but we perform this function anyway

$id = preg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($rem))));


//parse
if (isset($parser->category)) {
	foreach($parser->category as $singlecategory)
	{
		// echo $singlecategory->id[0]->tagData."<br>";
		// echo $singlecategory->description[0]->tagData;
		// echo "<br><br>";

		if ($id != ($singlecategory->id[0])) { // if the id of the new category is different from the ids already present in the XML file

			// put into the array
			//(yeah yeah I know I'm using arrays instead of XML commands... but I thought this solution, and it works...). If you have a more elegant solution (e.g. PHP native XML commands), please re-code this file and send your work to me under GPL license: beta@yellowjug.com (PS. your solution should work perfectly either with PHP 4 and 5, otherwise I won't be able to include it in the new releases of podcast generator)

				$arrdesc[] .= htmlspecialchars($singlecategory->description[0]); // Encode special characters
			$arrid[] .= $singlecategory->id[0];

		}
		else { // if ID already present in XML

			$existsinthefeed = "yes"; // assign duplicated label
			$duplicatedarrid = $singlecategory->id[0]; // set the the already present id name into a variable
		}

		$n++; //increment count

	}
}


if ($existsinthefeed == "yes") { // 001 if the category already exists in the XML file proceed to delete (I use an "Ignore process")

$arrdesc[] .= $rem; //Description

$PG_mainbody .= '<p><b>'._("Category deleted").'</b></p><p><a href="?p=admin&do=categories">'._("Back to category management").'</a>';


$arrid[] .= $id; // create Id

//echo "<br>tot elementi $n<BR>";


$xmlfiletocreate = '<?xml version="1.0" encoding="'.$feed_encoding.'"?>
<PodcastGenerator>';

foreach ($arrdesc as $key => $val) {
	// echo "cat[" . $key . "] = " . $val . "<br>";
	//echo $key."<br>";


	// explanation of the following if:
	// if the category in this foreach cicle is not the one user wants to delete, then include it in the XML file... (if it is THE ONE, it doesn't include.. It Ignores it, the result will be the exclusion from the new XML file generated)

	if ($duplicatedarrid != $arrid[$key]) { ////

		$xmlfiletocreate .= '
			<category>
			<id>'.$arrid[$key].'</id>
			<description>'.$val.'</description>
			</category>';
	} ////

} //end foreach cicle

$xmlfiletocreate .= '
	</PodcastGenerator>';

/////////////////////
// WRITE THE XML FILE
$fp = fopen("categories.xml",'w+'); //open desc file or create it

fwrite($fp,$xmlfiletocreate);

fclose($fp);

} // 001 end 

else { //if category doesn't exist in the XML

$PG_mainbody .= _("The category doesn't exist...").'<br /><br />
	<p><a href="?p=admin&do=categories">'._("Back to category management").'</a>';

}



?>