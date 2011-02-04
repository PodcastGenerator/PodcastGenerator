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
	if ($amilogged != "true") { exit; }
###

if (isset($_GET['file']) AND $_GET['file']!=NULL) {

	$file = $_GET['file']; 
	
		$file = str_replace("/", "", $file); // Replace / in the filename.. avoid deleting of file outside media directory - AVOID EXPLOIT with register globals set to ON

	$ext = $_GET['ext'];



	if (file_exists("$absoluteurl$upload_dir$file.$ext")) {
		unlink ("$upload_dir$file.$ext");
		$PG_mainbody .="<p><b>$file.$ext</b> $L_deleted</p>";

	}

	if (file_exists("$absoluteurl$upload_dir$file.xml")) {

		unlink ("$absoluteurl$upload_dir$file.xml"); // DELETE THE FILE

	}


	if (isset($_GET['img']) AND $_GET['img']!=NULL) { 

		$img = $_GET['img'];

		if (file_exists("$absoluteurl$img_dir$img")) { // if associated image exists

			unlink ("$absoluteurl$img_dir$img"); // DELETE IMAGE FILE

			$PG_mainbody .="<p>$L_del_img</p>";
		}

	} //end if isset image


	########## REGENERATE FEED
	include ("$absoluteurl"."core/admin/feedgenerate.php"); //(re)generate XML feed
	##########

	$PG_mainbody .= '<p><a href=?p=admin&do=editdel>'.$L_delother.'</a></p>';

} else { 
	$PG_mainbody .="$L_deletenothing";
}
?>