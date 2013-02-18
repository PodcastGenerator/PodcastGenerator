<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################


## Support for multiple file types (e.g. mp3, ogg, mpg, avi) at the same time.

function checkFileType ($filetype,$podcast_filetypes,$filemimetypes) {
	$i=0;
	$bool=false;
	$fileData = array();

	
	while (($i < sizeof($podcast_filetypes)) && $bool==false) {
		if ($filetype==$podcast_filetypes[$i]) {
			$fileData[0]=$podcast_filetypes[$i];
			$fileData[1]=$filemimetypes[$i];
			$bool=true;
		}
		$i+=1;
	}
	return $fileData;
}


########

## Rename uploaded file - STRICT

function renamefilestrict ($filetorename) { // strict rename policy (just characters from a to z and numbers... no accents and other characters). This kind of renaming can have problems with some languages (e.g. oriental)

	$filetorename = preg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($filetorename))));

	return $filetorename;

}


########

## Rename uploaded file - LESS STRICT

function renamefile ($filetorename) { // normal file rename policy

	$filetorename = strtolower($filetorename); // lower-case.
	$filetorename = strip_tags($filetorename); // remove HTML tags.
	$filetorename = preg_replace('!\s+!','_',$filetorename); // change space chars to underscores.
	$filetorename = stripslashes($filetorename); //remove slashes in the file name
	$filetorename = str_replace("'", "", $filetorename);
	$filetorename = str_replace("&", "_and_", $filetorename);

	return $filetorename;

}

########

## Validate e-mail address

function validate_email ($address) { //validate email address
	return (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $address));
}

########

## Depurate feed Content from non accepted characters (according also to iTunes specifications)

function depurateContent($content) {
	$content = stripslashes($content);				
	$content = str_replace('<', '&lt;', $content);
	$content = str_replace('>', '&gt;', $content);
	$content = str_replace('& ', '&amp; ', $content);
	$content = str_replace('’', '&apos;', $content);
	$content = str_replace('"', '&quot;', $content);
	$content = str_replace('©', '&#xA9;', $content);
	$content = str_replace('&copy;', '&#xA9;', $content);
	$content = str_replace('℗', '&#x2117;', $content);
	$content = str_replace('™', '&#x2122;', $content);
	return $content;
}


############ Determine whether to use the old or the new theme engine
//It depends on the presence or absence of the file theme.xml in the theme root folder
function useNewThemeEngine($theme_path) //$theme_path is defined in config.php
{
	if (file_exists($theme_path.'theme.xml')) { 
	return TRUE;
	}
	else {
	return FALSE;
	}
}


############ Is this an admin page?
function isThisAdminPage ()
{
if (isset($_GET['p']) and $_GET['p'] == "admin" AND isset($_SESSION["user_session"])

) return TRUE;
}


