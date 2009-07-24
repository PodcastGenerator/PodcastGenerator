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

### Check if user is logged ###
	if ($amilogged != "true") { exit; }
###

// check if user is already logged in
if(isset($amilogged) AND $amilogged =="true") {


	$PG_mainbody .= '<h3>'.$L_image_itunes.'</h3>
		<span class="admin_hints">'.$L_podcastimg.'</span><br /><br />';


	if (isset($_GET['action']) AND $_GET['action']=="change") {


		if (isset($_FILES['image'] ['name']) AND $_FILES['image'] ['name'] != NULL) { 


			$img = $_FILES['image'] ['name'];


			$img_ext=explode(".",$img); // divide filename from extension

			if ($img_ext[1]=="jpg" OR $img_ext[1]=="jpeg" OR $img_ext[1]=="JPG" OR $img_ext[1]=="JPEG") { // check image format

				$uploadFile2 = $absoluteurl.$img_dir."itunes_image.jpg";

				if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile2))
				{
					$PG_mainbody .= "<p><b>$L_imgsent</b></p>"; // If upload is successful.
				}
				else { //if upload NOT successful

					$PG_mainbody .= "<p><b>$L_imgnotsent</b></p>";
					//	$temporaneo= $_FILES['image']['tmp_name'];

				}

			} else { // if image extension is NOT valid

				$PG_mainbody .= "<p><b>$L_imgnotvalidext $L_imgkeep</b></p>";
				$PG_mainbody .= "<p>$L_image_itunes_param</p>";
				$PG_mainbody .= '<br />
					<form>
					<INPUT TYPE="button" VALUE='.$L_back.' onClick="history.back()">
					</form>';
			}

		}

		else {  //if new image NOT selected or empty field

			$PG_mainbody .= "<p>$L_image_itune_select</p>";
			$PG_mainbody .= '<br />
				<form>
				<INPUT TYPE="button" VALUE='.$L_back.' onClick="history.back()">
				</form>';
		}


		###################### end image upload section

	} 



	else { // if image is not posted open the form

		$PG_mainbody .= '
			<div class="topseparator"><p>
			'.$L_imagecurrent.'</p>
			<p>	<img src="'.$url.$img_dir.'itunes_image.jpg" width="300" height="300" alt="'.$L_image_itunes.'" />
			</p><br /></div>

			<div class="topseparator">	
			<form name="'.$L_image_itunes.'" method="POST" enctype="multipart/form-data" action="?p=admin&do=itunesimg&action=change">

			<p><label for="'.$L_image_itunes.'">'.$L_imagenew.'</label></p>
			<input name="image" type="file">
			<p><span class="admin_hints">'.$L_image_itunes_param.'</span></p>
			<p>
			<input type="submit" name="'.$L_send.'" value="'.$L_send.'" onClick="showNotify(\''.$L_uploading.'\');"></p>
			</p>
			</div>
			';


	} 


}

?>