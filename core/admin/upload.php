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

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="upload" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$PG_mainbody .= '<h3>'._("Upload Podcast").'</h3>';

	include("$absoluteurl"."core/admin/sendfile.php");

//	$PG_mainbody .= '</div>';

}

else {


	########### Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...

		# convert max upload size set in config.php in megabytes
		$max_upload_form_size_MB = $max_upload_form_size/1048576;
		$max_upload_form_size_MB = round($max_upload_form_size_MB, 2);

		$showmin = min($max_upload_form_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
		// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function
		#############################


		#########

		$PG_mainbody .= '<h3 class="sectionTitle">'._("Upload New Episode").'</h3>';

		$PG_mainbody .= '
		
		 <div class="span5 importantSection">
			<form action="?p=admin&amp;do=upload&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform" onsubmit="return submitForm();">

			<fieldset>
			<legend><b>'._("Main information").'</b></legend>
		
			
			<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_form_size.'">

			<label for="userfile">'._("File").' *</label>
			<input name="userfile" id="userfile" type="file"><br />';

		if ($showmin!=NULL and $showmin!="0") { 
			$PG_mainbody .= '<span class = "alert">'._("Your server configuration allows you to upload files up to").' '.$showmin._("MB").' '._("- If you need to upload larger files you can use the").' <a href="?p=admin&amp;do=ftpfeature">'._("FTP Feature").'</a></span>';
		}

		$PG_mainbody .= '<br /><br />
			<label for="title">'._("Title").' *</label>
			<input name="title" id="title" type="text" size="50" maxlength="255" ><br /><br />

			<label for="description">'._("Short Description").' *</label>

			<input name="description" id="description" type="text" onKeyDown="limitText(this.form.description,this.form.countdown,255);" 
			onKeyUp="limitText(this.form.description,this.form.countdown,255);" size="50" maxlength="255">
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
			
			
		//	$PG_mainbody .=  '<a href="javascript:;" onclick="$(\'#dateForm\').fadeToggle();"> '._("Publication Date").'</a><br />';
		//	$PG_mainbody .= '<div id="dateForm" style="display:none;">HERE THE DATE FORM</div>';
		
			$PG_mainbody .= '
			<br /><br />
			<label>'._("Publication Date").'</label>
			<span class ="alert">'._("The form below reports the current time and date of the server. If you specify a date in future, your episode won't be shown till then.").'</span><br /><br />
			'.CreaFormData("",time(),$dateformat); //dateformat is taken from config.php	
		
			
			
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
				<textarea id="long_description" name="long_description" cols="50" rows="3"></textarea>
				<br />';
				
		
//UPLOAD IMAGE ASSOCIATED TO EACH EPISODE
//Disabled for the moment (does it really work in the podcast feed?
//better to upload images in the WYSIWYG editor in future

//$PG_mainbody .= '<label for="image">'._("Image").'</label><br /><span class ="alert">'._("You can associate an image to this episode; it will appear on the recent podcast page and on the details page.").'</span><br /><span class ="alert">'._("Upload a SMALL image (suggested dimensions: 150x150 pixels). Accepted formats: png, gif e jpg.").'</span><br /><br /><input name="image" type="file"><br /><br /><br />';

				
				
				
				$PG_mainbody .= '
				<label for="keywords">'._("iTunes Keywords").'</label>
		
				<input name="keywords" type="text" onkeyup="cnt(this,document.uploadform.counttotalwords)" size="50" maxlength="255" placeholder="'._("Keyword1, Keyword2 (max 12)").'">';
				
				//count keywords
				//$PG_mainbody .= '<span><input type="text" name="counttotalwords" value="0"  onkeyup="cnt(document.uploadform.keywords,this)" class="readonlyinput" readonly />'._("keywords").'</span>';
				
				$PG_mainbody .= '
				<br /><br />


				<label for="explicit">'._("Explicit content").'</label>
				<span class ="alert">'._("Select YES if this episode contains explicit language or adult content").'</span><br /><br />
				'._("Yes").'&nbsp;<input type="radio" name="explicit" value="yes">&nbsp;
			'._("No").'&nbsp;<input type="radio" name="explicit" value="no" checked>
				<br /><br />


				<label for="auth_name">'._("Author").'</label>
				<span class ="alert">'._("You can specify a different author for this episode, otherwise the default author will be the podcast owner").'</span><br />

				
				<input name="auth_name" type="text" id="auth_name" size="30" maxlength="255" placeholder="'._("Author's name").'" class="input-medium">

				
				<input name="auth_email" type="text" id="auth_email" size="30" maxlength="255" placeholder="'._("Author's email address").'" class="input-medium">

				</fieldset>
				
				
				<br />
				
				<input type="submit" value="'._("Upload Episode").'"  class="btn btn-success btn-large" onClick="showNotify(\''._("Uploading...").'\');">
				
				<br /><br />
				
				</form>
				</div>

				
			

				';

		

		} // end else . if GET variable "c" is not = "ok"

		?>