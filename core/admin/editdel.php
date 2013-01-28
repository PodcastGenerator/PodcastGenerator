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

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue

	$PG_mainbody .= "<h3>"._("Edit/Delete Podcasts")."</h3>";

	//load XML parser for PHP4 or PHP5
	include("$absoluteurl"."components/xmlparser/loadparser.php");

	// Open podcast directory
	$handle = opendir ($absoluteurl.$upload_dir);
	while (($filename = readdir ($handle)) !== false)
	{

		if ($filename != '..' && $filename != '.' && $filename != 'index.htm' && $filename != '_vti_cnf' && $filename != '.DS_Store')
		{

			$file_array[$filename] = filemtime ($absoluteurl.$upload_dir.$filename);
		}

	}

	if (!empty($file_array)) { //if directory is not empty


		# asort ($file_array);
		arsort ($file_array); //the opposite of asort (inverse order)

		$recent_count = 0; //set recents to zero



		foreach ($file_array as $key => $value)

		{




			$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)

			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


				$podcast_filetype = $fileData[0];


				if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php

					$file_size = filesize("$upload_dir$file_multimediale[0].$podcast_filetype");
					$file_size = $file_size/1048576;
					$file_size = round($file_size, 2);

					############
					$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file

					if (file_exists("$filedescr")) { //if database file exists 


						//$file_contents=NULL; 


						# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");


						#Define episode headline
						$episode_date = "<a name=\"$file_multimediale[0]\"></a>
							<a href=\"".$url."download.php?filename=$file_multimediale[0].$podcast_filetype\">
							</a> &nbsp;".date ($dateformat, $value)."";



						### Here the output code for the episode is created

						# Fields Legend (parsed from XML):
						# $text_title = episode title
						# $text_shortdesc = short description
						# $text_longdesc = long description
						# $text_imgpg = image (url) associated to episode
						# $text_categoriespg = categories
						# $text_keywordspg = keywords
						# $text_explicitpg = explicit podcast (yes or no)
						# $text_authorpg = author

						####### delete quotes and apostrophes
						$text_title2 = str_replace('\'', '', $text_title); //$text_title2 replace apostrophe, otherwise delete doesn't work
						$text_title2 = str_replace('"', '', $text_title2); //$text_title2 replace quotes, otherwise delete doesn't work
						####### 

						$PG_mainbody .= 
							'<div class="episode">
							<p><b>'.$text_title.'</b><span class="admin_hints">'.$episode_date.'</span></p><p>[<a href="?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'">'._("Show").'</a> - <a href="?p=admin&do=edit&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'">'._("Edit").'</a> - <a href="javascript:Effect.toggle(\''.$text_title2.$recent_count.'\',\'appear\');">'._("Delete").'</a>]</p>
							<div id="'.$text_title2.$recent_count.'" style="display:none">

							<b>'._("Do you really want to delete permanently this episode?").'</b>
							<p>'._("Yes").' <input type="radio" name="'._("Delete").' '.$text_title2.'" value="yes" onClick="showNotify(\''._("Deleting...").'\');location.href=\'?p=admin&do=delete&file='.$file_multimediale[0].'&ext='.$podcast_filetype.'';

						if ($text_imgpg!=NULL) {
							$PG_mainbody .= '&img='.$text_imgpg.'';
						}
						$PG_mainbody .= '\';"> &nbsp;&nbsp; '._("No").' <input type="radio" name="'._("No").'" value="no" onClick="javascript:Effect.toggle(\''.$text_title2.$recent_count.'\',\'appear\');"></p>

							</div>
							';

						$recent_count++; //increment number - every episode has a single number and a single DIV id.

						// to implement: page number
						// echo "$recent_count<br />";
						// if ($recent_count == ($episodeperpage - 1)) { echo "STOP<br />";}

						$PG_mainbody .= "</div>";


					} 

				}
			}
		}

	} else { 
		$PG_mainbody .= '<div class="topseparator"><p>'._("Directory").' <b>'.$upload_dir.'</b> '._("is empty...").'</p><p><a href="?p=admin&do=upload">'._("Upload a podcast").'</a></p></div>';
	}

} //end if admin
?>