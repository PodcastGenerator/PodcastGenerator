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

if (isset($_GET['file']) AND $_GET['file']!=NULL) {

	$file = $_GET['file']; 
	
		$file = str_replace("/", "", $file); // Replace / in the filename.. avoid deleting of file outside media directory - AVOID EXPLOIT with register globals set to ON

	$ext = $_GET['ext'];



	if (file_exists("$absoluteurl$upload_dir$file.$ext")) {
		unlink ("$upload_dir$file.$ext");
		$PG_mainbody .="<p><b>$file.$ext</b> "._("has been deleted")."</p>";

	}

	if (file_exists("$absoluteurl$upload_dir$file.xml")) {

		unlink ("$absoluteurl$upload_dir$file.xml"); // DELETE THE FILE

	}
	
	//Delete associated image
	if (file_exists("$absoluteurl$img_dir$file.jpg")) {
		unlink ("$absoluteurl$img_dir$file.jpg"); 
	} else if (file_exists("$absoluteurl$img_dir$file.png")) {
		unlink ("$absoluteurl$img_dir$file.png"); 
	}



	########## REGENERATE FEED
	//include ("$absoluteurl"."core/admin/feedgenerate.php"); //(re)generate XML feed
	generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
	##########

	$PG_mainbody .= '<p><a href=?p=archive&amp;cat=all>'._("Delete other episodes").'</a></p>';

} else { 
	$PG_mainbody .= _("No file to delete...");
}
?>