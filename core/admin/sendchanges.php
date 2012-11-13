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

if (isset($_POST['userfile']) AND $_POST['userfile']!=NULL AND isset($_POST['title']) AND $_POST['title']!=NULL AND isset($_POST['description']) AND $_POST['description']!=NULL){ //001

	$file = $_POST['userfile']; //episode file

	$img = $_FILES['image'] ['name']; // image file
	
	if (isset($_POST['existentimage'])) $existentimage = $_POST['existentimage']; else $existentimage = NULL;
	
	$title = $_POST['title'];

	$description = $_POST['description'];

	if (isset($_POST['category']) AND $_POST['category'] != NULL) {
		$category = $_POST['category'];
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
		$PG_mainbody .= "<p>$L_longdesnotpresent</p>";
		$long_description = $description;
	}

	else {
		$PG_mainbody .= "<p>$L_longdescpresent</p>";
		$long_description = str_replace("&nbsp;", " ", $long_description); 
	}

	##############
	### processing iTunes KEYWORDS

	## iTunes supports a maximum of 12 keywords for searching: don't know how many keywords u can add in a feed. Anyway it's better to add a few keyword, so we display a warning if user submits more than 12 keywords

	# $PG_mainbody .= "$keywords<br>"; /debug

	if (isset($ituneskeywords) AND $ituneskeywords != NULL) { 
		$PG_mainbody .= "<p>$L_itunes_keywords $ituneskeywords</p>";

		$singlekeyword=explode(",",$keywords); // divide filename from extension

		if ($singlekeyword[12] != NULL) { //if more than 12 keywords
			$PG_mainbody .= "<p>- $L_itunes_num_keyw</p>";

		}
	}

	##############
	### processing Author

	if (isset($auth_name) AND $auth_name != NULL) { //if a different author is specified

		$PG_mainbody .= "<p>$L_authpresent</p>";

		if (!validate_email($auth_email)) { //if author doesn't have a valid email address, just ignore it and use default author

		$PG_mainbody .= "<p>$L_noauthemail $L_authignored</p>";

		$auth_name = NULL; //ignore author
		$auth_email = NULL; //ignore email

	} 


}
else { //if author's name doesn't exist unset also email field
$auth_email = NULL; //ignore email
}



$PG_mainbody .= "<p><b>$L_processingchanges</b></p>";



#show submitted data (debug purposes)
#$PG_mainbody .= "Dati inseriti:</b><br><br>Titolo: <i>$title</i> <br>Descrizione breve: <i>$description</i> <br>Descrizione lunga: <i>$long_description</i>";
###





#$PG_mainbody .= "<p>$L_origfilename <i>$file</i></p>";



$file_ext=explode(".",$file); // divide filename from extension



// $PG_mainbody .= "<p>$L_file_ext <i>$file_ext[1]</i></p>"; //display file extension

##############
### processing file extension
#$fileData = checkFileType($file_ext[1],$podcast_filetypes,$filemimetypes);

#if (isset($fileData[0])){ //avoids php notice if array [0] doesn't exist
#$podcast_filetype=$fileData[0];
#}else {
	#$podcast_filetype=NULL;	
	#}

	#if ($file_ext[1]==$podcast_filetype) { //003 (if file extension is accepted, go on....


		##############
		##############
		### file name depuration!!!! Important... By default Podcastgen uses a "strict" depuration policy (just characters from a to z and numbers... no accents and other characters).

		#if ($strictfilenamepolicy == "yes") {
			#enable this to have a very strict filename policy

			#$file_ext[0] = renamefilestrict ($file_ext[0]);

			#}

			#else {
				# LESS strict renaming policy

				#$file_ext[0] = renamefile ($file_ext[0]);

				#}
				##############
				############## end filename depuration


				#$filenamechanged = date('Y-m-d')."_".$file_ext[0]; //add date, to order files in mp3 players --- here the date is fixed Y-m-d to keep the order

				#$uploadFile = $upload_dir . $filenamechanged.".".$file_ext[1] ;


				#while (file_exists("$uploadFile")) { //cicle: if file already exists add an incremental suffix
					#$filesuffix++;

					# $PG_mainbody .= "$filesuffix"; //debug

					#$uploadFile = $absoluteurl . $upload_dir . $filenamechanged . $filesuffix.".".$file_ext[1] ;

					#}


					#$PG_mainbody .= "$L_filerenamed <i>$filenamechanged$filesuffix.$file_ext[1]</i><br>";

					#$uploadFile == NULL ;

					#$PG_mainbody .= "<br>Uploaded file:$uploadFile<br>";

					//move file from the temp directory to the upload directory
					#if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile))
					#{



						########################
						######### IMAGE upload section, if image is present

						if ($img!=NULL) {

							$PG_mainbody .= "<p><b>$L_imgpresent</b></p>";

							$img_ext=explode(".",$img); // divide filename from extension

							if ($img_ext[1]=="jpg" OR $img_ext[1]=="jpeg" OR $img_ext[1]=="gif"OR $img_ext[1]=="png" OR $img_ext[1]=="JPG" OR $img_ext[1]=="JPEG" OR $img_ext[1]=="GIF"OR $img_ext[1]=="PNG") { // control accepted image format

								// $PG_mainbody .= "<p>$L_origfilename <i>$img</i></p>";

								// Assign a new name to the image
								$uploadFile2 = $absoluteurl.$img_dir.$file_ext[0].".".$img_ext[1];


								//move file from the temp directory to the upload directory
								if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile2))
								{

									$image_new_name = "$file_ext[0].$img_ext[1]";

									// $PG_mainbody .= "$L_filerenamed <i>$nome_immagine</i>";

									$PG_mainbody .= "<p><font color=\"green\">$L_imgsent</font></p>"; // If upload is successful.



								}

								else { // if IMAGE upload is not successful

									$image_new_name = NULL;

									$PG_mainbody .= "<p><font color=\"red\">$L_imgnotsent $L_ignored</font></p>";
									// $temporaneo= $_FILES['image']['tmp_name'];
									// $PG_mainbody .= "$L_tempfile $temporaneo";
								}



							}

							else { // if the image extension is not valid: IGNORE the image

							//	$image_new_name = NULL;
							$image_new_name = $existentimage;

								$PG_mainbody .= "<p>$L_imgnotvalidext $L_imgextok jpg, gif, png.</p>";


							}

						} else { // if image has not been changed
						
						$image_new_name = $existentimage;	
						}

						########## end IMAGE upload section
						######################




						############################################
						#########################
						########## CREATING XML FILE ASSOCIATED TO EPISODE


						$file_desc = "$file_ext[0].xml"; // extension = XML

						// $PG_mainbody .= "<br>Description filename: $file_desc<br>";

						$xmlfiletocreate = '<?xml version="1.0" encoding="'.$feed_encoding.'"?>
						<PodcastGenerator>
							<episode>
							<titlePG>
							<![CDATA[ '.$title.' ]]>
							</titlePG>
							<shortdescPG>
							<![CDATA[ '.$description.' ]]>
							</shortdescPG>
							<longdescPG>
							<![CDATA[ '.$long_description.' ]]>
							</longdescPG>
							<imgPG>'.$image_new_name.'</imgPG>
							<categoriesPG>
							<category1PG>';
						if(isset($category[0]) AND $category[0]!= NULL){
							$xmlfiletocreate .=	$category[0];
						}
						$xmlfiletocreate .='</category1PG>
							<category2PG>';
						if(isset($category[1]) AND $category[1]!= NULL){
							$xmlfiletocreate .=	$category[1];
						}
						$xmlfiletocreate .='</category2PG>
							<category3PG>';
						if(isset($category[2]) AND $category[2]!= NULL){
							$xmlfiletocreate .=	$category[2];
						}
						$xmlfiletocreate .='</category3PG>
							</categoriesPG>
							<keywordsPG>'.$keywords.'</keywordsPG>
							<explicitPG>'.$explicit.'</explicitPG>
							<authorPG>
							<namePG>'.$auth_name.'</namePG>
							<emailPG>'.$auth_email.'</emailPG>
							</authorPG>
							</episode>
							</PodcastGenerator>';

						/////////////////////
						// WRITE THE XML FILE
						$fp = fopen($absoluteurl.$upload_dir.$file_desc,'w'); //open desc file or create it

						fwrite($fp,$xmlfiletocreate);

						fclose($fp);


						########## END CREATION XML FILE
						#########################
						############################################


						#	$PG_mainbody .= "<p><b><font color=\"green\">$L_filesent</font></b></p>"; // If upload is successful.

						########## REGENERATE FEED
						include ("$absoluteurl"."core/admin/feedgenerate.php"); //(re)generate XML feed
						##########


						$PG_mainbody .= "<p><a href=\"$url\">$L_gohome</a> - <a href=\"?p=admin&do=editdel\">$L_editotherepisodes</a></p>";

						#}
						#else //If upload is not successfull
						#{

							#$PG_mainbody .= "<p><b><font color=\"red\">$L_err_file $L_uploadfailed</font></b></p>";
							#$PG_mainbody .= "<p><b>$L_err_file1</b></p>";
							#$PG_mainbody .= "<p> - $L_err_file3</p>";
							#$PG_mainbody .= "<p> - $L_err_file4</p>";

							#$PG_mainbody .= "<p><b>$L_debuginfo</b> <a href=\"?p=admin&do=serverinfo\">$L_serverconf</a></p>";

							#$PG_mainbody .= "<p>$L_err_file5 <a href=\"http://podcastgen.sourceforge.net/\" target=\"_blank\">$L_podcastgensite</a></p>";

							#$PG_mainbody .= "<p><form>
							#<INPUT TYPE=\"button\" VALUE=\"$L_back\" onClick=\"history.back()\">
							#</form></p>";
							#}


							#} // 003 (if file extension is not accepted)
							#else {
								#$PG_mainbody .= "<p><i>$file_ext[1]</i> $L_notvalidext</p>";
								#$PG_mainbody .= "<form>
								#<INPUT TYPE=\"button\" VALUE=\"$L_back\" onClick=\"history.back()\">
								#</form>";
								#}



							} // 002
							else { //if long description is more than max characters allowed

								$PG_mainbody .= "<b>$L_longdesctoolong</b><p>$L_longdescmaxchar $longdescmax $L_characters - $L_actualenght <font color=red>".strlen($long_description)."</font> $L_characters.</p>
									<form>
									<INPUT TYPE=\"button\" VALUE=\"$L_back\" onClick=\"history.back()\">
									</form>";
							}
							#### end of long desc lenght checking


						} //001 
						else { //if file, description or title not present...
							$PG_mainbody .= '<p>'.$L_nofield.'
								<br />
								<form>
								<INPUT TYPE="button" VALUE='.$L_back.' onClick="history.back()">
								</form>
								</p>
								';
						}


?>