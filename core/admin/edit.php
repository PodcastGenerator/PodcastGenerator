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

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="edit" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$PG_mainbody .= '<h3>'._("Edit podcast").'</h3>';

	include("$absoluteurl"."core/admin/sendchanges.php");

//	$PG_mainbody .= '</div>';

}

else {


	###########

	if (isset($_GET['name']) AND $_GET['name'] != NULL ) {
		$singleEpisode = $_GET['name'];


		////Validate the current episode
				//NB. validateSingleEpisode returns [0] episode is supported (bool), [1] Episode Absolute path, [2] Episode XML DB absolute path,[3] File Extension (Type), [4] File MimeType, [5] File name without extension
	$thisPodcastEpisode = validateSingleEpisode($singleEpisode);

				////If episode is supported and has a related xml db, and if it's not set to a future date OR if it's set for a future date but you are logged in as admin
				if ($thisPodcastEpisode[0]==TRUE) { 

					////Parse XML data related to the episode 
					// NB. Function parseXMLepisodeData returns: [0] episode title, [1] short description, [2] long description, [3] image associated, [4] iTunes keywords, [5] Explicit language,[6] Author's name,[7] Author's email,[8] PG category 1, [9] PG category 2, [10] PG category 3, [11] file_info_size, [12] file_info_duration, [13] file_info_bitrate, [14] file_info_frequency
					$thisPodcastEpisodeData = parseXMLepisodeData($thisPodcastEpisode[2]);	
		

						//// content definition and depuration (solves problem with quotes etc...)
						$text_title = depurateContent($thisPodcastEpisodeData[0]); //title
						$thisPodcastEpisodeData[1] = depurateContent($thisPodcastEpisodeData[1]); //short desc
						$text_shortdesc = depurateContent($thisPodcastEpisodeData[1]); //short desc
						$text_longdesc = depurateCDATAfield($thisPodcastEpisodeData[2]); //long desc
						$text_keywordspg = depurateContent($thisPodcastEpisodeData[4]); //Keywords
						$text_authornamepg = depurateContent($thisPodcastEpisodeData[6]); //author's name
						$text_authoremailpg = $thisPodcastEpisodeData[7];
						$text_explicitpg = $thisPodcastEpisodeData[5];
						$episodedate = filemtime ($thisPodcastEpisode[1]);
						$text_category1 = $thisPodcastEpisodeData[8];
						$text_category2 = $thisPodcastEpisodeData[9];
						$text_category3 = $thisPodcastEpisodeData[10];
						
						
				
						#########






$PG_mainbody .= '<h3 class="sectionTitle">'._("Edit or Delete Episode").'</h3>';

							

		$PG_mainbody .= '
		
		 <div class="span5 importantSection">
		<form action="?p=admin&amp;do=edit&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform" onsubmit="return submitForm();">

							<fieldset>
							<legend><b>'._("Main information (required):").'</b></legend>
							';
$PG_mainbody .= '<input type="hidden" name="userfile" value="'.$_GET['name'].'">';


//$PG_mainbody .= '<label for="userfile">'._("File to edit:").'</label><br />
//<p><b>'.$text_title.'</b> ('.$_GET['name'].')</p>';

		$PG_mainbody .= '
			<label for="title">'._("Title").' *</label>
			<input name="title" id="title" type="text" size="50" maxlength="255" value="'.$text_title.'" /><br /><br />

			<label for="description">'._("Short Description").' *</label>

			<input name="description" id="description" type="text" onKeyDown="limitText(this.form.description,this.form.countdown,255);" 
			onKeyUp="limitText(this.form.description,this.form.countdown,255);" size="50" maxlength="255" value="'.$text_shortdesc.'">
			<br />
			<span>
			<input name="countdown" class="readonlyinput" type="text" value="255" class ="alert" size="3" readonly> '._("characters left").'</span> 
			<br /><br />';

		
		
		### INCLUDE CATEGORIES FORM
						if ($categoriesenabled == "yes") { // if categories are enabled in config.php

							include("$absoluteurl"."core/admin/showcat.php");

						} 
						//else { // if categories are disabled, then use an empty value
							//$PG_mainbody .= '<input type="hidden" name="category[0]" value="">';
							//	}

							### END CATEGORIES FORM

							
							
							
							
							$PG_mainbody .= '
							<br /><br />
							<label>'._("Change the episode date").'</label>
							<span class ="alert">'._("The episodes of your podcast are automatically sorted by date. Changing the date of this episode will change its order in the podcast feed. If you specify a date in future, your episode won't be shown till then.").'</span><br /><br />
							'.CreaFormData("",$episodedate,$dateformat); //dateformat is taken from config.php	
		
		
		

			$PG_mainbody .= '<br /><br />';
			$PG_mainbody .= _("Fields marked with * are required.").'
				
				';

		//	$PG_mainbody .= '<p><input type="checkbox" value="'._("add extra information to this episode").'" onClick="javascript:Effect.toggle(\'main\',\'appear\');">'._("add extra information to this episode").'</p>';
				
				
		
				
				
				$PG_mainbody .= '</fieldset>
			</div>';

			
			$PG_mainbody .= '
			 <div class="span5">

				<fieldset>
				<legend><b>'._("Extras").'</b></legend>

				<label for="long_description">'._("Long Description").'</label>
				<textarea id="long_description" name="long_description" cols="50" rows="3">'.$text_longdesc.'</textarea>
				<br />';
				
		
//UPLOAD IMAGE ASSOCIATED TO EACH EPISODE
//Disabled for the moment (does it really work in the podcast feed?
//better to upload images in the WYSIWYG editor in future

//$PG_mainbody .= '<label for="image">'._("Image").'</label><br /><span class ="alert">'._("You can associate an image to this episode; it will appear on the recent podcast page and on the details page.").'</span><br /><span class ="alert">'._("Upload a SMALL image (suggested dimensions: 150x150 pixels). Accepted formats: png, gif e jpg.").'</span><br /><br /><input name="image" type="file"><br /><br /><br />';

				
				
				
				$PG_mainbody .= '
				<label for="keywords">'._("iTunes Keywords").'</label>
		
				<input name="keywords" type="text" onkeyup="cnt(this,document.uploadform.counttotalwords)" size="50" maxlength="255" placeholder="'._("Keyword1, Keyword2 (max 12)").'" value="'.$text_keywordspg.'">';
				
				//count keywords
				//$PG_mainbody .= '<span><input type="text" name="counttotalwords" value="0"  onkeyup="cnt(document.uploadform.keywords,this)" class="readonlyinput" readonly />'._("keywords").'</span>';
				
				$PG_mainbody .= '
				<br /><br />


				<label for="explicit">'._("Explicit content?").'</label>
				<span class ="alert">'._("Select YES if this episode contains explicit language or adult content.").'</span><br /><br />
				'._("Yes").'&nbsp;<input type="radio" name="explicit" value="yes"';

						if ($text_explicitpg == "yes") {
							$PG_mainbody .= ' checked';	
						}

						$PG_mainbody .=	'>&nbsp;'._("No").'&nbsp;<input type="radio" name="explicit" value="no"';
						
						if ($text_explicitpg != "yes") {
							$PG_mainbody .= ' checked';	
						}
						
						
				$PG_mainbody .= '>
				
<br /><br />


				<label for="auth_name">'._("Author").'</label>
				<span class ="alert">'._("You can specify a different author for this episode, otherwise the default author will be the podcast owner").'</span><br />

				
				<input name="auth_name" type="text" id="auth_name" size="30" maxlength="255" placeholder="'._("Author's name").'" class="input-medium" value="'.$text_authornamepg.'" />

				
				<input name="auth_email" type="text" id="auth_email" size="30" maxlength="255" placeholder="'._("Author's email address").'" class="input-medium" value="'.$text_authoremailpg.'" />

				</fieldset>
				
				
				<br />
				
				<input type="submit" value="'._("Update Episode").'"  class="btn btn-success btn-large" onClick="showNotify(\''._("Updating").'\');">
				
				<input type="button" id="confirmdelete" value="'._("Delete Episode").'" class="btn btn-warning btn-medium" />
				
				
			
				
				<div id="confirmation" style="display:none;">
				<br />
				'._("Do you really want to permanently delete this episode?").' 
				
				<a class="btn btn-danger btn-mini" href="?p=admin&do=delete&file='.$thisPodcastEpisode[5].'&ext='.$thisPodcastEpisode[3].'">'._("YES, I am sure").'</a>
				
				</div>
				
			
				
				<br /><br />

				
				</form>
				</div>

				';

				
						}		

}	// END - If episode is supported						

					} // end else . if GET variable "c" is not = "ok"

?>