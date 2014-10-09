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

function checkFileType ($filetype,$podcast_filetypes,$podcast_filemimetypes) {
	$i=0;
	$bool=FALSE;
	$fileData = array();

	
	while (($i < sizeof($podcast_filetypes)) && $bool==FALSE) {
		if ($filetype==$podcast_filetypes[$i]) {
			$fileData[0]=$podcast_filetypes[$i];
			$fileData[1]=$podcast_filemimetypes[$i];
			$bool=TRUE;
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
	//$content = htmlentities($content); //this messes up things		
	return $content;
}

//this is just for CDATA fields such as <itunes:summary> (long description in PG)
function depurateCDATAfield($content) {
	$content = str_replace(']]>', '] ] &gt;', $content);
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

$disableextras = FALSE;


$categoryURLforPagination = ""; //preserve category URL GET

if ($all == TRUE) {
$max_recent = 999999; //show all episode (workaround - could be more elegant)
$categoryURLforPagination = "&cat=all"; //preserve category in links in number of pages at the button

	//don't show social networks nor user GETID3 when showing all episodes and noextras is appended to the URL
	if (isset($_GET['noextras'])) {
	$disableextras = TRUE;
	$categoryURLforPagination .= "&noextras"; //preserve category in links in number of pages at the button
	}

} else { // in home page, do not paginate but use $max_recent 
$episodeperpage = 999999; //do not use pagination (workaround - could be more elegant)
}


//if parameters is appended to the URL disable GETID3 (can slow down edit process when having a lot of files)
if (!$disableextras) {

//Note that GETID3 is initialized once
require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)
$getID3 = new getID3; //initialize getID3 engine

}

$finalOutputEpisodes = NULL; // Define variable that will contain output of this function




//HTML5 audio and video tag support (for web player)
//detectModernBrowser returns an array [0] => HTML5 audio support, [1] => HTML5 video support
$browserSupport = detectModernBrowser(); 


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
	$category_header .= '<h3 class="sectionTitle"><a href="'.$url.'feed.php?cat='.$category.'"><i class="fa fa-rss "></i></a>&nbsp'.$existingCategories[$category].'</h3>';
	}
	
	$category_header .= '</div>';
}

## END - Handle header if it's a category


// Open podcast directory

$fileNamesList = readMediaDir ($absoluteurl,$upload_dir);

