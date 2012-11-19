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

if (!isset($_GET['cat']) OR $_GET['cat'] == NULL ) {
	$PG_mainbody = NULL; //erase variable which contains episodes data

	include ("$absoluteurl"."components/xmlparser/loadparser.php");
	include ("$absoluteurl"."core/admin/readXMLcategories.php");

	if (file_exists("$absoluteurl"."categories.xml") AND isset($parser->document->category)) {

		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;

		foreach($parser->document->category as $singlecategory)
		{
			//echo $singlecategory->id[0]->tagData."<br>";
			//echo $singlecategory->description[0]->tagData;

			$arr[] .= $singlecategory->description[0]->tagData;
			$arrid[] .= $singlecategory->id[0]->tagData;
			$n++;
		}



		$PG_mainbody .= "<h3>"._("Choose a category:")."</h3>";
		$PG_mainbody .= '<ul>';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {
			//$PG_mainbody .= "cat[" . $key . "] = " . $val . "<br>";

			$PG_mainbody .= '<li><a href="?p=archive&amp;cat='.$arrid[$key].'">' . $val . '</a></li>';

		}


	} //if xml categories file doesn't exist

	$PG_mainbody .= '</ul>
		<div class="episode"><a href="?p=archive&amp;cat=all"><b>'._("Show all episodes").'</b></a></div>';
}

#########################
else { // if category is set

	//load XML parser for PHP4 or PHP5
	include("$absoluteurl"."components/xmlparser/loadparser.php");

	$PG_mainbody = NULL; //erase variable which contains episodes data


	###### display category title

	include ("$absoluteurl"."core/admin/readXMLcategories.php");

	if (file_exists("$absoluteurl"."categories.xml") AND isset($parser->document->category)) {

		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;
		$urlforitunes = str_replace("http://", "itpc://", $url);

		foreach($parser->document->category as $singlecategory)
		{
			//echo $singlecategory->id[0]->tagData."<br>";
			//echo $singlecategory->description[0]->tagData;

			$arr[] .= $singlecategory->description[0]->tagData;
			$arrid[] .= $singlecategory->id[0]->tagData;
			$n++;
		}

		foreach ($arr as $key => $val) {
			//$PG_mainbody .= "cat[" . $key . "] = " . $val . "<br>";

			if ($_GET['cat'] == $arrid[$key]) {

				$PG_mainbody .= '<h3>' . $val . '</h3>
					<p><a href="'.$url.'feed.php?cat='.$_GET['cat'].'">'._("Subscribe to this category").' <img src="feed-icon.gif" alt="'._("Subscribe to this category").'" border="0" /></a></p>
					<p><a href="'.$urlforitunes.'feed.php?cat='.$_GET['cat'].'">'._("Subscribe to this category with iTunes").'</a></p><br />';
				$categorypresent = "yes"; 
			} 
		}

		##### end display category title


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



			foreach ($file_array as $key => $value)	{




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
								<img src=\"podcast.gif\" alt=\""._("Download")." $text_title\" title=\""._("Download")." $text_title\" border=\"0\" align=\"left\" /></a> &nbsp;".date ($dateformat, $value)." <i>($file_size "._("MB").")</i>";



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


							//CATEGORY BELONGING CHECK
							if ($_GET['cat'] != NULL AND $_GET['cat'] == "$text_category1" OR $_GET['cat'] == "$text_category2" OR $_GET['cat'] == "$text_category3") {

								$oneispresent = "yes"; //at least one episode is present in a category

								$PG_mainbody .= 
									'<div class="episode">
									<p class="episode_date">'.$episode_date.'</p>
									<h3 class="episode_title"><a href="?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'">'.$text_title.'</a>';


								if ($podcast_filetype=="mpeg" OR $podcast_filetype=="mov" OR $podcast_filetype=="mp4" OR $podcast_filetype=="wmv" OR $podcast_filetype=="3gp" OR $podcast_filetype=="mp4" OR $podcast_filetype=="avi" OR $podcast_filetype=="flv" OR $podcast_filetype=="m4v") { // if it is a video

									$PG_mainbody .= '&nbsp;<img src="video.png" alt="'._("(Video Podcast)").'" />';

								}


								$PG_mainbody .= '</h3>
									<ul class="episode_imgdesc">';


								$PG_mainbody .= 
									'<li>'.$text_shortdesc;	

								$PG_mainbody .= "<br /><br />
									</li>
									</ul>";

								// to implement: page number
								// echo "$recent_count<br />";
								// if ($recent_count == ($episodeperpage - 1)) { echo "STOP<br />";}

								$PG_mainbody .= "</div>";

							} //end check belonging to category


						} 

					}
				}
			}
			if (!isset($oneispresent) AND isset($categorypresent)) {

				$PG_mainbody .= '<p>'._("This category is empty...").'</p>';

			} elseif (!isset($categorypresent)) {
				$PG_mainbody .= '<p>'._("This category doesn't exist").'</p>';
			}

			$PG_mainbody .=	'<p><a href="?p=archive">'._("Back to category list").'</a></p>';

		} else { 
			$PG_mainbody .= '<div class="topseparator"><p>'._("Directory").' <b>'.$upload_dir.'</b> '._("is empty...").'</p></div>';
		}

	}
}

?>