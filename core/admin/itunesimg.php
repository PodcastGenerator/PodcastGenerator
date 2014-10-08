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

// check if user is already logged in
if(isUserLogged()) {


	$PG_mainbody .= '<h3>'._("iTunes cover art").'</h3>
		<span class="admin_hints">'._("Podcast cover that will be displayed in the iTunes Store").'</span><br /><br />';


	if (isset($_GET['action']) AND $_GET['action']=="change") {


		if (isset($_FILES['image'] ['name']) AND $_FILES['image'] ['name'] != NULL) { 


			$img = $_FILES['image'] ['name'];


			$img_ext=explode(".",$img); // divide filename from extension

			if ($img_ext[1]=="jpg" OR $img_ext[1]=="jpeg" OR $img_ext[1]=="JPG" OR $img_ext[1]=="JPEG") { // check image format

				$uploadFile2 = $absoluteurl.$img_dir."itunes_image.jpg";

				if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile2))
				{
					$PG_mainbody .= "<p><b>"._("iTunes cover art replaced successfully.")."</b></p>"; // If upload is successful.
				}
				else { //if upload NOT successful

					$PG_mainbody .= "<p><b>"._("Error: image NOT sent!")."</b></p>";
					//	$temporaneo= $_FILES['image']['tmp_name'];

				}

			} else { // if image extension is NOT valid

				$PG_mainbody .= "<p><b>"._("Image extension not valid. The image extension must end in .jpg, .jpeg or .png")."</b></p>";
			//	$PG_mainbody .= "<p>"._("You can replace the current image with a new one. To be eligible for featuring on iTunes Store, a podcast must have 1400 x 1400 pixel cover art in JPG or PNG.")."</p>";
				$PG_mainbody .= '<br />
					<form>
					<input type="button" value="&laquo; '._("Back").'" onClick="history.back()" class="btn btn-danger btn-small" />
					</form>';
			}

		}

		else {  //if new image NOT selected or empty field

			$PG_mainbody .= "<p>"._("No file selected. Please go back and select an image.")."</p>";
			$PG_mainbody .= '<br />
				<form>
				<input type="button" value="&laquo; '._("Back").'" onClick="history.back()" class="btn btn-danger btn-small" />
				</form>';
		}


		###################### end image upload section

	} 



	else { // if image is not posted open the form

		$PG_mainbody .= '
			<div class="topseparator"><p>
			'._("Current image:").'</p>
			<p>	<img src="'.$url.$img_dir.'itunes_image.jpg" width="300" height="300" alt="'._("iTunes image").'" />
			</p><br /></div>

			<div class="topseparator">	
			<form name="'._("iTunes cover art").'" method="POST" enctype="multipart/form-data" action="?p=admin&do=itunesimg&action=change">

			<p><label for="'._("iTunes image").'">'._("New image:").'</label></p>
			<input name="image" type="file">
			<p><span class="admin_hints">'._("You can replace the current image with a new one. To be eligible for featuring on iTunes Store, a podcast must have 1400 x 1400 pixel cover art in JPG or PNG.").'</span></p>
			<p>
			<input type="submit" name="'._("Send").'" class="btn btn-success btn-small" value="'._("Send").'" class="btn btn-success btn-small" onClick="showNotify(\''._("Uploading...").'\');"></p>
			</p>
			</div>
			';


	} 


}

?>