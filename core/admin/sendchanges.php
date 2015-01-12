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

if (isset($_POST['userfile']) AND $_POST['userfile']!=NULL AND isset($_POST['title']) AND $_POST['title']!=NULL AND isset($_POST['description']) AND $_POST['description']!=NULL){ //001

	$file = $_POST['userfile']; //episode file

	if (isset($_FILES['image'])) $img = $_FILES['image'] ['name']; // image file
	
	if (isset($_POST['existentimage'])) $existentimage = $_POST['existentimage']; else $existentimage = NULL;
	
	$title = $_POST['title'];

	$description = $_POST['description'];

	if (isset($_POST['category']) AND $_POST['category'] != NULL) {
		$category = $_POST['category'];
	} else {
	$category = NULL;
	}

	$long_description = $_POST['long_description'];

	$keywords = $_POST['keywords'];

	$explicit = $_POST['explicit'];

	$auth_name = $_POST['auth_name'];

	$auth_email = $_POST['auth_email'];



	// echo "<br /><br /><br />$file - err $errore - temp: $temporaneo<br /><br /><br />";

	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name

	####
	## here I check lenght of long description: according to the iTunes technical specifications
	## the itunes:summary field can be up to 4000 characters, while the other fields up to 255

	$longdescmax =4000; #set max characters variable. iTunes specifications by Apple say "max 4000 characters" for long description field

	if (strlen($long_description)<$longdescmax) { // 002 (if long description IS NOT too long, go on executing...
		####



		############### cleaning/depurate input
		###############
		//$title = stripslashes($title);
		$title = strip_tags($title);
		$title = htmlspecialchars($title); 

		//$description = stripslashes($description); // no slashes on ' and "
		$description = strip_tags($description);
		#$description = htmlspecialchars($description); 

		$long_description = stripslashes($long_description);
		#$long_description = htmlspecialchars($long_description); // long description accepts HTML

		//$keywords = stripslashes($keywords);
		$keywords = strip_tags($keywords);
		$keywords = htmlspecialchars($keywords);

		//$auth_name = stripslashes($auth_name);
		$auth_name = strip_tags($auth_name);
		$auth_name = htmlspecialchars($auth_name);


		############## end input depuration
		##############

		#### INPUT DEPURATION N.2
		$title = depurateContent($title); //title
		$description = depurateContent($description); //short desc
		//$long_description = depurateContent($long_description); //long desc
		$keywords = depurateContent($keywords); //Keywords
		$auth_name = depurateContent($auth_name); //author's name

		##############
		### processing Long Description

		#$PG_mainbody .= "QUI: $long_description<br>lunghezza:".strlen($long_description)."<br>"; //debug

		if ($long_description == NULL OR $long_description == " ") { //if user didn't input long description the long description is equal to short description
		$PG_mainbody .= "<p>"._("Long description not present; I'll use short description...")."</p>";
		$long_description = $description;
	}

	else {
		$PG_mainbody .= "<p>"._("Long Description present")."</p>";
		$long_description = str_replace("&nbsp;", " ", $long_description); 
	}

	##############
	### processing iTunes KEYWORDS

	## iTunes supports a maximum of 12 keywords for searching: don't know how many keywords u can add in a feed. Anyway it's better to add a few keyword, so we display a warning if user submits more than 12 keywords

	# $PG_mainbody .= "$keywords<br>"; /debug

	if (isset($ituneskeywords) AND $ituneskeywords != NULL) { 
		$PG_mainbody .= "<p>"._("iTunes Keywords:")." $ituneskeywords</p>";

		$singlekeyword=explode(",",$keywords); // divide filename from extension

		if ($singlekeyword[12] != NULL) { //if more than 12 keywords
			$PG_mainbody .= "<p>- "._("You submitted more than 12 keywords for iTunes...")."</p>";

		}
	}

	##############
	### processing Author

	if (isset($auth_name) AND $auth_name != NULL) { //if a different author is specified

		$PG_mainbody .= "<p>"._("Author specified for this episode...")."</p>";

		if (!validate_email($auth_email)) { //if author doesn't have a valid email address, just ignore it and use default author

		$PG_mainbody .= "<p>"._("Author's email address not present or not valid.")." "._("Author will be IGNORED")."</p>";

		$auth_name = NULL; //ignore author
		$auth_email = NULL; //ignore email

	} 


}
else { //if author's name doesn't exist unset also email field
$auth_email = NULL; //ignore email
}


$file_ext = divideFilenameFromExtension($file); //supports more full stops . in the file name. PHP >= 5.2.0 needed


					
############################################
# START CHANGE DATE

//print_r($_POST);

if (isset($_POST['Day']) AND isset($_POST['Month']) AND isset($_POST['Year']) AND isset($_POST['Hour']) AND isset($_POST['Minute'])) { 


$filefullpath = $absoluteurl.$upload_dir.$file;

$oradelfile = filemtime($filefullpath);

$oracambiata = mktime($_POST['Hour'],$_POST['Minute'],0,$_POST['Month'],$_POST['Day'],$_POST['Year']); //seconds are simply 0, no need to handle them


if ($oradelfile != $oracambiata AND checkdate($_POST['Month'],$_POST['Day'],$_POST['Year']) == TRUE) { //is date posted is different from file date and if php function CHECKDATE == TRUE
	
touch($filefullpath,$oracambiata);

$PG_mainbody .= "<p>"._("Date and time of the episode have been modified (this might change the order of your episodes in the podcast feed).")."</p>";

				}

} 					
						
# END CHANGE DATE						
############################################					
						



$PG_mainbody .= "<p><b>"._("Processing changes...")."</b></p>";


				//// RE-CREATING XML FILE ASSOCIATED TO EPISODE

				$thisEpisodeData = array($title,$description,$long_description,$image_new_name,$category,$keywords,$explicit,$auth_name,$auth_email);
		
		$episodeXMLDBAbsPath = $absoluteurl.$upload_dir.$file_ext[0].'.xml'; // extension = XML

		//// Creating xml file associated to episode
		writeEpisodeXMLDB($thisEpisodeData,$absoluteurl,$filefullpath,$episodeXMLDBAbsPath,$file_ext[0],TRUE);
						

						#	$PG_mainbody .= "<p><b><font color=\"green\">"._("File")."sent</font></b></p>"; // If upload is successful.

						########## REGENERATE FEED
						//include ("$absoluteurl"."core/admin/feedgenerate.php"); //(re)generate XML feed
						$episodesCounter = generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
						##########


						$PG_mainbody .= "<p><a href=\"$url\">"._("Go to the homepage")."</a> - <a href=\"?p=archive&amp;cat=all\">"._("Edit other episodes")."</a></p>";

						



							} // 002
							else { //if long description is more than max characters allowed

								$PG_mainbody .= "<b>"._("Long Description")."toolong</b><p>"._("Long Description")."maxchar $longdescmax "._("characters")." - "._("Actual Length")." <font color=red>".strlen($long_description)."</font> "._("characters").".</p>
									<form>
									<INPUT TYPE=\"button\" VALUE=\""._("Back")."\" onClick=\"history.back()\">
									</form>";
							}
							#### end of long desc lenght checking


						} //001 
						else { //if file, description or title not present...
							$PG_mainbody .= '<p>'._("Error: No file, description or title present").'
								<br />
								<form>
								<input type="button" value="&laquo; '._("Back").'" onClick="history.back()" class="btn btn-danger btn-small" />
								</form>
								</p>
								';
						}


?>