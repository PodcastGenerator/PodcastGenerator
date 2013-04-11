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

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="edit" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$PG_mainbody .= '<h3>'._("Edit podcast").'</h3>';

	include("$absoluteurl"."core/admin/sendchanges.php");

//	$PG_mainbody .= '</div>';

}

else {


	###########

	if (isset($_GET['name']) AND $_GET['name'] != NULL ) {
		$file_multimediale = $_GET['name'];


		if (file_exists("$absoluteurl"."$upload_dir$file_multimediale")) {


			//		require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

			//		$getID3 = new getID3; //initialize getID3 engine

			//load XML parser for PHP4 or PHP5
			include("$absoluteurl"."components/xmlparser/loadparser.php");


			$file_multimediale = explode(".",$file_multimediale); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)


			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


				$podcast_filetype = $fileData[0];


				if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php

					$wholeepisodefile = "$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype";


					//				$file_size = filesize("$wholeepisodefile");
					//				$file_size = $file_size/1048576;
					//				$file_size = round($file_size, 2);


					//				$file_time = filemtime("$wholeepisodefile");

					//				$filedate = date ("$dateformat", "$file_time");



					############
					$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file

					if (file_exists("$filedescr")) { //if database file exists 


						//$file_contents=NULL; 


						# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");



						### Here the output code for the episode is created

						# Fields Legend (parsed from XML):
						# $text_title = episode title
						# $text_shortdesc = short description
						# $text_longdesc = long description
						# $text_imgpg = image (url) associated to episode
						# $text_category1, $text_category2, $text_category3 = categories
						# $text_keywordspg = keywords
						# $text_explicitpg = explicit podcast (yes or no)
						# $text_authornamepg = author's name
						# $text_authoremailpg = author's email

						#############################


						#### CONTENT DEPURATION (solves problem with quotes etc...)
						$text_title = depurateContent($text_title); //title
						$text_shortdesc = depurateContent($text_shortdesc); //short desc
						$text_longdesc = depurateContent($text_longdesc); //long desc
						$text_keywordspg = depurateContent($text_keywordspg); //Keywords
						$text_authornamepg = depurateContent($text_authornamepg); 

						}	}	}
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
			<input name="title" id="title" type="text" size="50" maxlength="255" value='.$text_title.' /><br /><br />

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

							
							//Read file date
							$episodedate = filemtime($wholeepisodefile);
							
							$PG_mainbody .= '
							<br /><br />
							<label>Change the episode date</label>
							<span class ="alert">'._("The episodes of your podcast are automatically sorted by date. Changing the date of this episode will change its order in the podcast feed.").'</span><br /><br />
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
				
				<a class="btn btn-danger btn-mini" href="?p=admin&do=delete&file='.$file_multimediale[0].'&ext='.$podcast_filetype.'">'._("YES, I am sure").'</a>
				
				</div>
				
			
				
				<br /><br />

				
				</form>
				</div>

				';

				
						}	}				

					} // end else . if GET variable "c" is not = "ok"

?>