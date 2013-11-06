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

if (isset($_GET['cat']) AND $_GET['cat'] != NULL) {



	include("config.php"); 
	include("$absoluteurl"."core/functions.php");

	include("$absoluteurl"."core/supported_media.php");

	include("$absoluteurl"."core/language.php");


	$cat_image = $img_dir.$_GET['cat'].".jpg";

	### DEFINE FEED FILENAME
	$feedfilename = $absoluteurl.$feed_dir."feed.xml";



	###### display category title
//	include ("$absoluteurl"."components/xmlparser/loadparser.php");
//	include ("$absoluteurl"."core/admin/readXMLcategories.php");

# READ the XML database file and parse the fields
	//				include("$absoluteurl"."core/readXMLdb.php");

	if (file_exists("$absoluteurl"."categories.xml") AND isset($parser->document->category)) {

		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;

		foreach($parser->document->category as $singlecategory)
		{
			$arr[] .= $singlecategory->description[0]->tagData;
			$arrid[] .= $singlecategory->id[0]->tagData;
			$n++;
		}

		foreach ($arr as $key => $val) {

			if ($_GET['cat'] == $arrid[$key]) {
				$podcast_title .= " - $val";

			} 
		}
	}
	##### end category title




	##### CONTENT DEPURATION n.1
	#Depurate feed content according to iTunes specifications
	#$podcast_description = depurateContent($podcast_description); //description
	#$copyright = depurateContent($copyright); //copyright notice
	#$author_name = depurateContent($author_name); // author's name specified in config.php
	$itunes_category[0] = depurateContent($itunes_category[0]);
	$itunes_category[1] = depurateContent($itunes_category[1]);
	$itunes_category[2] = depurateContent($itunes_category[2]);


	######

	$head_feed ="<?xml version=\"1.0\" encoding=\"$feed_encoding\"?>
	<!-- generator=\"Podcast Generator $podcastgen_version\" -->
		<rss xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" xml:lang=\"$feed_language\" version=\"2.0\">
	<channel>
		<title>$podcast_title</title>
		<link>$url</link>
		<description>$podcast_description</description>
		<generator>Podcast Generator $podcastgen_version - http://podcastgen.sourceforge.net</generator>
	<lastBuildDate>".date("r")."</lastBuildDate>
		<language>$feed_language</language>
		<copyright>$copyright</copyright>";
		
		
		if (file_exists($cat_image)) { 
			$head_feed .= "<siono>yes</siono>";
			$head_feed .= "
			<itunes:image href=\"$url$cat_image\" />
			<image>
			<url>$url$cat_image</url>";
		}
		else{
		$head_feed .= "<siono>no: $cat_image</siono>";
		$head_feed .= "<itunes:image href=\"".$url.$img_dir."itunes_image.jpg\" />
		<image>
		<url>".$url.$img_dir."itunes_image.jpg</url>";
		}
		
		$head_feed .= "
		<title>$podcast_title</title>
		<link>$url</link>
		</image>
		<itunes:summary>$podcast_description</itunes:summary>
		<itunes:subtitle>$podcast_description</itunes:subtitle>
		<itunes:author>$author_name</itunes:author>
		<itunes:owner>
		<itunes:name>$author_name</itunes:name>
		<itunes:email>$author_email</itunes:email>
		</itunes:owner>
		<itunes:explicit>$explicit_podcast</itunes:explicit>
		";

	### iTunes categories:

	if ($itunes_category[0]!=NULL) { //category 1

		$cat1 =explode(":",$itunes_category[0]);
		$cat1 = str_replace('&', ' &amp; ', $cat1); // depurate &

		$head_feed.= "<itunes:category text=\"$cat1[0]\">
			";

		if (isset($cat1[1]) AND $cat1[1]!=NULL) { 

			$head_feed.= "<itunes:category text=\"$cat1[1]\" />
				";

		}

		$head_feed.= "</itunes:category>
			";

	} //end category 1


	if ($itunes_category[1]!=NULL) { //category 2

		$cat2 =explode(":",$itunes_category[1]);
		$cat2 = str_replace('&', ' &amp; ', $cat2); // depurate &

		$head_feed.= "<itunes:category text=\"$cat2[0]\">
			";

		if (isset ($cat2[1]) AND $cat2[1]!=NULL) { 

			$head_feed.= "<itunes:category text=\"$cat2[1]\" />
				";

		}

		$head_feed.= "</itunes:category>
			";

	} //end category 2


	if ($itunes_category[2]!=NULL) { //category 3

		$cat3 =explode(":",$itunes_category[2]);
		$cat3 = str_replace('&', ' &amp; ', $cat3); // depurate &

		$head_feed.= "<itunes:category text=\"$cat3[0]\">
			";

		if (isset($cat3[1]) AND $cat3[1]!=NULL) { 

			$head_feed.= "<itunes:category text=\"$cat3[1]\" />
				";

		}

		$head_feed.= "</itunes:category>
			";

	} //end category 2






	// Open podcast directory
	$handle = opendir ($absoluteurl.$upload_dir);
	while (($filename = readdir ($handle)) !== false)
	{

		if ($filename != '..' && $filename != '.' && $filename != 'index.htm' && $filename != '_vti_cnf')
		{

			$file_array[$filename] = filemtime ($absoluteurl.$upload_dir.$filename);
		}

	}

	if (!empty($file_array)) { //if directory is not empty


		# asort ($file_array);
		arsort ($file_array); //the opposite of asort (inverse order)

		$recent_count = 0; //set recents to zero



		$single_file = NULL; //define and empty variable


		############# START CICLE ###################
		foreach ($file_array as $key => $value)

		{


			if ($recent_count < $recent_episode_in_feed OR $recent_episode_in_feed == "All") { //ir recents are not more than specified in config.php



				$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)


				$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes); 


				if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


					$podcast_filetype = $fileData[0];

					###### Mimetype
					$filemimetype=$fileData[1]; //define mimetype to put in the feed


					if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php




						############
						$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file




						if (file_exists("$filedescr")) { //if database file exists 


							//$file_contents=NULL; 


							######## INCLUDE PARSER AND PARSE
							//load XML parser for PHP4 or PHP5
		//					require_once("$absoluteurl"."components/xmlparser/loadparser.php");

							# READ the XML database file and parse the fields
							include("$absoluteurl"."core/readXMLdb.php");



							### Here the output code for the episode is created

							# Fields Legend (parsed from XML):
							# $text_title = episode title
							# $text_shortdesc = short description
							# $text_longdesc = long description
							# $text_imgpg = image (url) associated to episode
							# $text_categoriespg = categories
							# $text_keywordspg = keywords
							# $text_explicitpg = explicit podcast (yes or no)
							# $text_authornamepg = author's name
							# $text_authoremailpg = author's email


							//category BELONGING CHECK
							if ($_GET['cat'] != NULL AND $_GET['cat'] == "$text_category1" OR $_GET['cat'] == "$text_category2" OR $_GET['cat'] == "$text_category3") {

								$oneispresent = "yes"; //at least one episode is present in a category


								//depuration of long description field, appearing in iTunes when you click the "circled i" next to the podcast description


								$text_longdesc = stripslashes($text_longdesc);
								$text_longdesc = strip_tags($text_longdesc);



								#### CONTENT DEPURATION N.2
								$text_title = depurateContent($text_title); //title
								$text_shortdesc = depurateContent($text_shortdesc); //short desc
								$text_longdesc = depurateContent($text_longdesc); //long desc
								$text_keywordspg = depurateContent($text_keywordspg); //Keywords
								$text_authornamepg = depurateContent($text_authornamepg); //author's name

								$file_size = filesize("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype");
								$filetime = filemtime ("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype");
								$filepubdate = date ('r', $filetime);



								$single_file.="<item>
									<title>$text_title</title>
									<itunes:subtitle>$text_shortdesc</itunes:subtitle>
									<itunes:summary><![CDATA[ $text_longdesc ]]></itunes:summary>
									<description>$text_shortdesc</description>
									<link>$link$key</link>
									<enclosure url=\"$url$upload_dir$key\" length=\"$file_size\" type=\"$filemimetype\"/>
									<guid>$link$key</guid>
									";


								###### GETID3 - DURATION
								require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)
								$getID3 = new getID3; //initialize getID3 engine

								# File details (duration, bitrate, etc...)
								$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype"); //read file tags

								$file_duration = @$ThisFileInfo['playtime_string'];

								if($file_duration!=NULL) { // display file duration
									$single_file.= "<itunes:duration>$file_duration</itunes:duration>
										";
								} 


								### AUTHOR
								if ($text_authornamepg==NULL OR $text_authornamepg==",") { //if author field is empty

									$single_file.= "<author>$author_email ($author_name)</author>
										<itunes:author>$author_name</itunes:author>
										";

								} 

								else { //if author is present

									$single_file.= "<author>$text_authoremailpg ($text_authornamepg)</author>
										<itunes:author>$text_authornamepg</itunes:author>
										";
								}


								## KEYWORDS
								if ($text_keywordspg!=NULL) { //if keywords are present

									$single_file.= "<itunes:keywords>$text_keywordspg</itunes:keywords>";

								} 

								if ($text_explicitpg!=NULL) {
									$single_file.= "<itunes:explicit>$text_explicitpg</itunes:explicit>
										";
								}


								$single_file.= "<pubDate>$filepubdate</pubDate>
									</item>";


								$recent_count++; // increment recent counter

							}	
						} 

					} 

				}
			}
		}

	}






	#########
	##########coda


	$tail_feed ="</channel></rss>";


	#### PRINT FEED
	echo "$head_feed"."$single_file"."$tail_feed";


}


?>