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



########

## Sanitize URL Content to avoid cross-site scripting (XSS)

function avoidXSS($url) {
	$url = filter_var($url,FILTER_SANITIZE_URL);				
	$url = str_replace('<', '&lt;', $url);
	$url = str_replace('>', '&gt;', $url);
	$url = str_replace('& ', '&amp; ', $url);
	$url = str_replace('’', '&apos;', $url);
	$url = str_replace('"', '&quot;', $url);
	$url = str_replace('iframe', '', $url);
	$url = str_replace('width=', '', $url);
	$url = str_replace('height=', '', $url);
	$url = str_replace('src=', '', $url);
	$url = str_replace('javascript=', '', $url);
	return $url;
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
	
########### Security code, avoids cross-site scripting with Register Globals ON
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End	
else if (isset($_GET['p']) and $_GET['p'] == "admin" AND isset($_SESSION["user_session"])) return TRUE;
}


############ Create form date and time
	function CreaFormData($inName, $useDate=0, $dateformat) //inName is the form name, it can be null
	{ 
	// array with months
	$monthName = array(1=> _("Jan"), _("Feb"), _("Mar"),
	_("Apr"), _("May"), _("Jun"), _("Jul"), _("Aug"),
	_("Sep"), _("Oct"), _("Nov"), _("Dec"));

	// se data non specificata, o invalida, usa timestamp corrente
	if($useDate == NULL)
	{
	$useDate = Time();
	}
	
	$outputform =  '<p>'._("Date:").'</p>'; //title
	
	//day
	$outputformDAY =  "<select class=\"input-small\" name=\"" . $inName . "Day\">\n";
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
	$outputformMONTH =  "<select class=\"input-small\" name=\"" . $inName . "Month\">\n";
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
	$outputformYEAR =  "<select class=\"input-small\" name=\"" . $inName . "Year\">\n";
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
	$outputform .=  '<p>'._("Time:").'</p>'; //titoletto


    //ore
	$outputform .=  "<select class=\"input-small\" name=\"" . $inName . "Hour\">\n";
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
	$outputform .=  "<select class=\"input-small\" name=\"" . $inName . "Minute\">\n";
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

function showPodcastEpisodes($all,$category,$singleEpisode) { //$all is a bool, yes or not (the latter meaning that it takes $max_recent in config.php. $category null means all categories

include("core/includes.php");

$categoryURLforPagination = ""; //preserve category in links in number of pages at the button

if ($all == TRUE) {
$max_recent = 999999; //show all episode (workaround - could be more elegant I Know)
$categoryURLforPagination = "&cat=all"; //preserve category in links in number of pages at the button
}

require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

$finalOutputEpisodes = NULL; // Define variable that will contain output of this function

$getID3 = new getID3; //initialize getID3 engine


## Handle header if it's a category

//IF it's a category print category title and feed
if (isset($category) AND $category != NULL) {

//URL depuration
$category = avoidXSS($category);

$categoryURLforPagination = "&cat=".$category;

//retrieve existing categories (to read their description/long name)
$existingCategories = readPodcastCategories ($absoluteurl); //$existingCategories[$category] will be the name of the category (not the simple ID / $category)

	$category_header = '<div>';
	
	if (isset($existingCategories[$category])) {
	$category_header .= '<h3 class="sectionTitle"><a href="'.$url.'feed.php?cat='.$category.'"><img src="feed-icon.png" alt="'._("Subscribe to this category").'" border="0" /></a>&nbsp'.$existingCategories[$category].'</h3>';
	}
	
	$category_header .= '</div>';
}

## END - Handle header if it's a category


// Open podcast directory
$handle = opendir ($absoluteurl.$upload_dir);
while (($filename = readdir ($handle)) !== false)
{


//List of directory or filename to exclude
$toExclude = array("..",".","index.htm","_vti_cnf",".DS_Store",".svn");


	if (!in_array($filename, $toExclude)) $file_array[$filename] = filemtime ($absoluteurl.$upload_dir.$filename);

}

if (!empty($file_array)) { //if directory is not empty

if (!isset($atLeastOneEpisodeInCategory )) $atLeastOneEpisodeInCategory = FALSE; //Set bool to false. if we require a category and no episode are associated it will be set to true

	arsort ($file_array); //the opposite of asort (inverse order)

	$recent_count = 0; //set recents to zero


	//if isset page in variables GET
	if (isset($_GET["pgn"]) AND is_numeric($_GET["pgn"])) {


		$maxC = $episodeperpage * $_GET["pgn"];
		$minC = $episodeperpage* $_GET["pgn"] - $episodeperpage;

	//	echo "<br>MAX: $maxC and MIN: $minC"; //debug
		
	} 
	//if home page or no pages are set in GET 
	else {
		
	$maxC = $episodeperpage;
	$minC = 0;
		
	}


	foreach ($file_array as $key => $value) //loop through each file in the media dir
		{
	
		$resulting_episodes = NULL; //reset VAR

		//avoid reading files that won't be displayed in this page
		if ($recent_count > $maxC) {
				$recent_count = count($file_array)/2; //count($file_array)/2 is the total number of episodes
			//	echo "total episodes:".count($file_array)/2;
				break;
			}



		if ($recent_count < $max_recent) { //COUNT RECENTS if recents are not more than specified in config.php
		
		
		
		

		$file_parts = divideFilenameFromExtension($key); //supports more full stops . in the file name. PHP >= 5.2.0 needed
		$filenameWithouExtension = $file_parts[0];
		$fileExtension = $file_parts[1];
	

		$fileData = checkFileType($fileExtension,$podcast_filetypes,$filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


			$podcast_filetype = $fileData[0];


			if ($fileExtension==$podcast_filetype) { // if the extension is accepted

					$file_size = round(filesize("$absoluteurl"."$upload_dir$filenameWithouExtension.$podcast_filetype")/1048576,2);

					
					$filedescr = $absoluteurl.$upload_dir.$filenameWithouExtension.'.xml'; //database file


					if (file_exists($filedescr)) { //if database file exists 


	# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");		
						
						//Fields retrieved from XML
						# $text_title = episode title
						# $text_shortdesc = short description
						# $text_longdesc = long description
						# $text_imgpg = image (url) associated to episode
						# $text_category1, $text_category2, $text_category3 = categories
						# $text_keywordspg = keywords
						# $text_explicitpg = explicit podcast (yes or no)
						# $text_authornamepg = author's name
						# $text_authoremailpg = author's email

						//echo "<p>1: $text_category1,2: $text_category2,3: $text_category3</p>";
						
						 
						//if 
						
						if (isset($category) AND $category != NULL) {
								if ($category != $text_category1 AND $category != $text_category2 AND $category != $text_category3) {
								//echo ">>>match Category: $category<br><br><br>";
								continue; //STOP this cicle in the loop and start a new cicle
								} else {
								$atLeastOneEpisodeInCategory = TRUE; //There is at least one episode
								}
						} 
						
						
						
						
						$episodeDateAndSize = date ($dateformat, $value)." <i>($file_size "._("MB").")</i>";
						

						# File details (duration, bitrate, etc...)
						$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$filenameWithouExtension.$podcast_filetype"); //read file tags

						$file_duration = @$ThisFileInfo['playtime_string'];

						if($file_duration!=NULL) { // display file duration
							$episode_details = _("Duration:")." ";
							$episode_details .= @$ThisFileInfo['playtime_string'];
							$episode_details .= " "._("m")." - "._('Filetype:')." ";
							$episode_details .= @$ThisFileInfo['fileformat'];

						if($podcast_filetype=="mp3") { //if mp3 show bitrate &co
							$episode_details .= " (";
							$episode_details .= @$ThisFileInfo['bitrate']/1000;
							$episode_details .= " "._("kbps")." ";
							$episode_details .= @$ThisFileInfo['audio']['sample_rate'] ;
							$episode_details .= " "._("Hz").")";
							}

						
						} 


	
//////////////////////////////////////////////////////////////////////////
//CONSTRUCT EPISODE OUTPUT!! (here <2.0 themes compatibility is preserved)

$numberOfEpisodesPerLine = 2; //number of episodes per line in some themes - defined in $numberOfEpisodesPerLine


if (useNewThemeEngine($theme_path)) { //If use new theme template
	
	//NB $resulting_episodes is not appended to the previous. it will be appended to $finalOutputEpisodes


		//just if the episod number is multiple of $numberOfEpisodesPerLine
		if ($recent_count % $numberOfEpisodesPerLine != 0 OR $recent_count == count($file_array)) {
		//open div with class row-fluid (theme based on bootstrap)
	
		$resulting_episodes .= '<div class="row-fluid">'; // row-fluid is a line that contains 1 or more episodes
		}

	$resulting_episodes .= '<div class="span6 6u episodebox">'; //open the single episode DIV
}

else { //if an old theme is used
	
	//NB $resulting_episodes is not appended to the previous. it will be appended to $finalOutputEpisodes

	$resulting_episodes .= '<div class="episode">'; //open the single episode DIV
} 
	
							

	$resulting_episodes .= '<h3 class="episode_title">'.$text_title;


						
//List of file extensions classified as videos
$listOfVideoFormats = array("mpg","mpeg","mov","mp4","wmv","3gp","avi","flv","m4v");
if (in_array($podcast_filetype, $listOfVideoFormats)) { // if it is a video
$resulting_episodes .= '&nbsp;<img src="video.png" alt="'._("Video Podcast").'" />';
$isvideo = TRUE; 
}


$resulting_episodes .= '</h3>';
	

// EPISODE DATE AND SIZE
$resulting_episodes .= '<p class="episode_date">'.$episodeDateAndSize.'</p>';


///////////////////////////////////////////
//EDIT DELETE BUTTON (JUST IF LOGGED IN)

// IF USER IS LOGGED AND PAGE IS ALL PODCAST
if (!isset($_REQUEST['amilogged']) AND isset($_SESSION["user_session"]) AND isset($_GET["cat"]) AND ($_GET["cat"]) == "all") { 


		$resulting_episodes .= '<p><a class="btn btn-inverse btn-mini" href="?p=admin&amp;do=edit&amp;=episode&amp;name='.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("Edit / Delete").'</a></p>';

}
		
		

// END - EDIT DELETE BUTTON
///////////////////////////////////////////
					/*	
					$resulting_episodes .= '<ul class="episode_imgdesc">';

						if(isset($text_imgpg) AND $text_imgpg!=NULL AND file_exists("$img_dir$text_imgpg")) {

							$resulting_episodes .= "<li><img src=\"$img_dir$text_imgpg\" class=\"episode_image\" alt=\"$text_title\" /></li>";

						} */

						/*
						if(isset($text_longdesc) AND $text_longdesc!=NULL ) { // if is set long description

							$resulting_episodes .= $text_longdesc;

						} else {

							$resulting_episodes .= $text_shortdesc;	
						}
						*/
						

						$resulting_episodes .= '<p>'.$text_shortdesc.'</p>';	 //SHOW short description (no HTML)
						
						
						#BUTTONS
						$resulting_episodes .= '<p>';
						
						//show button view Details
						$resulting_episodes .= '<a class="btn" href="?name='.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("View details").' &raquo;</a>&nbsp;&nbsp;';
						
						
						## BUTTON DOWNLOAD
						//iOS device has been reported having some trouble downloading episode using the "download.php" forced download...
						if (!detectMobileDevice()) { //IF IS NOT MOBILE DEVICE
						//show button (FORCED) download using download.php
						$resulting_episodes .= '<a class="btn" href="'.$url.'download.php?filename='.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("Download").' &raquo;</a>';
						} 
						else { // SHOW BUTTON DOWNLOAD THAT links directly to the file (so no problems with PHP forcing headers)
						$resulting_episodes .= '<a class="btn" href="'.$url.$upload_dir.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("Download").' &raquo;</a>';
						}
						## END - BUTTON DOWNLOAD
						
						$resulting_episodes .= '</p>';
						#END BUTTONS
		
						
						
					//EPISODE DURATION, FILETYPE AND OTHER DETAILS IS AVAILABLE
if (isset($episode_details)) {
$resulting_episodes .= '<p class="episode_info">'.$episode_details.'</p>';
}
						
							#MP3 FLASH PLAYER
// if it's not a mobile device and streaming is enabled show streaming player
						if(!detectMobileDevice() AND $enablestreaming=="yes" AND $podcast_filetype=="mp3") { 

							include ("components/player/player.php");
							$resulting_episodes .= ''.$showplayercode; 
							$resulting_episodes .= '<br />'; 
						} 
						# END - MP3 FLASH PLAYER
						

					//	$resulting_episodes .= "<br />";

						if (isset($isvideo) AND $isvideo == TRUE) {
	
//Display watch button (old)	
//$resulting_episodes .= "<a href=\"".$url.$upload_dir."$filenameWithouExtension.$podcast_filetype\" title=\""._("Watch this video (requires browser plugin)")."\"><span class=\"episode_download\">"._("Watch")."</span></a><span class=\"episode_download\"> - </span>";

							$isvideo = FALSE; //so variable is assigned on every cicle
						}


					
				//add social networks and embedded code
				include("$absoluteurl"."core/attachtoepisode.php");	
					

					
					
					//Blank space
					$resulting_episodes .= "<br />";
					

						$resulting_episodes .= "</div>";

							
						//close line with one or more episode for new themes >=2.0
						if (useNewThemeEngine($theme_path) AND $recent_count % $numberOfEpisodesPerLine != 0 OR $recent_count == count($file_array)) { 
						//close class row-fluid
						$resulting_episodes .= "</div>";
						}
						
			

						if ($recent_count == 0) { //use keywords of the most recent episode as meta tags in the home page
							$assignmetakeywords = $text_keywordspg;
						}

						$recent_count++; //increment recents
					} 

				} 

			}
			
		//FINAL OUTPUT
if ($recent_count <= $maxC AND $recent_count > $minC) {
		$finalOutputEpisodes .= $resulting_episodes;
	}

		
			
		} //END - COUNT RECENTS (if statement)
		
		else  {  // i.e. if COUNT RECENTS condition occurs
		break; // Jump out of the loop 
		}
				
			
			

	} //END "if directory is not empty"
	
} else { // IF media directory is empty
	//$resulting_episodes .= '<div class="topseparator"><p>'._("Directory").' <b>'.$upload_dir.'</b> '._("is empty...").'</p></div>';
	
	$finalOutputEpisodes .= '<div class="topseparator"><p>'._("No episodes at the moment.").'</p></div>';
	
}

	//IF a category is requested
	if (isset($category) AND $category != NULL) {
	
		//If a category is requested and this doesn't contain any episode, then tell to the user there are no episodes
		if (isset($atLeastOneEpisodeInCategory) AND $atLeastOneEpisodeInCategory != TRUE) {
		$finalOutputEpisodes .= '<p>'.("No episodes here yet...").'</p>';
		}
		
	$finalOutputEpisodes = $category_header.$finalOutputEpisodes; //category header at the top
	
	} 



	//CREATE PAGES
	
	//calculate total number of pages
	$numberOfPages = ($recent_count / $episodeperpage);
	if ($numberOfPages>1) $numberOfPages = ceil($numberOfPages); //round to the next integer
	
	//echo $numberOfPages;
	
	if (isset($_GET['p'])) $pageURLforPagination = avoidXSS(($_GET['p']));
	else $pageURLforPagination = "home";
	
	if  (isset($_GET["pgn"])) $thisCurrentPage = $_GET["pgn"];
	else $thisCurrentPage = 1;
	
	if ($recent_count > $episodeperpage) {
		
		$finalOutputEpisodes .= '<div class="row-fluid" style="clear:both;"><p>';
		//print page index and links
		for ($onePage =1; $onePage <= $numberOfPages; $onePage++) {
		
		if ($thisCurrentPage == $onePage) {
		$finalOutputEpisodes .= $onePage.' | ';		
		} else
		$finalOutputEpisodes .= '
		<a href="?p='.$pageURLforPagination.$categoryURLforPagination.'&amp;pgn='.$onePage.'">'.$onePage.'</a> | ';		
		}
		$finalOutputEpisodes .= '</p></div>';
	}

return $finalOutputEpisodes; // return results

} // end function showPodcastEpisodes



function divideFilenameFromExtension ($filetodivide) {

	$file_parts = pathinfo($filetodivide); //divide filename from extension 

	$fileParts = array();
	
		$fileParts[0] = $file_parts['filename'];
		$fileParts[1] = $file_parts['extension'];
		
		return $fileParts;
		
}


function readPodcastCategories ($absoluteurl) {

	if (file_exists($absoluteurl."categories.xml")) { //if categories file exists

	$parser = simplexml_load_file($absoluteurl."categories.xml",'SimpleXMLElement',LIBXML_NOCDATA);

	//var_dump($parser); //Debug

	$existingCategories = array();
	
			$n = 0;
			foreach($parser->category as $singlecategory) {

			//create array containing category id as seed and description for each id
			$catID = $singlecategory->id[0];
			$catDescription = $singlecategory->description[0];
			$existingCategories["$catID"] = $catDescription;
				
			$n++;
			}
	}
	return $existingCategories;	
}		


function detectMobileDevice() {

//Some of the main mobile devices
$iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android= stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$webOS= stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
$Blackberry= stripos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
$Kindle= stripos($_SERVER['HTTP_USER_AGENT'],"Kindle");

	//here we can do something with the mobile devices or just a subset of them
	if($iPod OR $iPhone OR $iPad OR $Android OR $webOS OR $Blackberry OR $Kindle){
	return TRUE;
	} else {
	return FALSE;
	} 
}



////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//SHOW SINGLE PODCAST EPISODE

function showSinglePodcastEpisode($all,$category,$singleEpisode,$justTitle) { //$all is a bool, yes or not (the latter meaning that it takes $max_recent in config.php. $category null means all categories.
//Note that $justTitle == 1 will return just the title of the episode (for meta tags in the head etc...) and not follow with the function

include("core/includes.php");


if ($all == TRUE) $max_recent = 999999; //show all episode (workaround - could be more elegant I Know)

require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

$resulting_episodes = NULL; // Define variable that will contain output of this function

$getID3 = new getID3; //initialize getID3 engine




if (isset($singleEpisode) AND $singleEpisode != NULL ) {
	
	
	$key = $singleEpisode;
		

		$file_parts = divideFilenameFromExtension($key); //supports more full stops . in the file name. PHP >= 5.2.0 needed
		$filenameWithouExtension = $file_parts[0];
		$fileExtension = $file_parts[1];
	

		$fileData = checkFileType($fileExtension,$podcast_filetypes,$filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


			$podcast_filetype = $fileData[0];


			if ($fileExtension==$podcast_filetype AND file_exists($absoluteurl."$upload_dir$filenameWithouExtension.$podcast_filetype")) { // if the extension is accepted AND the file EXISTS

					$file_size = round(filesize($absoluteurl."$upload_dir$filenameWithouExtension.$podcast_filetype")/1048576,2);

					
					//TIMESTAMP
					$file_timestamp = filemtime($absoluteurl."$upload_dir$filenameWithouExtension.$podcast_filetype");
					
					
					$filedescr = $absoluteurl.$upload_dir.$filenameWithouExtension.'.xml'; //database file


					if (file_exists($filedescr)) { //if database file exists 


	# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");		
						
						//Fields retrieved from XML
						# $text_title = episode title
						# $text_shortdesc = short description
						# $text_longdesc = long description
						# $text_imgpg = image (url) associated to episode
						# $text_category1, $text_category2, $text_category3 = categories
						# $text_keywordspg = keywords
						# $text_explicitpg = explicit podcast (yes or no)
						# $text_authornamepg = author's name
						# $text_authoremailpg = author's email

						//echo "<p>1: $text_category1,2: $text_category2,3: $text_category3</p>";
						

//RETURN JUST THE TITLE IF REQUESTED
if ($justTitle == 1) {
return $text_title;
} 			

						$episodeDateAndSize = date ($dateformat,$file_timestamp)." <i>($file_size "._("MB").")</i>";
						

						# File details (duration, bitrate, etc...)
						$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$filenameWithouExtension.$podcast_filetype"); //read file tags

						$file_duration = @$ThisFileInfo['playtime_string'];

						if($file_duration!=NULL) { // display file duration
							$episode_details = _("Duration:")." ";
							$episode_details .= @$ThisFileInfo['playtime_string'];
							$episode_details .= " "._("m")." - "._('Filetype:')." ";
							$episode_details .= @$ThisFileInfo['fileformat'];

						if($podcast_filetype=="mp3") { //if mp3 show bitrate &co
							$episode_details .= " (";
							$episode_details .= @$ThisFileInfo['bitrate']/1000;
							$episode_details .= " "._("kbps")." ";
							$episode_details .= @$ThisFileInfo['audio']['sample_rate'] ;
							$episode_details .= " "._("Hz").")";
							}

						
						} 


	
//////////////////////////////////////////////////////////////////////////
//CONSTRUCT EPISODE OUTPUT!! (here <2.0 themes compatibility is preserved)

$numberOfEpisodesPerLine = 2; //number of episodes per line in some themes - defined in $numberOfEpisodesPerLine


if (useNewThemeEngine($theme_path)) { //If use new theme template


	$resulting_episodes .= '<div class="episodebox">'; //open the single episode BOX
	$resulting_episodes .= '<div class="span6">'; //open the single episode DIV
}

else { //if an old theme is used
	$resulting_episodes .= '<div class="episode">'; //open the single episode DIV
} 
	
							

	$resulting_episodes .= '<h3 class="episode_title">'.$text_title;


						
//List of file extensions classified as videos
$listOfVideoFormats = array("mpg","mpeg","mov","mp4","wmv","3gp","avi","flv","m4v");
if (in_array($podcast_filetype, $listOfVideoFormats)) { // if it is a video
$resulting_episodes .= '&nbsp;<img src="video.png" alt="'._("Video Podcast").'" />';
$isvideo = TRUE; 
}


$resulting_episodes .= '</h3>';
	

// EPISODE DATE AND SIZE
$resulting_episodes .= '<p class="episode_date">'.$episodeDateAndSize.'</p>';


///////////////////////////////////////////
//EDIT DELETE BUTTON (JUST IF LOGGED IN)

// IF USER IS LOGGED AND PAGE IS ALL PODCAST
if (!isset($_REQUEST['amilogged']) AND isset($_SESSION["user_session"]) AND isset($_GET["cat"]) AND ($_GET["cat"]) == "all") { 


		$resulting_episodes .= '<p><a class="btn btn-inverse btn-mini" href="?p=admin&amp;do=edit&amp;=episode&amp;name='.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("Edit / Delete").'</a></p>';

}
		
						

						$resulting_episodes .= '<p>'.$text_longdesc.'</p>';	 //SHOW short description (no HTML)
						
						$resulting_episodes .= '<p><em>'._("Categories").'</em> ';	
						
						if ($text_category1 != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$text_category1.'">'.$text_category1.'</a>';
						if ($text_category2 != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$text_category2.'">'.$text_category2.'</a>';
						if ($text_category3 != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$text_category3.'">'.$text_category3.'</a>';
						
						$resulting_episodes .= '</p>';
						
						
						
						#BUTTONS
						$resulting_episodes .= '<p>';
						
						//show button view Details
						//$resulting_episodes .= '<a class="btn" href="?p=episode&amp;name='.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("View details").' &raquo;</a>&nbsp;&nbsp;';
						
						
						## BUTTON DOWNLOAD
						//iOS device has been reported having some trouble downloading episode using the "download.php" forced download...
						if (!detectMobileDevice()) { //IF IS NOT MOBILE DEVICE
						//show button (FORCED) download using download.php
						$resulting_episodes .= '<a class="btn" href="'.$url.'download.php?filename='.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("Download").' &raquo;</a>';
						} 
						else { // SHOW BUTTON DOWNLOAD THAT links directly to the file (so no problems with PHP forcing headers)
						$resulting_episodes .= '<a class="btn" href="'.$url.$upload_dir.$filenameWithouExtension.'.'.$podcast_filetype.'">'._("Download").' &raquo;</a>';
						}
						## END - BUTTON DOWNLOAD
						
						$resulting_episodes .= '</p>';
						#END BUTTONS
		
						
						
					//EPISODE DURATION, FILETYPE AND OTHER DETAILS IS AVAILABLE
if (isset($episode_details)) {
$resulting_episodes .= '<p class="episode_info">'.$episode_details.'</p>';
}
						
							#MP3 FLASH PLAYER
// if it's not a mobile device and streaming is enabled show streaming player
						if(!detectMobileDevice() AND $enablestreaming=="yes" AND $podcast_filetype=="mp3") { 

							include ("components/player/player.php");
							$resulting_episodes .= ''.$showplayercode; 
							$resulting_episodes .= '<br />'; 
						} 
						# END - MP3 FLASH PLAYER
						

					//	$resulting_episodes .= "<br />";

						if (isset($isvideo) AND $isvideo == TRUE) {
	
//Display watch button (old)	
//$resulting_episodes .= "<a href=\"".$url.$upload_dir."$filenameWithouExtension.$podcast_filetype\" title=\""._("Watch this video (requires browser plugin)")."\"><span class=\"episode_download\">"._("Watch")."</span></a><span class=\"episode_download\"> - </span>";

							$isvideo = FALSE; //so variable is assigned on every cicle
						}


					
				//add social networks and embedded code
				include("$absoluteurl"."core/attachtoepisode.php");	
					

					
					
					//Blank space
					$resulting_episodes .= "<br />";
					

						$resulting_episodes .= "</div>"; //close the single episode DIV

						$resulting_episodes .= "</div>";	//close the single episode BOX
					
						
			

						
					} 

				} 

			} 

	
} else { // IF media directory is empty
	//$resulting_episodes .= '<div class="topseparator"><p>'._("Directory").' <b>'.$upload_dir.'</b> '._("is empty...").'</p></div>';
	
	$resulting_episodes .= '<div class="topseparator"><p>'._("No episodes at the moment.").'</p></div>';
	
}

	

return $resulting_episodes; // return results


} // end function showPodcastEpisodes






?>