############ Create form date and time
	function CreaFormData($inName, $useDate=0, $dateformat) //inName is the form name, it can be null
	{ 
	// array with months
	$monthName = array(1=> _("January"), _("February"), _("March"),
	_("April"), _("May"), _("June"), _("July"), _("August"),
	_("September"), _("October"), _("November"), _("December"));

	// se data non specificata, o invalida, usa timestamp corrente
	if($useDate == NULL)
	{
	$useDate = Time();
	}
	
	$outputform =  _("Date:")." "; //title
	
	//day
	$outputformDAY =  "<select name=\"" . $inName . "Day\">\n";
	for($currentDay=1; $currentDay <= 31; $currentDay++)
	{
	$outputformDAY .=  "<option value=\"$currentDay\"";
	if(intval(date( "d", $useDate))==$currentDay)
	{
	$outputformDAY .=  " selected";
	}
	$outputformDAY .=  ">$currentDay\n";
	}
	$outputformDAY .=  "</select>";

	//mese
	$outputformMONTH =  "<select name=\"" . $inName . "Month\">\n";
	for($currentMonth = 1; $currentMonth <= 12; $currentMonth++)
	{
	$outputformMONTH .=  "<option value=\"";
	$outputformMONTH .=  intval($currentMonth);
	$outputformMONTH .=  "\"";
	if(intval(date( "m", $useDate))==$currentMonth)
	{
	$outputformMONTH .=  " selected";
	}
	$outputformMONTH .=  ">" . $monthName[$currentMonth] . "\n";
	}
	$outputformMONTH .=  "</select>";

	//anno
	$outputformYEAR =  "<select name=\"" . $inName . "Year\">\n";
	$startYear = date( "Y", $useDate);
	for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++)
	{
	$outputformYEAR .=  "<option value=\"$currentYear\"";
	if(date( "Y", $useDate)==$currentYear)
	{
	$outputformYEAR .=  " selected";
	}
	$outputformYEAR .=  ">$currentYear\n";
	}
	$outputformYEAR .=  "</select>";
	
	
	if ($dateformat == "m-d-Y") {
	$outputform.= $outputformMONTH.$outputformDAY.$outputformYEAR;}
	elseif ($dateformat == "Y-m-d") {
	$outputform.= $outputformYEAR.$outputformMONTH.$outputformDAY;}
	else { $outputform.= $outputformDAY.$outputformMONTH.$outputformYEAR; }
	
	
	$outputform .=  "&nbsp;&nbsp;"; //two blank spaces
	$outputform .=  _("Time:")." "; //titoletto


    //ore
	$outputform .=  "<select name=\"" . $inName . "Hour\">\n";
	for($currentHour = 0; $currentHour <= 23; $currentHour++)
	{
	$outputform .=  "<option value=\"";
	$outputform .=  intval($currentHour);
	$outputform .=  "\"";
	if(intval(date( "G", $useDate))==$currentHour)
	{
	$outputform .=  " selected";
	}
	$outputform .=  ">" . $currentHour. "\n";
	}
	$outputform .=  "</select>";
	
	//minuti
	$outputform .=  "<select name=\"" . $inName . "Minute\">\n";
	for($currentMinute = 0; $currentMinute <= 59; $currentMinute++)
	{
	$outputform .=  "<option value=\"";
	
	if ($currentMinute <= 9) {
	$outputform .=  "0".intval($currentMinute); } //add 0 before number from 1 to 9
	else { $outputform .=  intval($currentMinute); }
	
	$outputform .=  "\"";
	if(intval(date( "i", $useDate))==$currentMinute)
	{
	$outputform .=  " selected";
	}
	
	if ($currentMinute <= 9) {
	$outputform .=  ">0".intval($currentMinute). "\n"; } //aggiungi zero ai minuti da 1 a 9
	else { $outputform .=  ">".intval($currentMinute). "\n"; }
	
	}
	$outputform .=  "</select>";

	
	return $outputform;

} // End - form date and time




#################### SOCIAL NETWORK INTEGRATION

//$fullURL,$text_title are episode data. the rest: value 1 (/ TRUE) enable a certain social network, value 0 disables
function displaySocialNetworkButtons($fullURL,$text_title,$fb,$tw,$gp) { 
	
$construct_output = '<br />'; //space above

//FB Like Button
if ($fb == TRUE) {
$construct_output .= '
<iframe src="//www.facebook.com/plugins/like.php?href='.$fullURL.'&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=361488987252256" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe>
';
}

//TWITTER Button
if ($tw == TRUE) {
$construct_output.= '
<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$fullURL.'" data-text="'.$text_title.'" data-hashtags="podcastgen">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
';
}

//G+ Button
if ($gp == TRUE) {
$construct_output .= '
<div class="g-plusone" data-size="medium" data-href="'.$fullURL.'"></div>

<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
';
}
	
	return $construct_output;
}


////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//SHOW PODCAST EPISODES

