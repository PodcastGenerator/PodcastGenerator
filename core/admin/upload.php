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

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="upload" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$PG_mainbody .= '<h3>'._("Upload Podcast").'</h3>';

	include("$absoluteurl"."core/admin/sendfile.php");

	$PG_mainbody .= '</div>';

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

		$PG_mainbody .= '<h3>'._("Upload Podcast").'</h3>';

		$PG_mainbody .= '
			<form action="?p=admin&amp;do=upload&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform" onsubmit="return submitForm();">

			<fieldset>
			<legend><b>'._("Main information (required):").'</b></legend>
			<br />
			<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_form_size.'">

			<label for="userfile">'._("File").'*</label><br />
			<input name="userfile" id="userfile" type="file"><br />';

		if ($showmin!=NULL and $showmin!="0") { 
			$PG_mainbody .= '<span class ="admin_hints">'._("Max:")."_upload_allowed.' '.$showmin."._("MB").' '._("- If you need to upload larger files you can use the").' <a href="?p=admin&amp;do=ftpfeature">'._("FTP Feature").'</a></span>';
		}

		$PG_mainbody .= '<br /><br />
			<label for="title">'._("Title").'*</label><br />
			<input name="title" id="title" type="text" size="50" maxlength="255" ><br /><br /><br />

			<label for="description">'._("Short Description").'*</label><br />
			<span class ="admin_hints">'._("(max 255 characters)").'</span><br />

			<input name="description" id="description" type="text" onKeyDown="limitText(this.form.description,this.form.countdown,255);" 
			onKeyUp="limitText(this.form.description,this.form.countdown,255);" size="50" maxlength="255">
			<br /><br />
			<span class ="admin_hints">
			<input name="countdown" type="text" value="255" class ="admin_hints" size="3" readonly> '._("remaining characters.").'</span> 
			<br /><br />';

		### INCLUDE CATEGORIES FORM
		if ($categoriesenabled == "yes") { // if categories are enabled in config.php

			include("$absoluteurl"."core/admin/showcat.php");

		} 
		//else { // if categories are disabled, then use an empty value
			//$PG_mainbody .= '<input type="hidden" name="category[0]" value="">';
			//	}

			### END CATEGORIES FORM

			$PG_mainbody .= _("Fields marked with * are required.").'
				</fieldset>
				';

			$PG_mainbody .= '
				<p><input type="checkbox" value="'._("add extra information to this episode").'" onClick="javascript:Effect.toggle(\'main\',\'appear\');">'._("add extra information to this episode").'
				</p>

				<br />
				<div id="main" style="display:none"> 

				<fieldset>
				<legend><b>'._("Extra information (optional):").'</b></legend>

				<label for="long_description">'._("Long Description").'</label> <span class ="admin_hints">'._("(HTML tags accepted)").'</span><br /><br />

				<textarea id="long_description" name="long_description" cols="50" rows="3"></textarea>
				<br /><br />

				<label for="image">'._("Image").'</label><br />
				<span class ="admin_hints">'._("You can associate an image to this episode; it will appear on the recent podcast page and on the details page.").'</span><br />
				<span class ="admin_hints">'._("Upload a SMALL image (suggested dimensions: 150x150 pixels). Accepted formats: png, gif e jpg.").'</span><br />
				<br />
				<input name="image" type="file">
				<br /><br /><br />

				<label for="keywords">'._("iTunes Keywords:").'</label><br />
				<span class ="admin_hints">'._("Separate keywords by a comma").'</span><br /><br />
				<input name="keywords" type="text" onkeyup="cnt(this,document.uploadform.counttotalwords)" size="50" maxlength="255"></textarea><br />
				<span class ="admin_hints"><input type="text" name="counttotalwords" class ="admin_hints" value="0" size="3" onkeyup="cnt(document.uploadform.keywords,this)" readonly> '._("words.").'</span>
				<br /><br /><br />


				<label for="explicit">'._("Explicit content?").'</label><br />
				<span class ="admin_hints">'._("Select YES if this episode contains explicit language or adult content.").'</span><br /><br />
				'._("Yes").'<input type="radio" name="explicit" value="yes">&nbsp;
			'._("No").'<input type="radio" name="explicit" value="no" checked>
				<br /><br /><br />


				'._("Author").'<br />
				<span class ="admin_hints">'._("You can specify a different author for this episode, otherwise the default author will be the podcast owner.").'</span><br /><br />

				<label for="auth_name">'._("Author's name").'</label><br />
				<input name="auth_name" type="text" id="auth_name" size="50" maxlength="255">
				<br /><br />

				<label for="auth_email">'._("Author's email address").'</label><br />
				<input name="auth_email" type="text" id="auth_email" size="50" maxlength="255">

				</fieldset>
				<br /></div>

				<input type="submit" value="'._("Send").'" onClick="showNotify(\''._("Uploading...").'\');">
				<br /><br /><br /><br />

				</form>

				';

			$PG_mainbody .= '</div>';

		} // end else . if GET variable "c" is not = "ok"

		?>