if (!empty($fileNamesList)) { //if directory is not empty

if (!isset($atLeastOneEpisodeInCategory )) $atLeastOneEpisodeInCategory = FALSE; //Set bool to false. if we require a category and no episode are associated it will be set to true


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


	foreach ($fileNamesList as $singleFileName) //loop through each file in the media dir
		{
	
		$resulting_episodes = NULL; //reset VAR

		//avoid reading files that won't be displayed in this page
		if ($recent_count > $maxC) {
				$recent_count = count($fileNamesList)/2; //count($fileNamesList)/2 is the total number of episodes
			//	echo "total episodes:".count($fileNamesList)/2;
				break;
			}



		else if ($recent_count < $max_recent) { //COUNT RECENTS if recents are not more than specified in config.php
		
		$filefullpath = $absoluteurl.$upload_dir.$singleFileName;
		
		

		$file_parts = divideFilenameFromExtension($singleFileName); //supports more full stops . in the file name. PHP >= 5.2.0 needed
		$filenameWithoutExtension = $file_parts[0];
		$fileExtension = $file_parts[1];
	

		$fileData = checkFileType($fileExtension,$podcast_filetypes,$podcast_filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


			$podcast_filetype = $fileData[0];

			
			if ($fileExtension==$podcast_filetype AND !publishInFuture($filefullpath)) { // if the extension is accepted

					$filedescr = $absoluteurl.$upload_dir.$filenameWithoutExtension.'.xml'; //database file


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
						
						
						$episodeDate = date ($dateformat, filemtime($filefullpath));
						
				if (!$disableextras) {
					# Use GETID3 lib to retrieve media file duration, bitrate, etc...
					$episode_details = retrieveMediaFileDetails ("$absoluteurl"."$upload_dir$filenameWithoutExtension.$podcast_filetype",$podcast_filetype,$getID3);
				}

	
//////////////////////////////////////////////////////////////////////////
//CONSTRUCT EPISODE OUTPUT!! (here <2.0 themes compatibility is preserved)

$numberOfEpisodesPerLine = 2; //number of episodes per line in some themes - defined in $numberOfEpisodesPerLine


if (useNewThemeEngine($theme_path)) { //If use new theme template
	
	//NB $resulting_episodes is not appended to the previous. it will be appended to $finalOutputEpisodes


		//just if the episod number is multiple of $numberOfEpisodesPerLine
		if ($recent_count % $numberOfEpisodesPerLine != 0 OR $recent_count == count($fileNamesList)) {
		//open div with class row-fluid (theme based on bootstrap)
	
		$resulting_episodes .= '<div class="row-fluid">'; // row-fluid is a line that contains 1 or more episodes
		}

	$resulting_episodes .= '<div class="span6 6u episodebox">'; //open the single episode DIV
}

else { //if an old theme is used
	
	//NB $resulting_episodes is not appended to the previous. it will be appended to $finalOutputEpisodes

	$resulting_episodes .= '<div class="episode">'; //open the single episode DIV
} 
	
	
	$isvideo = isItAvideo($podcast_filetype);
							

	$resulting_episodes .= '<h3 class="episode_title">'.$text_title;
	if ($isvideo == TRUE) $resulting_episodes .= '&nbsp;<i class="fa fa-youtube-play"></i>';


$resulting_episodes .= '</h3>';
	

// EPISODE DATE AND SIZE
$resulting_episodes .= '<p class="episode_date">';

if (filemtime($filefullpath) > time()) {
$resulting_episodes .= '<i class="fa fa-clock-o fa-2x"></i>&nbsp;&nbsp;';	
}
$resulting_episodes .= $episodeDate.'</p>';



///////////////////////////////////////////
//EDIT DELETE BUTTON (JUST IF LOGGED IN)

// IF USER IS LOGGED AND PAGE IS ALL PODCAST
if (!isset($_REQUEST['amilogged']) AND isset($_SESSION["user_session"]) AND isset($_GET["cat"]) AND ($_GET["cat"]) == "all") { 

	$resulting_episodes .= '<p><a class="btn btn-inverse btn-mini" href="?p=admin&amp;do=edit&amp;=episode&amp;name='.$filenameWithoutExtension.'.'.$podcast_filetype.'">'._("Edit / Delete").'</a></p>';

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

$resulting_episodes .= showButtons($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,$recent_count);

						#END BUTTONS
		
						
						
					//EPISODE DURATION, FILETYPE AND OTHER DETAILS IS AVAILABLE
if (isset($episode_details)) {
$resulting_episodes .= '<p class="episode_info">'.$episode_details.'</p>';
}
			

//PLAYER AUDIO (FLASH/HTML5) AND VIDEO (HTML5) FOR SUPPORTED FILES AND BROWSERS

if ($enablestreaming=="yes" AND !detectMobileDevice()) { //if audio and video streaming is enabled in PG options
	
$resulting_episodes .= showStreamingPlayers ($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,$recent_count);

} //END if audio and video streaming is enabled in PG options

	//PUT ISVIDEO TO FALSE AGAIN	
	$isvideo = FALSE; //so variable is assigned on every cicle
					
				//add social networks and embedded code
				include("$absoluteurl"."core/attachtoepisode.php");	
					

					
					
					//Blank space
					$resulting_episodes .= "<br />";
					

						$resulting_episodes .= "</div>";

							
						//close line with one or more episode for new themes >=2.0
						if (useNewThemeEngine($theme_path) AND $recent_count % $numberOfEpisodesPerLine != 0 OR $recent_count == count($fileNamesList)) { 
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
	if (isset($recent_count)) $numberOfPages = ($recent_count / $episodeperpage);
	if (isset($numberOfPages) AND $numberOfPages>1) $numberOfPages = ceil($numberOfPages); //round to the next integer
	
	//echo $numberOfPages;
	
	if (isset($_GET['p'])) $pageURLforPagination = avoidXSS(($_GET['p']));
	else $pageURLforPagination = "home";
	
	if  (isset($_GET["pgn"])) $thisCurrentPage = $_GET["pgn"];
	else $thisCurrentPage = 1;
	
	if (isset($recent_count) AND $recent_count > $episodeperpage) {
		
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
	
	
	$singleFileName = $singleEpisode;
		

		$file_parts = divideFilenameFromExtension($singleFileName); //supports more full stops . in the file name. PHP >= 5.2.0 needed
		$filenameWithoutExtension = $file_parts[0];
		$fileExtension = $file_parts[1];
	
		
		$fileData = checkFileType($fileExtension,$podcast_filetypes,$podcast_filemimetypes);


			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


			$podcast_filetype = $fileData[0];


			$filefullpath = $absoluteurl.$upload_dir.$singleFileName;
			
			if ($fileExtension==$podcast_filetype AND !publishInFuture($filefullpath) AND file_exists($absoluteurl."$upload_dir$filenameWithoutExtension.$podcast_filetype")) { // if the extension is accepted AND the file EXISTS
		
					//TIMESTAMP
					$file_timestamp = filemtime($absoluteurl."$upload_dir$filenameWithoutExtension.$podcast_filetype");
					
					
					$filedescr = $absoluteurl.$upload_dir.$filenameWithoutExtension.'.xml'; //database file


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

						$episodeDate = date ($dateformat,$file_timestamp);
						

						
						# Use GETID3 lib to retrieve media file duration, bitrate, etc...
					$episode_details = retrieveMediaFileDetails ("$absoluteurl"."$upload_dir$filenameWithoutExtension.$podcast_filetype",$podcast_filetype,$getID3);

	
//////////////////////////////////////////////////////////////////////////
//CONSTRUCT EPISODE OUTPUT!! (here <2.0 themes compatibility is preserved)

$numberOfEpisodesPerLine = 2; //number of episodes per line in some themes - defined in $numberOfEpisodesPerLine


if (useNewThemeEngine($theme_path)) { //If use new theme template


	$resulting_episodes .= '<div class="span episodebox">'; //open the single episode BOX
	$resulting_episodes .= '<div class="span6">'; //open the single episode DIV
}

else { //if an old theme is used
	$resulting_episodes .= '<div class="episode">'; //open the single episode DIV
} 
	
							

	$resulting_episodes .= '<h3 class="episode_title">'.$text_title;

	if (isItAvideo($podcast_filetype) == TRUE) $resulting_episodes .= '&nbsp;<i class="fa fa-youtube-play"></i>';


$resulting_episodes .= '</h3>';
	

// EPISODE DATE AND SIZE
$resulting_episodes .= '<p class="episode_date">';
if (filemtime($filefullpath) > time()) {
$resulting_episodes .= '<i class="fa fa-clock-o fa-2x"></i>&nbsp;&nbsp;';	
}
$resulting_episodes .= $episodeDate.'</p>';



///////////////////////////////////////////
//EDIT DELETE BUTTON (JUST IF LOGGED IN)

// IF USER IS LOGGED AND PAGE IS ALL PODCAST
if (!isset($_REQUEST['amilogged']) AND isset($_SESSION["user_session"]) AND isset($_GET["name"])) { 


		$resulting_episodes .= '<p><a class="btn btn-inverse btn-mini" href="?p=admin&amp;do=edit&amp;=episode&amp;name='.$filenameWithoutExtension.'.'.$podcast_filetype.'">'._("Edit / Delete").'</a></p>';

}
		
						

						$resulting_episodes .= '<p>'.$text_longdesc.'</p>';	 //SHOW short description (no HTML)
						
						$resulting_episodes .= '<p><em>'._("Categories").'</em> ';	
						
						if ($text_category1 != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$text_category1.'">'.categoryNameFromID($absoluteurl,$text_category1).'</a>';
						if ($text_category2 != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$text_category2.'">'.categoryNameFromID($absoluteurl,$text_category2).'</a>';
						if ($text_category3 != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$text_category3.'">'.categoryNameFromID($absoluteurl,$text_category3).'</a>';
						
						$resulting_episodes .= '</p>';
						
						
						
						#BUTTONS
	$resulting_episodes .= showButtons($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,"singleEpisode"); //NB recent count is passed as non numeric so no button More is showed
						#END BUTTONS
		
						
						
					//EPISODE DURATION, FILETYPE AND OTHER DETAILS IS AVAILABLE
if (isset($episode_details)) {
$resulting_episodes .= '<p class="episode_info">'.$episode_details.'</p>';
}
						
if ($enablestreaming=="yes" AND !detectMobileDevice()) { 
$resulting_episodes .= showStreamingPlayers ($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,"singleEpisode"); //NB recent count is passed as non numeric so no button More is showed
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



//generate language ISO639 format (e.g. just "en" and not "en_EN") for RSS feed
function languageISO639 ($language) {
$parts = explode("_", $language);
return $parts[0];
}

function categoryNameFromID ($absoluteurl,$id) {
include ("$absoluteurl"."core/admin/readXMLcategories.php");
	if (in_array($id, $arrid)) {
	$positionInArray = array_search($id, $arrid);
	$catName = $arr[$positionInArray];
	return $catName;
	} else {
	return;
	}
	
}


function languagesList ($absoluteurl,$isTranslation) { // $isTranslation TRUE when the list is used for localized files

	if (file_exists($absoluteurl."components/locale/languages.xml")) {
	
	$parser = simplexml_load_file($absoluteurl.'components/locale/languages.xml','SimpleXMLElement',LIBXML_NOCDATA);
	
	}

	$languageList = array (); //empty
	
	$n = 0;
	foreach($parser->language as $singlelanguage)
		{

			//echo $singlelanguage->id[0]."<br>";
			//echo $singlelanguage->description[0];

			$id = $singlelanguage->id[0];
			$desc = $singlelanguage->description[0];
		
		if ($isTranslation == TRUE) {
		//if localization folder exists
		
		$localizedFolder = $absoluteurl."components/locale/".$id."/LC_MESSAGES/";
		
				if (file_exists($localizedFolder."messages.mo") AND file_exists($localizedFolder."messages.po")) { 
				$languageList["$id"] = "$desc"; //double quotes necessary
			}
		} else {
		//add all to the list
		$languageList["$id"] = "$desc"; //double quotes necessary
		}
		
		$n++;
		}
	
	return $languageList;
}


function random_str($size) 
{ 
	$text = NULL;
        $randoms = array( 
                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", 
                "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", 
                "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z" 
        ); 

        srand ((double) microtime() * 1000000); 

        for($i = 1; $i <= $size; $i++) 
            $text .= $randoms[(rand(0,35))]; 

        return $text; 
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

//detect browser name and version from user agent (until get_browser will come in bundle with PHP)
function browserAndVersion() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
   
   
//The following conditions are custom-tailored for specific user agents and may change in future
   
   //Opera (NB. It goes before Chrome, cause the user agent contains chrome too
   if(preg_match('/(opr)[ \/]([\w.]+)/', $userAgent)) {
   $browser = 'opera';
   preg_match('/(opr)[ \/]([\w]+)/', $userAgent, $versionExtracted); //used (opr) in regexp
   }
   //Chrome
   else if(preg_match('/(chrome)[ \/]([\w.]+)/', $userAgent)) {
   $browser = 'chrome';
   preg_match('/('.$browser.')[ \/]([\w]+)/', $userAgent, $versionExtracted);
   }
   //Firefox
   else if(preg_match('/(firefox)[ \/]([\w.]+)/', $userAgent)) {
   $browser = 'firefox';
   preg_match('/('.$browser.')[ \/]([\w]+)/', $userAgent, $versionExtracted);
   }
   //IE
   else if(preg_match('/(msie)[ \/]([\w.]+)/', $userAgent)) {
   $browser = 'msie';
   preg_match('/('.$browser.')[ \/]([\w]+)/', $userAgent, $versionExtracted);
   }
   //IE 11
   else if(preg_match('/(trident)[ \/]([\w.]+)/', $userAgent)) {
   $browser = 'msie';
	$version = "11"; //manually assigned to IE 11 
	}
   //SAFARI
   else if(preg_match('/(safari)[ \/]([\w.]+)/', $userAgent)) {
   $browser = 'safari';
   preg_match('/(version)[ \/]([\w]+)/', $userAgent, $versionExtracted); //version in the regexp
   }
   else {
   $browser = "notDetected";
   $version = "0";
   }
   
   if (!isset($version)) $version = $versionExtracted[2];
   
/*
   if(preg_match('/(chromium)[ \/]([\w.]+)/', $userAgent))
            $browser = 'chromium';
    elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $userAgent))
            $browser = 'chrome';
    elseif(preg_match('/(safari)[ \/]([\w.]+)/', $userAgent))
            $browser = 'safari';
    elseif(preg_match('/(opera)[ \/]([\w.]+)/', $userAgent))
            $browser = 'opera';
    elseif(preg_match('/(msie)[ \/]([\w.]+)/', $userAgent))
            $browser = 'msie';
    elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $userAgent))
            $browser = 'mozilla';
	elseif(preg_match('/(opr)[ \/]([\w.]+)/', $userAgent))
            $browser = 'opera';

    
*/
	
	// DEBUG!
	//echo $userAgent."<br />";
	//echo $browser." v.".$version."<br />";
	
    return array($browser,$version, 'name'=>$browser,'version'=>$version);
}


//Function detectModernBrowser detects modern browsers to enable HTML5 audio/video players
//It compares user agent and versions agains a list of browsers known to support html5 audio/video tags
function detectModernBrowser() 
{
	
	$supportHTML5audioTag = array("msie","firefox","chrome","safari");
	$supportHTML5videoTag = array("msie","chrome","safari"); //firefox does not fully support mp4 videos yet
	
	//for each browser, which is the minimum required version that supports HTML5 audio and video tags
	$minumumRequiredVersion = array(
    "msie" => 9,
    "firefox" => 29,
	"safari" => 5, 
	"chrome" => 36,
);
	
	$browser = browserAndVersion();
	$browsername = $browser[0];
	$browserMajorVersion = $browser[1];
	
	
	$HTML5audiosupport = FALSE;
	if (in_array($browser[0], $supportHTML5audioTag)) {  
	//echo "<br/>Browser: ".$browsername." - Version: ".$browserMajorVersion." supports HTML5 audio tag";
		if ($browserMajorVersion >= $minumumRequiredVersion[$browsername]) $HTML5audiosupport = TRUE;
		//echo $HTML5audiosupport;
	
	}
	
	$HTML5videosupport = FALSE;
	if (in_array($browser[0], $supportHTML5videoTag)) {  
	//echo "<br/>Browser: ".$browsername." - Version: ".$browserMajorVersion." supports HTML5 video tag";  
		if ($browserMajorVersion >= $minumumRequiredVersion[$browsername]) $HTML5videosupport = TRUE;
		//echo $HTML5audiosupport;
	
	}
	
	$browserCompatibility = array($HTML5audiosupport,$HTML5videosupport);
	//print_r($browserCompatibility);
	
	return $browserCompatibility;
}


function isItAvideo($podcast_filetype) {
	$listOfVideoFormats = array("mpg","mpeg","mov","mp4","wmv","3gp","avi","flv","m4v");
	if (in_array($podcast_filetype, $listOfVideoFormats)) { // if it is a video
	return TRUE; 
	}
}


function showButtons($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,$recent_count) {
	$buttonsOutput = '<p>';
	
	//show button "More" - in the permalink it is not show (no numeric var passed)
	if (is_numeric($recent_count)) $buttonsOutput .= '<a class="btn" href="?name='.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-search"></i> '._("More").'</a>&nbsp;&nbsp;';
	
	$browserAudioVideoSupport = detectModernBrowser();
	if (isItAvideo($podcast_filetype) == TRUE AND $browserAudioVideoSupport[1] == TRUE AND !detectMobileDevice()) {
	//javascript:; is added as an empty link for href
	$buttonsOutput .= '<a href="javascript:;" class="btn"  onclick="$(\'#videoPlayer'.$recent_count.'\').fadeToggle();$(this).css(\'font-weight\',\'bold\');"><i class="fa fa-youtube-play"></i> '._("Watch").'</a>&nbsp;&nbsp;';
	}
	//videoPlayer.'.$recent_count.'
	
	## BUTTON DOWNLOAD
	//iOS device has been reported having some trouble downloading episode using the "download.php" forced download...
	if (!detectMobileDevice()) { //IF IS NOT MOBILE DEVICE
	//show button (FORCED) download using download.php
	$buttonsOutput .= '<a class="btn" href="'.$url.'download.php?filename='.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-download"></i> '._("Download").'</a>';
	} 
	else { // SHOW BUTTON DOWNLOAD THAT links directly to the file (so no problems with PHP forcing headers)
	//Write "watch" or "listen" in mobile devices.
		if (isItAvideo($podcast_filetype) == TRUE) { 
			$buttonsOutput .= '<a class="btn" 			href="'.$url.$upload_dir.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-youtube-play"></i> 	'._("Watch").'</a>';
	}	
		//if it's audio
		else if ($podcast_filetype=="mp3" OR $podcast_filetype=="m4a"){
		$buttonsOutput .= '<a class="btn" 			href="'.$url.$upload_dir.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-play"></i> 	'._("Listen").'</a>';
	}	else {
			$buttonsOutput .= '<a class="btn" 			href="'.$url.$upload_dir.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-download"></i> 	'._("Download").'</a>';
	}
		

	}
	## END - BUTTON DOWNLOAD
	
	$buttonsOutput .= '</p>';

return $buttonsOutput;
}


function showStreamingPlayers($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,$recent_count) {
	
	$playersOutput = "";
	
	$browserAudioVideoSupport = detectModernBrowser();
	
	//// AUDIO PLAYER (MP3)
		if ($browserAudioVideoSupport[0] == TRUE AND $podcast_filetype=="mp3") { //if browser supports HTML5
		$showplayercode =	'<audio style="width:80%;" controls>
			  <source src="'.$url.$upload_dir.$filenameWithoutExtension.'.mp3" type="audio/mpeg">
			'._("Your browser does not support the audio player").'
			</audio>';
			$playersOutput .= ''.$showplayercode.'<br />'; 

		} else { //if no support for HTML5, then flash player just for mp3
			if($podcast_filetype=="mp3") { //if not mobile
			include ("components/player/player.php");
			$playersOutput .= ''.$showplayercode.'<br />'; 
			}
		}
	//// END AUDIO PLAYER (MP3)


	//// VIDEO PLAYER (MP4)	

		// If the file is a video and the browser supports HTML5 video tag
		if (isItAvideo($podcast_filetype) == TRUE AND $browserAudioVideoSupport[1] == TRUE) {

		$playersOutput .= '<video width="85%" id="videoPlayer'.$recent_count.'" style="display:none;" controls>
		  <source src="'.$url.$upload_dir.$filenameWithoutExtension.'.'.$podcast_filetype.'" type="video/mp4">
		'._("Your browser does not support the video player").'
		</video>';

		$playersOutput .= '<br />';
		}
		
		return $playersOutput;
		
}



function retrieveMediaFileDetails ($MediaFile,$podcast_filetype,$getID3) {
	
	$ThisFileSize = round(filesize($MediaFile)/1048576,2);
	
	$ThisFileInfo = $getID3->analyze($MediaFile); //read file tags
	$file_duration = @$ThisFileInfo['playtime_string'];
	$file_type = @$ThisFileInfo['fileformat'];
	
	$episode_details = NULL;
	
	if ($file_type != NULL) $episode_details .= _('Filetype:')." ".strtoupper($file_type)." - ";
	$episode_details .= _('Size:')." ".$ThisFileSize._("MB");
	
	if($file_duration!=NULL) { // display file duration
		$episode_details .= " - "._("Duration:")." ";
		$episode_details .= @$ThisFileInfo['playtime_string'];
		$episode_details .= " "._("m");
		
		if($podcast_filetype=="mp3") { //if mp3 show bitrate &co
			$episode_details .= " (";
			$episode_details .= @$ThisFileInfo['bitrate']/1000;
			$episode_details .= " "._("kbps")." ";
			$episode_details .= @$ThisFileInfo['audio']['sample_rate'] ;
			$episode_details .= " "._("Hz").")";
		}

	} 
	
	return $episode_details;
	
}



function readMediaDir ($absoluteurl,$upload_dir) {
	
	//List of directories or files to exclude
	$toExclude = array("..",".","index.htm","_vti_cnf",".DS_Store",".svn");
	
	$handle = opendir ($absoluteurl.$upload_dir);
	while (($filename = readdir ($handle)) !== false) 
	{
		if (!in_array($filename, $toExclude)) $files_array[$filename] = filemtime ($absoluteurl.$upload_dir.$filename);
	}
	
	//$files_array has the file name as key and the file date as value - here we sort inversely by time 
	arsort ($files_array); //opposite of asort
	
	//Now that the array as been ordered by date (most recent on top), we can keep just keys (file names)
	$fileNamesList = array_keys($files_array);

	//array containing all the accepted files names in media folder, with the exception of $toExclude
	return $fileNamesList; 
}




//GeneratePodcastFeed $outputInFile TRUE writes to a file (feed.xml), FALSE prints on screen
function generatePodcastFeed ($outputInFile) {
	
	//include functions and variables in config.php
	include("core/includes.php"); 
	

	# SET CUSTOM WEB URL (shown in iTunes Store), if specified in config.php
	if (isset($feed_iTunes_LINKS_Website) AND $feed_iTunes_LINKS_Website != NULL) {
	$podcastWebHomePage = $feed_iTunes_LINKS_Website; } 
	else { $podcastWebHomePage = $url; }
	

	### DEFINE FEED FILENAME
	$feedfilename = $absoluteurl.$feed_dir."feed.xml";

	//rewrite the language var to adhere to ISO639
	$feed_language = languageISO639($feed_language);
	

	##### CONTENT DEPURATION n.1
	#Depurate feed content according to iTunes specifications
	$itunes_category[0] = depurateContent($itunes_category[0]);
	$itunes_category[1] = depurateContent($itunes_category[1]);
	$itunes_category[2] = depurateContent($itunes_category[2]);


	//RSS FEED HEADER
	$head_feed = 
	'<?xml version="1.0" encoding="'.$feed_encoding.'"?>
	<!-- generator="Podcast Generator '.$podcastgen_version.'" -->
	<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xml:lang="'.$feed_language.'" version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>'.$podcast_title.'</title>
		<link>'.$podcastWebHomePage.'</link>
		<atom:link href="'.$url.$feed_dir.'feed.xml" rel="self" type="application/rss+xml" />
		<description>'.$podcast_description.'</description>
		<generator>Podcast Generator '.$podcastgen_version.' - http://podcastgen.sourceforge.net</generator>
	<lastBuildDate>'.date("r").'</lastBuildDate>
		<language>'.$feed_language.'</language>
		<copyright>'.$copyright.'</copyright>
		<itunes:image href="'.$url.$img_dir.'itunes_image.jpg" />
		<image>
		<url>'.$url.$img_dir.'itunes_image.jpg</url>
		<title>'.$podcast_title.'</title>
		<link>'.$url.'</link>
		</image>
		<itunes:summary>'.$podcast_description.'</itunes:summary>
		<itunes:subtitle>'.$podcast_subtitle.'</itunes:subtitle>
		<itunes:author>'.$author_name.'</itunes:author>
		<itunes:owner>
		<itunes:name>'.$author_name.'</itunes:name>
		<itunes:email>'.$author_email.'</itunes:email>
		</itunes:owner>
		<itunes:explicit>'.$explicit_podcast.'</itunes:explicit>';

	####
	# iTunes categories (and subcategories, which are separated by :)
	//category 1
	if ($itunes_category[0]!=NULL) { 
		$tmpcat = explode(":",$itunes_category[0]);
		$head_feed.= '
		<itunes:category text="'.$tmpcat[0].'">';
		//Sub Category
		if (isset($tmpcat[1]) AND $tmpcat[1]!=NULL) $head_feed.= '<itunes:category text="'.$tmpcat[1].'" />';
		$head_feed.= '</itunes:category>
		';
	} //end category 1
	//category 2
	if ($itunes_category[1]!=NULL) { 
		$tmpcat = explode(":",$itunes_category[1]);
		$head_feed.= '<itunes:category text="'.$tmpcat[0].'">';
		//Sub Category
		if (isset($tmpcat[1]) AND $tmpcat[1]!=NULL) $head_feed.= '<itunes:category text="'.$tmpcat[1].'" />';
		$head_feed.= '</itunes:category>
		';
	} //end category 2
	//category 3
	if ($itunes_category[2]!=NULL) { 
		$tmpcat =explode(":",$itunes_category[2]);
		$head_feed.= '<itunes:category text="'.$tmpcat[0].'">';
		//Sub Category
		if (isset($tmpcat[1]) AND $tmpcat[1]!=NULL) $head_feed.= '<itunes:category text="'.$tmpcat[1].'" />';
		$head_feed.= '</itunes:category>
		';
	} //end category 3
	# END iTunes Categories

	####
	# List all the items (i.e. podcast episodes)

	// Open podcast directory
	$fileNamesList = readMediaDir ($absoluteurl,$upload_dir);

	if (!empty($fileNamesList)) { //if directory is not empty

		$recent_count = 0; //set counter
		$episodes_feed = NULL; //define and empty variable

	//Go through episodes and index them as items of the RSS feed
		foreach ($fileNamesList as $singleFileName) {

			//Limit episodes in the feed (from config.php)
			if ($recent_count < $recent_episode_in_feed OR $recent_episode_in_feed == "All") { 
			
				$file_multimediale = explode(".",$singleFileName); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)


				$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$podcast_filemimetypes); 


				if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]


					$podcast_filetype = $fileData[0];

					###### Mimetype
					$filemimetype=$fileData[1]; //define mimetype to put in the feed

					$filefullpath = $absoluteurl.$upload_dir.$singleFileName;
					
					
					if ($file_multimediale[1]==$podcast_filetype AND !publishInFuture($filefullpath)) { // if the extension is the same as specified in config.php




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
							# $text_categoriespg = categories
							# $text_keywordspg = keywords
							# $text_explicitpg = explicit podcast (yes or no)
							# $text_authornamepg = author's name
							# $text_authoremailpg = author's email


							//depuration of long description field, appearing in iTunes when you click the "circled i" next to the podcast description


							$text_longdesc = stripslashes($text_longdesc);
							$text_longdesc = strip_tags($text_longdesc);



							#### CONTENT DEPURATION N.2
							$text_title = depurateContent($text_title); //title
							$text_shortdesc = depurateContent($text_shortdesc); //short desc
							$text_longdesc = depurateCDATAfield($text_longdesc); //long desc
							$text_keywordspg = depurateContent($text_keywordspg); //Keywords
							$text_keywordspg = htmlspecialchars($text_keywordspg); //convert special characters e.g. r&b -> r&amp;b
							$text_authornamepg = depurateContent($text_authornamepg); //author's name

							$file_size = filesize("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype");
							$filetime = filemtime ("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype");
							$filepubdate = date ('r', $filetime);


							$episodes_feed.="<item>
								<title>$text_title</title>
								<itunes:subtitle>$text_shortdesc</itunes:subtitle>
								<itunes:summary><![CDATA[ $text_longdesc ]]></itunes:summary>
								<description>$text_shortdesc</description>
								<link>$link$singleFileName</link>
								<enclosure url=\"$url$upload_dir$singleFileName\" length=\"$file_size\" type=\"$filemimetype\"/>
								<guid>$link$singleFileName</guid>
								";



							###### GETID3 - DURATION
							require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)
							$getID3 = new getID3; //initialize getID3 engine

							# File details (duration, bitrate, etc...)
							$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype"); //read file tags

							$file_duration = @$ThisFileInfo['playtime_string'];

							if($file_duration!=NULL) { // display file duration
								$episodes_feed.= "<itunes:duration>$file_duration</itunes:duration>
									";
							} 


							### AUTHOR
							if ($text_authornamepg==NULL OR $text_authornamepg==",") { //if author field is empty

								$episodes_feed.= "<author>$author_email ($author_name)</author>
									<itunes:author>$author_name</itunes:author>
									";

							} 

							else { //if author is present

								$episodes_feed.= "<author>$text_authoremailpg ($text_authornamepg)</author>
									<itunes:author>$text_authornamepg</itunes:author>
									";
							}


							## KEYWORDS
							if ($text_keywordspg!=NULL) { //if keywords are present

								$episodes_feed.= "<itunes:keywords>$text_keywordspg</itunes:keywords>";

							} 

							if ($text_explicitpg!=NULL) {
								$episodes_feed.= "<itunes:explicit>$text_explicitpg</itunes:explicit>
									";
							}


							$episodes_feed.= "<pubDate>$filepubdate</pubDate>
								</item>";


							$recent_count++; // increment recent counter


						} 

					} 

				}
			}
		}

	}






	#########
	##########coda


	$tail_feed ="</channel></rss>";
	
	if (!isset($episodes_feed)) $episodes_feed = ""; // avoid notice
	####
	$fp1 = fopen("$feedfilename", "w+"); //Apri il file in lettura e svuotalo (w+)
	fclose($fp1);

	$fp = fopen("$feedfilename", "a+"); //testa xml
	fwrite($fp, "$head_feed"."$episodes_feed"."$tail_feed"); 
	fclose($fp);

	############
	
	}




	
function validateSingleEpisode ($episodeFile) {
//include functions and variables in config.php

	include("core/includes.php");

	$episodeFile_parts = divideFilenameFromExtension($singleFileName); // PHP >= 5.2.0 needed
	$episodeFilenameWithoutExtension = $file_parts[0];
	$EpisodeFileExtension = $file_parts[1];
	
	$fileData = checkFileType($EpisodeFileExtension,$podcast_filetypes,$podcast_filemimetypes);

	$filefullpath = $absoluteurl.$upload_dir.$singleFileName;
	$filedescr = $absoluteurl.$upload_dir.$episodeFilenameWithoutExtension.'.xml'; //database file
	
		// $fileData[0] is one of the supported extensions in $podcast_filetypes
		if (isset($fileData[0]) AND $EpisodeFileExtension==$fileData[0] AND !publishInFuture($filefullpath) AND file_exists($filedescr)) { 

		$GoForIt = TRUE;	
		}
		
		else {
		$GoForIt = FALSE;
		}

	//NB. $GoForIt = TRUE means that the episode file format is supported, it has a corresponding data file (xml)
	
	return array($GoForIt,$filefullpath);

		
}



//DETECT WHETHER USER IS LOGGED-IN OR NOT
function isUserLogged () {
	//read user and md5 pwd from config.php
	if (file_exists("config.php")) include("config.php"); 
	//compare sessions to stored user and md5 pwd
	if(isset($_SESSION["user_session"]) AND $_SESSION["user_session"]==$username AND md5($_SESSION["password_session"])==$userpassword) { return TRUE; }
	else { return FALSE; }
}


// Is the episode set to a future date?
function publishInFuture($filefullpath) {	
	$fileTime = 0;
	if (file_exists($filefullpath)) $fileTime = filemtime($filefullpath);
	if ($fileTime > time()) return TRUE;
	else return FALSE;
}




?>