function showPodcastEpisodes($all,$category) { //$all is a bool, yes or not (the latter meaning that it takes $max_recent in config.php. $category null means all categories

include("core/includes.php");

require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

$resulting_episodes = NULL; // Define variable that will contain output of this function

$getID3 = new getID3; //initialize getID3 engine

//load XML parser for PHP4 or PHP5
//include("$absoluteurl"."components/xmlparser/loadparser.php");


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



	foreach ($file_array as $key => $value) //start loop through each file in the media directory

	{

		if ($recent_count < $max_recent) { //COUNT RECENTS if recents are not more than specified in config.php



			$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)


		$count_of_elements = count($file_multimediale);
		
		$file_name = $file_multimediale[0];
		$file_extension = $file_multimediale[$count_of_elements - 1];
		echo "file name:".$file_name;
		echo "<br>estensione:".$file_extension;
			
			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


				$podcast_filetype = $fileData[0];


				if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php

					//$file_size = filesize("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype");
					$file_size = round(filesize("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype")/1048576,2);
				//	$file_size = round($file_size, 2);

					############
					$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file




					if (file_exists("$filedescr")) { //if database file exists 


						//$file_contents=NULL; 


						# READ the XML database file and parse the fields
						//include("$absoluteurl"."core/readXMLdb.php");


	# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");				
						

						#Define episode headline
						$episode_date = "<a name=\"$file_multimediale[0]\"></a>
							<a href=\"".$url."download.php?filename=$file_multimediale[0].$podcast_filetype\">
							<img src=\"podcast.gif\" alt=\""._("Download")." $text_title\" title=\""._("Download")." $text_title\" border=\"0\" align=\"left\" /></a> &nbsp;".date ($dateformat, $value)." <i>($file_size "._("MB").")</i>";


						# File details (duration, bitrate, etc...)
						$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype"); //read file tags

						$file_duration = @$ThisFileInfo['playtime_string'];

						if($file_duration!=NULL) { // display file duration
							$episode_details = _("Duration:")." ";
							$episode_details .= @$ThisFileInfo['playtime_string'];
							$episode_details .= " "._("m")." - "._('Filetype:')." ";
							$episode_details .= @$ThisFileInfo['fileformat'];

							if($podcast_filetype=="mp3") { //if mp3 show bitrate &co
								$episode_details .= " - "._("Bitrate")." ";
								$episode_details .= @$ThisFileInfo['bitrate']/1000;
								$episode_details .= " "._("KBPS")." - "._("Frequency:")." ";
								$episode_details .= @$ThisFileInfo['audio']['sample_rate'] ;
								$episode_details .= " "._("HZ");
							}

						} 


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

						$resulting_episodes .= 
							'<div class="episode">
							<p class="episode_date">'.$episode_date.'</p>';

						if (isset($episode_details)) {
							$resulting_episodes .= '<p class="episode_info">'.$episode_details.'</p>';
						}

						$resulting_episodes .= '<h3 class="episode_title"><a href="?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'">'.$text_title.'</a>';

						if ($podcast_filetype=="mpg" OR $podcast_filetype=="mpeg" OR $podcast_filetype=="mov" OR $podcast_filetype=="mp4" OR $podcast_filetype=="wmv" OR $podcast_filetype=="3gp" OR $podcast_filetype=="mp4" OR $podcast_filetype=="avi" OR $podcast_filetype=="flv" OR $podcast_filetype=="m4v") { // if it is a video

							$resulting_episodes .= '&nbsp;<img src="video.png" alt="'._("Video Podcast").'" />';
							$isvideo = "yes"; 

						}


						$resulting_episodes .= '</h3>
							<ul class="episode_imgdesc">';

						if(isset($text_imgpg) AND $text_imgpg!=NULL AND file_exists("$img_dir$text_imgpg")) {

							$resulting_episodes .= "<li><img src=\"$img_dir$text_imgpg\" class=\"episode_image\" alt=\"$text_title\" /></li>";

						}

						if(isset($text_longdesc) AND $text_longdesc!=NULL ) { // if is set long description

							$resulting_episodes .= 
								'<li>'.$text_longdesc;

						} else {

							$resulting_episodes .= 
								'<li>'.$text_shortdesc;	
						}


						if($enablestreaming=="yes" AND $podcast_filetype=="mp3") { // if streaming is enabled show streaming player

							include ("components/player/player.php");
							$resulting_episodes .= '<br /><br />'.$showplayercode; 

						} else {
							$resulting_episodes .= '<br />'; 
						}

						$resulting_episodes .= "<br />";

						if (isset($isvideo) AND $isvideo == "yes") {
							$resulting_episodes .= "<a href=\"".$url.$upload_dir."$file_multimediale[0].$podcast_filetype\" title=\""._("Watch this video (requires browser plugin)")."\"><span class=\"episode_download\">"._("Watch")."</span></a><span class=\"episode_download\"> - </span>";

							$isvideo = "no"; //so variable is assigned on every cicle

						}

						$resulting_episodes .= "<a href=\"".$url."download.php?filename=$file_multimediale[0].$podcast_filetype\" title=\""._("Download this episode")."\"><span class=\"episode_download\">"._("Download")."</span></a>
							</li>
							</ul>";
							
					
				//add social networks and embedded code
				include("$absoluteurl"."core/attachtoepisode.php");	
					

							
						$resulting_episodes .= "</div>";


						if ($recent_count == 0) { //use keywords of the most recent episode as meta tags in the home page
							$assignmetakeywords = $text_keywordspg;
						}

						$recent_count++; //increment recents
					} 

				} 

			}
		} //END - COUNT RECENTS (if statement)
		
		else  {  // i.e. if COUNT RECENTS condition occurs
		break; // Jump out of the loop 
		}
		
	} //END "if directory is not empty"
	
} else { // IF media directory is empty
	//$resulting_episodes .= '<div class="topseparator"><p>'._("Directory").' <b>'.$upload_dir.'</b> '._("is empty...").'</p></div>';
	
	$resulting_episodes .= '<div class="topseparator"><p>'._("No episodes at the moment.").'</p></div>';
	
}


return $resulting_episodes; // return results

} // end function showPodcastEpisodes




?>