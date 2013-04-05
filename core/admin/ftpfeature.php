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

	$PG_mainbody .= "<h3>"._("FTP Feature")."</h3>";
	$PG_mainbody .= "<p><span class=\"alert\">"._("Looking for manually uploaded podcast into directory:")." $upload_dir</span></p>";

	if (!isset($_GET['c'])) { //show "Continue" Button

	include ("$absoluteurl"."components/loading_indicator/loading.js");

	$PG_mainbody .= '<br /><br />

		<form method="GET" action="index.php">
		<input type="hidden" name="p" value="'.$_GET['p'].'">
		<input type="hidden" name="do" value="'.$_GET['do'].'">
		<input type="hidden" name="c" value="ok">
		<input type="submit" value="'._("Continue").'" onClick="showNotify(\''._("Searching...").'\');">
		</form>
		';

	} elseif (isset($_GET['c']) AND isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="ftpfeature") {

		require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

		$getID3 = new getID3; //initialize getID3 engine

		//$PG_mainbody .= '<div><i>'._("Searching...").'</i></div>';

		// Open podcast directory
		$handle = opendir ($upload_dir);
		while (($filename = readdir ($handle)) !== false)
		{

			if ($filename != '..' && $filename != '.' && $filename != 'index.htm' && $filename != '_vti_cnf')
			{

				$file_array[$filename] = filemtime ($upload_dir.$filename);
			}

		}

		if (!empty($file_array)) { //if directory is not empty


			# asort ($file_array);
			arsort ($file_array); //the opposite of asort (inverse order)

			$files_count = 0; //set file number to 0

			foreach ($file_array as $key => $value)

			{


			//	$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)

				
				$file_multimediale = divideFilenameFromExtension($key);

				$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);


				if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


					$podcast_filetype = $fileData[0];


					if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php


						############
						$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file



						### "FTP FEATURE" check if there are media files in /media directory uploaded manually, if you find, create a proper XML file and add to the podcast


						if (file_exists("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype") AND !file_exists("$filedescr")) { //if there is the multimedia file but not the database file with information

							$PG_mainbody .= '<br /><ul><li><p><b>'._("Media file found:").'</b> '.$file_multimediale[0].'.'.$podcast_filetype.'</li></ul></p>';

							# File details (duration, bitrate, etc...)
							$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype"); //read file tags

							### use ID tag -if present- in the xml data file

							// set title
							if (isset($ThisFileInfo['tags']['id3v2']['title'][0]) AND $ThisFileInfo['tags']['id3v2']['title'][0] != NULL) { //try id3 v2
								$episode_id_title = @$ThisFileInfo['tags']['id3v2']['title'][0];

								$PG_mainbody .= '<p><i>'._("Reading data from ID3 tags:").'</i></p>
									<p><b>'._("Title").'</b> '.$episode_id_title.'</p>';

								} elseif (isset($ThisFileInfo['tags']['id3v1']['title'][0]) AND $ThisFileInfo['tags']['id3v1']['title'][0] != NULL) { //try id3 v1
									$episode_id_title = @$ThisFileInfo['tags']['id3v1']['title'][0];

									$PG_mainbody .= '<p><i>'._("Reading data from ID3 tags:").'</i></p>
										<p><b>'._("Title").'</b> '.$episode_id_title.'</p>';

								} else { //if it cannot read both id3 v1 and v2 use the filename
									$episode_id_title = $file_multimediale[0];
								}


								// set artist (short description)
								if (isset($ThisFileInfo['tags']['id3v2']['artist'][0]) AND $ThisFileInfo['tags']['id3v2']['artist'][0] != NULL) { //try id3 v2
									$episode_id_description = @$ThisFileInfo['tags']['id3v2']['artist'][0];

									$PG_mainbody .= '<p><b>'._("Description").'</b> '.$episode_id_description.'</p>';

									} elseif (isset($ThisFileInfo['tags']['id3v1']['artist'][0]) AND $ThisFileInfo['tags']['id3v1']['artist'][0] != NULL) { //try id3 v1
										$episode_id_description = @$ThisFileInfo['tags']['id3v1']['artist'][0];

										$PG_mainbody .= '<p><b>'._("Description").'</b> '.$episode_id_description.'</p>';

									} else { //if it cannot read both id3 v1 and v2 use the filename
										$episode_id_description = $file_multimediale[0];
									}


									$xmlfiletocreate = '<?xml version="1.0" encoding="'.$feed_encoding.'"?>
									<PodcastGenerator>
										<episode>
										<titlePG>'.$episode_id_title.'</titlePG>
										<shortdescPG>'.$episode_id_description.'</shortdescPG>
										<longdescPG>
										<![CDATA[ '.$episode_id_description.' ]]>
										</longdescPG>
										<imgPG></imgPG>
										<categoriesPG>
										<category1PG></category1PG>
										<category2PG></category2PG>
										<category3PG></category3PG>
										</categoriesPG>
										<keywordsPG></keywordsPG>
										<explicitPG></explicitPG>
										<authorPG>
										<namePG></namePG>
										<emailPG></emailPG>
										</authorPG>
										</episode>
										</PodcastGenerator>';

									// echo "<br>titolo: $episode_id_title - desc: $episode_id_description<br><br>";

									# file name depuration!

									$filenamedepured = $file_multimediale[0];

									#enable this to have a very strict filename policy
									$filenamedepured = preg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($filenamedepured)))); //very strict... not correct with every language...

									$filenamedepured = strtolower($filenamedepured);  // lower-case.
									$filenamedepured = strip_tags($filenamedepured);  // remove HTML tags.
									$filenamedepured = preg_replace('!\s+!','_',$filenamedepured); // change space chars to underscores.
									$filenamedepured = stripslashes($filenamedepured); //remove slashes in the file name
									$filenamedepured = str_replace("'", "", $filenamedepured);
									$filenamedepured = str_replace("&", "_and_", $filenamedepured);

									$filenamechanged = date('Y-m-d')."_".$filenamedepured; //add date, to order files in mp3 players

									$renamedfile = $filenamechanged.".".$podcast_filetype;

									//echo "<br />renamed file: $renamedfile";

									$filesuffix = NULL;

									while (file_exists("$absoluteurl"."$upload_dir$renamedfile")) { //cicle: if file already exists add an incremental suffix
										$filesuffix++;

										# echo "$filesuffix"; //debug

										$renamedfile = $filenamechanged . $filesuffix.".".$podcast_filetype;
									}

									#new name to the episode file
									copy("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype", "$absoluteurl"."$upload_dir$renamedfile"); //copy the file (to rename it) 

									#delete old episode file (original name)
									if (file_exists("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype")) { 
										unlink ("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype"); //delete original file, if exists
									}

									$newxmlfilename = "$absoluteurl"."$upload_dir"."$filenamechanged"."$filesuffix".".xml";

									### create corresponding XML
									$fp = fopen($newxmlfilename,'a'); //create XML file
									fwrite($fp,$xmlfiletocreate);
									fclose($fp);

									$files_count++; //add number to file count



								}



							}



						}

					}

					$PG_mainbody .= '<br /><div><b>'._("Scan finished:").'</b> '.$files_count.' '._("new episode(s) added.");

					$PG_mainbody .= "<p><a href=\"$url\">"._("Go to the homepage")."</a></p>";

					//REGENERATE FEED ...
					if ($files_count != "0") {include ("$absoluteurl"."core/admin/feedgenerate.php");}


					$PG_mainbody .= '</div>';

				} 

			} // if continue button is pressed

		} // if is called from admin
		?>