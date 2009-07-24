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

include ('checkconfigexistence.php');

include ("$absoluteurl"."core/functions.php");
include ("$absoluteurl"."core/supported_media.php");

$upload_dir = "media/";

$handle = opendir ("$absoluteurl"."$upload_dir");
while (($filename = readdir ($handle)) !== false)
{
	if ($filename != '..' && $filename != '.' && $filename != 'index.htm' && $filename != '_vti_cnf' && $filename != '.DS_Store')
	{


		$file_array[$filename] = filemtime ("$absoluteurl"."$upload_dir"."$filename");

	}
}

if (!empty($file_array)) { 

	arsort ($file_array); //invert order (most recent above)

	//	echo "Looking for old podcast episodes <br />";

	foreach ($file_array as $key => $value)

	{


		$file_multimediale=explode(".",$key); //divide filename from extension

		$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);


		if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]

			$podcast_filetype=$fileData[0];

			$filedescr = "$absoluteurl$upload_dir$file_multimediale[0].desc"; //description file

			if ($file_multimediale[1]=="$podcast_filetype" AND file_exists("$filedescr")) { // if the extension is the same as specified in config.php

				//$file_size = filesize("$upload_dir$file_multimediale[0].$podcast_filetype");
				//$file_size = $file_size/1048576;
				//$file_size = round($file_size, 2);

				############


				if (file_exists("$filedescr")) { //if description file exists 

					$file_contents=NULL; //reset 

					//open description file
					$fs1 = fopen( $filedescr, "r" ) or die("$L_opendesc_error"); 

					while (!feof($fs1)) { 
						$file_contents .= fgets($fs1, 1024); 
					} 
					fclose($fs1);
				} 
				############

				$fields = explode("|||",$file_contents); //divide title from description
				$category = explode(",,",$fields[4]); //categories in old podcastgen
				$auth = explode(",",$fields[7]); //author name and email

				if (!isset($oldepisodesfound)) {

					$oldepisodesfound = "yes"; //set yes if at least one old episode is found.

				}
				$PG_mainbody .= "<br />Found: $fields[0] (<i>$file_multimediale[0].$podcast_filetype</i>)<br />";


				$title = $fields[0];
				$description = $fields[1];
				$long_description = $fields[2];

				if (isset($fields[3]) AND $fields[3] != NULL) {
					$image_name = $fields[3];	
				} else {
					$image_name = "";	
				}

				if (isset($category[0]) AND $category[0] != NULL) {
					$text_category1 = $category[0];	
				} else {
					$text_category1 = "";	
				}

				if (isset($category[1]) AND $category[1] != NULL) {
					$text_category2 = $category[1];	
				} else {
					$text_category2 = "";	
				}

				if (isset($category[2]) AND $category[2] != NULL) {
					$text_category3 = $category[2];	
				} else {
					$text_category3 = "";	
				}

				$keywords = $fields[5];

				$explicit = $fields[6];

				$auth_name = $auth[0];
				$auth_email = $auth[1];


				$title = depurateContent($title); //title
				$description = depurateContent($description); //short desc
				//$long_description = depurateContent($long_description); //long desc
				$keywords = depurateContent($keywords); //Keywords
				$auth_name = depurateContent($auth_name); //author's name

				############################################
				#########################
				########## CREATING XML FILE ASSOCIATED TO EPISODE


				$file_desc_xml = "$file_multimediale[0].xml"; // extension = XML

				// $PG_mainbody .= "<br>Description filename: $file_desc<br>";

				$xmlfiletocreate = '<?xml version="1.0" encoding="UTF-8"?>
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
					<imgPG>'.$image_name.'</imgPG>
					<categoriesPG>
					<category1PG>';
				if(isset($text_category1) AND $text_category1!= NULL){
					$xmlfiletocreate .=	$text_category1;
				}
				$xmlfiletocreate .='</category1PG>
					<category2PG>';
				if(isset($text_category2) AND $text_category2!= NULL){
					$xmlfiletocreate .=	$text_category2;
				}
				$xmlfiletocreate .='</category2PG>
					<category3PG>';
				if(isset($text_category3) AND $text_category3!= NULL){
					$xmlfiletocreate .=	$text_category3;
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
				$fp = fopen($absoluteurl.$upload_dir.$file_desc_xml,'w'); //open desc file or create it

				fwrite($fp,$xmlfiletocreate);

				fclose($fp);


				########## END CREATION XML FILE
				#########################
				############################################

				$PG_mainbody .= "<font color=\"green\">Converted!</font><br />";

				unlink ("$filedescr"); // DELETE THE OLD .desc FILE




			}

		}
	}
} 

//else { 
	//	echo "No file to upgrade...";
	//}

	?>