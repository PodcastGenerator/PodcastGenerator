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

			if (strtolower($img_ext[1])=="jpg" OR strtolower($img_ext[1])=="jpeg" OR strtolower($img_ext[1])=="png") { // check image format

			
				$iTunesCoverNameWithoutExtension = $absoluteurl.$img_dir."itunes_image.";
			
				$newNameiTunesCoverUploaded = $iTunesCoverNameWithoutExtension.strtolower($img_ext[1]);

				//Delete previous covers
				if (file_exists($iTunesCoverNameWithoutExtension.'jpg')) unlink($iTunesCoverNameWithoutExtension.'jpg');
				else if (file_exists($iTunesCoverNameWithoutExtension.'png')) unlink($iTunesCoverNameWithoutExtension.'png');
				
				if (move_uploaded_file($_FILES['image']['tmp_name'], $newNameiTunesCoverUploaded))
				{
					$PG_mainbody .= "<p><b>"._("iTunes cover art replaced successfully.")."</b></p>"; // If upload is successful.
					
					
					########## REGENERATE FEED
					$episodesCounter = generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
					##########
					
					
				}
				else { //if upload NOT successful

					$PG_mainbody .= "<p><b>"._("Error: image NOT sent!")."</b></p>";
					//	$temporaneo= $_FILES['image']['tmp_name'];

				}

			} else { // if image extension is NOT valid

				$PG_mainbody .= "<p><b>"._("Image extension not valid. The image extension must end in .jpg or .png")."</b></p>";
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

		if (file_exists($absoluteurl.$img_dir.'itunes_image.jpg')) {
			$podcastCoverArt= $url.$img_dir.'itunes_image.jpg';
		} else if (file_exists($absoluteurl.$img_dir.'itunes_image.png')) {
			$podcastCoverArt= $url.$img_dir.'itunes_image.png';
		}
	
	
		//time() is added to the img URL so the browser doesn't cache it in the admin section
		$PG_mainbody .= '
			<div class="topseparator"><p>
			'._("Current image:").'</p>
			<p>	<img src="'.$podcastCoverArt.'?'.time().'" width="300" height="300" alt="'._("iTunes image").'" />
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