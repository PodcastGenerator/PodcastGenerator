<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

##################################################################################

function checkFileType ($filetype,$absoluteurl) {
	
	if (file_exists($absoluteurl."components/supported_media/supported_media.xml")) {
		
		$parser = simplexml_load_file($absoluteurl.'components/supported_media/supported_media.xml','SimpleXMLElement',LIBXML_NOCDATA);
		
		$podcast_filetypes = array();
		$podcast_filemimetypes = array();
		foreach($parser->mediaFile as $singleFileType) {
				array_push ($podcast_filetypes,$singleFileType->extension[0]);
				array_push ($podcast_filemimetypes,$singleFileType->mimetype[0]);
		}
		
		$i=0;
		$isFileSupported=FALSE;
		$fileData = array();	
		while (($i < sizeof($podcast_filetypes)) && $isFileSupported==false) {
			if ($filetype==$podcast_filetypes[$i]) {
				$fileData[0]=$podcast_filetypes[$i];
				$fileData[1]=$podcast_filemimetypes[$i];
				$isFileSupported=TRUE;
			}
			$i++;
		}
		
		
		//Always return something
		if (!isset($fileData[0])) $fileData[0] = NULL;
		if (!isset($fileData[1])) $fileData[1] = NULL;
		
		//returns bool (true if file is present in supported_media.xml)
		$fileData[2] = $isFileSupported;
		
		//Array with 3 values: podcast_filetypes, podcast_filemimetypes, isFileSupported
		return ($fileData);
	}
}
## END - DETECT SUPPORTED MEDIA FILE FORMATS AND RETURN MIMETYPE
##################################################################################





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
	$outputform .=  "0".intval($currentMinute); } //add 0 to numbers from 1 to 9
	else { $outputform .=  intval($currentMinute); }
	
	$outputform .=  "\"";
	if(intval(date( "i", $useDate))==$currentMinute)
	{
	$outputform .=  " selected";
	}
	
	if ($currentMinute <= 9) {
	$outputform .=  ">0".intval($currentMinute). "\n"; } //add 0 to numbers from 1 to 9
	else { $outputform .=  ">".intval($currentMinute). "\n"; }
	
	}
	$outputform .=  "</select>";

	
	return $outputform;

} // End - form date and time


##################################################################################
##################################################################################
## SOCIAL NETWORK INTEGRATION

//$fullURL,$text_title are episode data. the rest: value 1 (TRUE) enable a certain social network, value 0 disables
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
## END SOCIAL NETWORK INTEGRATION
##################################################################################


##################################################################################
##################################################################################
## SHOW PODCAST EPISODES

//NB $all is bool (FALSE = takes $max_recent from config.php) $category is the category name from GET (null = all categories)
function showPodcastEpisodes($all,$category) { 

	include("core/includes.php");
	$finalOutputEpisodes = NULL; // declare final output to return
	
	if ($all == TRUE) {
		$max_recent = NULL; //reset limitation for recent episodes set in config.php
		$categoryURLforPagination = "&cat=all"; //preserve category in links in number of pages at the button

			//don't show social networks when noextras is appended to the URL
			if (isset($_GET['noextras'])) {
				$disableextras = TRUE;
				$categoryURLforPagination .= "&noextras"; //preserve category in links in number of pages at the button
			}

	} 
	else { // in home page, do not paginate but use $max_recent 
		$episodeperpage = 999999; //do not use pagination (workaround - could be more elegant)
	}


	/// Header for Category (RSS and Title)
	if (isset($category) AND $category != NULL) {

		$CounterEpisodesInCategory = 0; // set counter to 0
		$category = avoidXSS($category); //URL depuration
		$categoryURLforPagination = "&cat=".$category;

		//retrieve existing categories (description/long name)
		//NB $existingCategories[$category] is category full name (not just ID)
		$existingCategories = readPodcastCategories ($absoluteurl); 

		$category_header = '<div>';
			if (isset($existingCategories[$category])) {
			$category_header .= '<h3 class="sectionTitle"><a href="'.$url.'feed.php?cat='.$category.'"><i class="fa fa-rss "></i> '.$existingCategories[$category].'</a></h3>';
			}
		$category_header .= '</div>';
	}

	// Open podcast directory and read all the files contained
	$fileNamesList = readMediaDir ($absoluteurl,$upload_dir);


	if (empty($fileNamesList)) { // If media directory is empty
		$finalOutputEpisodes .= '<div class="topseparator"><p>'._("No episodes here yet...").'</p></div>';
	} 
	else { // If media directory contains files

		$episodesCounter = 0; //set counter to zero

		//if isset pagination variable in GET
		if (isset($_GET["pgn"]) AND is_numeric($_GET["pgn"])) {
			$maxC = $episodeperpage * $_GET["pgn"];
			$minC = $episodeperpage* $_GET["pgn"] - $episodeperpage;
		} 
		//if home page or no pages are set in GET 
		else {
			$maxC = $episodeperpage;
			$minC = 0;
		}

		// Loop through each file in the media directory
		foreach ($fileNamesList as $singleFileName) {

			$resulting_episodes = NULL; //declare the 1st time and then reset

			//If current episode won't be displayed in this page, skip it and break the loop
			if ($episodesCounter > $maxC) {
				//NB. count($fileNamesList)/2 is the total number of episodes
				$episodesCounter = count($fileNamesList)/2; 
				break;
			}
			//Else if this episode is shown in this page, or no limitation in $max_recent (i.e. home page)
			else if ($episodesCounter < $max_recent OR $max_recent == NULL) { 

				////Validate the current episode
				//NB. validateSingleEpisode returns [0] episode is supported (bool), [1] Episode Absolute path, [2] Episode XML DB absolute path,[3] File Extension (Type), [4] File MimeType, [5] File name without extension, [6] episode file supported but to XML present
				$thisPodcastEpisode = validateSingleEpisode($singleFileName);

			
				////If episode is supported and has a related xml db, and if it's not set to a future date OR if it's set for a future date but you are logged in as admin
				if (($thisPodcastEpisode[0]==TRUE AND !publishInFuture($thisPodcastEpisode[1])) OR ($thisPodcastEpisode[0]==TRUE AND publishInFuture($thisPodcastEpisode[1]) AND isUserLogged())) { 

					////Parse XML data related to the episode 
					// NB. Function parseXMLepisodeData returns: [0] episode title, [1] short description, [2] long description, [3] image associated, [4] iTunes keywords, [5] Explicit language,[6] Author's name,[7] Author's email,[8] PG category 1, [9] PG category 2, [10] PG category 3, [11] file_info_size, [12] file_info_duration, [13] file_info_bitrate, [14] file_info_frequency, [15] embedded image in mp3
					$thisPodcastEpisodeData = parseXMLepisodeData($thisPodcastEpisode[2]);

					////if category is specified as a parameter of this function
					if (isset($category) AND $category != NULL) { 
						//if category is not associated to the current episode
						if ($category != $thisPodcastEpisodeData[8] AND $category != $thisPodcastEpisodeData[9] AND $category != $thisPodcastEpisodeData[10]) {
							continue; //STOP this cycle and start a new one
						} else {
							$CounterEpisodesInCategory++; // Incremente episodes counter
						}
					}

					//// Start constructing episode HTML output
					
					//Theme engine PG version >= 2.0
					if (useNewThemeEngine($theme_path)) {
						//episodes per line in some themes (e.g. bootstrap)
						$numberOfEpisodesPerLine = 2; 
						//If the current episode number is multiple of $numberOfEpisodesPerLine
						if ($episodesCounter % $numberOfEpisodesPerLine != 0 OR $episodesCounter == count($fileNamesList)) {
							//open div with class row-fluid (theme based on bootstrap)
							//N.B. row-fluid is a CSS class for a div containing 1 or more episodes
							//$resulting_episodes .= '<div class="row-fluid">';
							$resulting_episodes .= '<div class="episode">';
						}
						$resulting_episodes .= '<div class="span6 6u episodebox">'; //open the single episode DIV
					}
					//If old Theme engine for <2.0 themes retro compatibility.
					else { 
						$resulting_episodes .= '<div class="episode">'; //open the single episode DIV
					} 

					////Title
					$resulting_episodes .= '<h3 class="episode_title"><a href="?name='.$thisPodcastEpisode[5].'.'.$thisPodcastEpisode[3].'">'.$thisPodcastEpisodeData[0];
					if (isItAvideo($thisPodcastEpisode[3])) $resulting_episodes .= '&nbsp;<i class="fa fa-youtube-play"></i>'; //add video icon
					$resulting_episodes .= '</a></h3>';

					////Date
					$resulting_episodes .= '<p class="episode_date">';
					$thisEpisodeDate = filemtime($thisPodcastEpisode[1]);
					if ($thisEpisodeDate > time()) { //if future date
					$resulting_episodes .= '<i class="fa fa-clock-o fa-2x"></i>  ';	//show watch icon
					}
					$episodeDate = date ($dateformat, $thisEpisodeDate);
					$resulting_episodes .= $episodeDate.'</p>';

					
					//// Edit/Delete button for logged user (i.e. admin)
					if (isUserLogged()) { 
						$resulting_episodes .= '<p><a class="btn btn-inverse btn-mini" href="?p=admin&amp;do=edit&amp;=episode&amp;name='.$thisPodcastEpisode[5].'.'.$thisPodcastEpisode[3].'">'._("Edit / Delete").'</a></p>';
					}
					
					
							//Show Image embedded in the mp3 file or image associated in the images/ folder from previous versions of PG (i.e. 1.4-) - Just jpg and png extension supported
						if (file_exists($absoluteurl.$img_dir.$thisPodcastEpisode[5].'.jpg')) {
						$resulting_episodes .= '<img class="episode_image" src="'.$url.$img_dir.$thisPodcastEpisode[5].'.jpg" alt="'.$thisPodcastEpisodeData[0].'" />';
						} 
						else if (file_exists($absoluteurl.$img_dir.$thisPodcastEpisode[5].'.png')) {
						$resulting_episodes .= '<img class="episode_image" src="'.$url.$img_dir.$thisPodcastEpisode[5].'.png" alt="'.$thisPodcastEpisodeData[0].'" />';
						}


					//// Short Description
					$resulting_episodes .= '<p>'.$thisPodcastEpisodeData[1].'</p>';


					////Buttons (More, Download, Watch)
					$resulting_episodes .= showButtons($thisPodcastEpisode[5],$thisPodcastEpisode[3],$url,$upload_dir,$episodesCounter,$thisPodcastEpisode[1],$enablestreaming);

					
					////Other details (file type, duration, bitrate, frequency)					
					//NB. read from XML DB (except file extension = $thisPodcastEpisode[3]).
					$episodeDetails = _('Filetype:')." ".strtoupper($thisPodcastEpisode[3]);
					if ($thisPodcastEpisodeData[11] != NULL) $episodeDetails .= ' - '._('Size:')." ".$thisPodcastEpisodeData[11]._("MB");
					
					if($thisPodcastEpisodeData[12]!=NULL) { // display file duration
					$episodeDetails .= " - "._("Duration:")." ".$thisPodcastEpisodeData[12]." "._("m");
					}
					if($thisPodcastEpisode[3]=="mp3" AND $thisPodcastEpisodeData[13] != NULL AND $thisPodcastEpisodeData[14] != NULL) { //if mp3 show bitrate and frequency
						$episodeDetails .= " (".$thisPodcastEpisodeData[13]." "._("kbps")." ".$thisPodcastEpisodeData[14]." "._("Hz").")";
					}
					$resulting_episodes .= '<p class="episode_info">'.$episodeDetails.'</p>';


					////Playes: audio (flash/html5) and video (html5), for supported files and browsers
					//if audio and video streaming is enabled in PG options
					if ($enablestreaming=="yes" AND !detectMobileDevice()) { 
						$resulting_episodes .= showStreamingPlayers ($thisPodcastEpisode[5],$thisPodcastEpisode[3],$url,$upload_dir,$episodesCounter);
					}
					$isvideo = FALSE; //RESET isvideo for next episode

					
					////Social networks and (eventual) embedded code
					$resulting_episodes .= attachToEpisode($thisPodcastEpisode[5],$thisPodcastEpisode[3],$thisPodcastEpisodeData[0]);

					
					//Blank space as bottom margin (to be replaced with CSS style!)
					$resulting_episodes .= "<br />";
					//Close the single episode DIV
					$resulting_episodes .= "</div>";
					//Close div with class row-fluid (theme based on bootstrap). Theme engine >= 2.0
					if (useNewThemeEngine($theme_path) AND $episodesCounter % $numberOfEpisodesPerLine != 0 OR 		$episodesCounter == count($fileNamesList)) { 
						$resulting_episodes .= "</div>"; //close class row-fluid (bootstrap)
					}

					$episodesCounter++; //increment counter
				} // END - If episode is supported and has a related xml db

				
				if ($episodesCounter <= $maxC AND $episodesCounter > $minC) {
					//Append this episode to the final output to return
					$finalOutputEpisodes .= $resulting_episodes;
				}

			} //END - Else if this episode is shown in this page, or no limitation in $max_recent 

		} // END - Loop through each file in the media directory

	} // END - If media directory contains files

	//IF a category is requested add category header and message when empty
	if (isset($category) AND $category != NULL) {
		//If a category is requested and doesn't contain any episode
		if ($CounterEpisodesInCategory < 1 AND !empty($fileNamesList)) {
		$finalOutputEpisodes .= '<p>'.("No episodes here yet...").'</p>';
		}
	$finalOutputEpisodes = $category_header.$finalOutputEpisodes; //category header at the top
	} 


	////Pagination (and links to pages)

	//Calculate total number of pages
	if (isset($episodesCounter)) $numberOfPages = ($episodesCounter / $episodeperpage);
	if (isset($numberOfPages) AND $numberOfPages>1) $numberOfPages = ceil($numberOfPages); //round to the next integer
	//echo $numberOfPages; // Debug

	if (isset($_GET['p'])) $pageURLforPagination = avoidXSS(($_GET['p']));
	else $pageURLforPagination = "home";

	if  (isset($_GET["pgn"])) $thisCurrentPage = $_GET["pgn"];
	else $thisCurrentPage = 1;

	if (isset($episodesCounter) AND $episodesCounter > $episodeperpage) {
		$finalOutputEpisodes .= '<div class="row-fluid" style="clear:both;"><p>';
		
		//Print page index and links
		for ($onePage =1; $onePage <= $numberOfPages; $onePage++) {
			if ($thisCurrentPage == $onePage) {
				$finalOutputEpisodes .= $onePage.' | ';		
			} else
				$finalOutputEpisodes .= '<a href="?p='.$pageURLforPagination.$categoryURLforPagination.'&amp;pgn='.$onePage.'">'.$onePage.'</a> | ';		
			}
		$finalOutputEpisodes .= '</p></div>';
		}

	//Finally, return all the episodes to output on the web page
	return $finalOutputEpisodes;

} 
## END - SHOW PODCAST EPISODES
##################################################################################



##################################################################################
##################################################################################
## SHOW SINGLE EPISODE

// $justTitle is bool and returns just the title of the episode
function showSingleEpisode($singleEpisode,$justTitle) { 

	include("core/includes.php");
	$finalOutputEpisodes = NULL; // declare final output to return

			$resulting_episodes = NULL; //declare the 1st time and then reset

				////Validate the current episode
				//NB. validateSingleEpisode returns [0] episode is supported (bool), [1] Episode Absolute path, [2] Episode XML DB absolute path,[3] File Extension (Type), [4] File MimeType, [5] File name without extension, [6] episode file supported but to XML present
				
				$thisPodcastEpisode = validateSingleEpisode($singleEpisode);

				////If episode is supported and has a related xml db, and if it's not set to a future date OR if it's set for a future date but you are logged in as admin
				if (($thisPodcastEpisode[0]==TRUE AND !publishInFuture($thisPodcastEpisode[1])) OR ($thisPodcastEpisode[0]==TRUE AND publishInFuture($thisPodcastEpisode[1]) AND isUserLogged())) { 

					////Parse XML data related to the episode 
					// NB. Function parseXMLepisodeData returns: [0] episode title, [1] short description, [2] long description, [3] image associated, [4] iTunes keywords, [5] Explicit language,[6] Author's name,[7] Author's email,[8] PG category 1, [9] PG category 2, [10] PG category 3, [11] file_info_size, [12] file_info_duration, [13] file_info_bitrate, [14] file_info_frequency
					$thisPodcastEpisodeData = parseXMLepisodeData($thisPodcastEpisode[2]);

					//// Return just title and end function here, if just title is required
					if (isset($justTitle) AND $justTitle == TRUE) return $thisPodcastEpisodeData[0]; //Function ends here
					
					//// Start constructing episode HTML output
					
					//Theme engine PG version >= 2.0 row-fluid
						$resulting_episodes .= '<div class="episode row-fluid">';
						$resulting_episodes .= '<div class="span6 6u episodebox">'; //open the single episode DIV
			

					////Title
					$resulting_episodes .= '<h3 class="episode_title">'.$thisPodcastEpisodeData[0];
					if (isItAvideo($thisPodcastEpisode[3])) $resulting_episodes .= '&nbsp;<i class="fa fa-youtube-play"></i>'; //add video icon
					$resulting_episodes .= '</h3>';


					////Date
					$resulting_episodes .= '<p class="episode_date">';
					$thisEpisodeDate = filemtime($thisPodcastEpisode[1]);
					if ($thisEpisodeDate > time()) { //if future date
					$resulting_episodes .= '<i class="fa fa-clock-o fa-2x"></i>  ';	//show watch icon
					}
					$episodeDate = date ($dateformat, $thisEpisodeDate);
					$resulting_episodes .= $episodeDate.'</p>';

					
					//// Edit/Delete button for logged user (i.e. admin)
					if (isUserLogged()) { 
						$resulting_episodes .= '<p><a class="btn btn-inverse btn-mini" href="?p=admin&amp;do=edit&amp;=episode&amp;name='.$thisPodcastEpisode[5].'.'.$thisPodcastEpisode[3].'">'._("Edit / Delete").'</a></p>';
					}


								//Show Image embedded in the mp3 file or image associated in the images/ folder from previous versions of PG (i.e. 1.4-) - Just jpg and png extension supported
							if (file_exists($absoluteurl.$img_dir.$thisPodcastEpisode[5].'.jpg')) {
							$resulting_episodes .= '<img class="episode_image" src="'.$url.$img_dir.$thisPodcastEpisode[5].'.jpg" alt="'.$thisPodcastEpisodeData[0].'" />';
							} 
							else if (file_exists($absoluteurl.$img_dir.$thisPodcastEpisode[5].'.png')) {
							$resulting_episodes .= '<img class="episode_image"  src="'.$url.$img_dir.$thisPodcastEpisode[5].'.png" alt="'.$thisPodcastEpisodeData[0].'" />';
							}

					//// Show Long description if available, otherwise, short Description
					if ($thisPodcastEpisodeData[2] != NULL) $resulting_episodes .= '<p>'.$thisPodcastEpisodeData[2].'</p>';
					else $resulting_episodes .= '<p>'.$thisPodcastEpisodeData[1].'</p>';
					
					/// Categories 
					$resulting_episodes .= '<p><em>'._("Categories").'</em> ';	
					if ($thisPodcastEpisodeData[8] != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$thisPodcastEpisodeData[8] .'">'.categoryNameFromID($absoluteurl,$thisPodcastEpisodeData[8]).'</a>';
					if ($thisPodcastEpisodeData[9] != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$thisPodcastEpisodeData[9].'">'.categoryNameFromID($absoluteurl,$thisPodcastEpisodeData[9]).'</a>';
					if ($thisPodcastEpisodeData[10] != "") $resulting_episodes .= ' | <a href="?p=archive&cat='.$thisPodcastEpisodeData[10].'">'.categoryNameFromID($absoluteurl,$thisPodcastEpisodeData[10]).'</a>';
					$resulting_episodes .= '</p>';
				

					////Buttons (More, Download, Watch).
					$resulting_episodes .= showButtons($thisPodcastEpisode[5],$thisPodcastEpisode[3],$url,$upload_dir,"singleEpisode",$thisPodcastEpisode[1],$enablestreaming);

					
					////Other details (file type, duration, bitrate, frequency)					
					//NB. read from XML DB (except file extension = $thisPodcastEpisode[3]).
					$episodeDetails = _('Filetype:')." ".strtoupper($thisPodcastEpisode[3]);
					if ($thisPodcastEpisodeData[11] != NULL) $episodeDetails .= ' - '._('Size:')." ".$thisPodcastEpisodeData[11]._("MB");
					
					if($thisPodcastEpisodeData[12]!=NULL) { // display file duration
					$episodeDetails .= " - "._("Duration:")." ".$thisPodcastEpisodeData[12]." "._("m");
					}
					if($thisPodcastEpisode[3]=="mp3" AND $thisPodcastEpisodeData[13] != NULL AND $thisPodcastEpisodeData[14] != NULL) { //if mp3 show bitrate and frequency
						$episodeDetails .= " (".$thisPodcastEpisodeData[13]." "._("kbps")." ".$thisPodcastEpisodeData[14]." "._("Hz").")";
					}
					$resulting_episodes .= '<p class="episode_info">'.$episodeDetails.'</p>';


					////Playes: audio (flash/html5) and video (html5), for supported files and browsers
					//if audio and video streaming is enabled in PG options
					if ($enablestreaming=="yes" AND !detectMobileDevice()) { 
						$resulting_episodes .= showStreamingPlayers ($thisPodcastEpisode[5],$thisPodcastEpisode[3],$url,$upload_dir,"singleEpisode");
					}
					$isvideo = FALSE; //RESET isvideo for next episode

					
					////Social networks and (eventual) embedded code
					$resulting_episodes .= attachToEpisode($thisPodcastEpisode[5],$thisPodcastEpisode[3],$thisPodcastEpisodeData[0]);


					//Close the single episode DIV
					$resulting_episodes .= "</div>";
					//Close div with class row-fluid (theme based on bootstrap). Theme engine >= 2.0
					$resulting_episodes .= "</div>"; //close class row-fluid (bootstrap)

					//Append this episode to the final output to return
					$finalOutputEpisodes .= $resulting_episodes;
				}


	//Finally, return all the episodes to output on the web page
	return $finalOutputEpisodes;

} 
## END - SHOW SIGLE EPISODE
##################################################################################



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


function random_str($size) { 
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


function showButtons($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,$recent_count,$absolutePathEpisode,$enablestreaming) {
	
	$buttonsOutput = '<p>';
	
	//// Button "More" - in the permalink it is not show (no numeric var passed)
	if (is_numeric($recent_count)) $buttonsOutput .= '<a class="btn" href="?name='.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-search"></i> '._("More").'</a>&nbsp;&nbsp;';
	
	//// Button Watch (takes into account $enablestreaming from config.php)
	$browserAudioVideoSupport = detectModernBrowser();
	if ($enablestreaming == "yes" AND isItAvideo($podcast_filetype) == TRUE AND $browserAudioVideoSupport[1] == TRUE AND !detectMobileDevice()) {
	//javascript:; is added as an empty link for href
	$buttonsOutput .= '<a href="javascript:;" class="btn"  onclick="$(\'#videoPlayer'.$recent_count.'\').fadeToggle();$(this).css(\'font-weight\',\'bold\');"><i class="fa fa-youtube-play"></i> '._("Watch").'</a>&nbsp;&nbsp;';
	}
	
	//// Button download
	//Download button doesn't appear for episodes with future publication date (security reasons)
	if (!publishInFuture($absolutePathEpisode)) {
		//iOS device has been reported having some trouble downloading episode using the "download.php" forced download...
		if (!detectMobileDevice()) {
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
			$buttonsOutput .= '<a class="btn" href="'.$url.$upload_dir.$filenameWithoutExtension.'.'.$podcast_filetype.'"><i class="fa fa-download"></i> 	'._("Download").'</a>';
			}
		}
	}
	
	$buttonsOutput .= '</p>';

return $buttonsOutput;
}


function showStreamingPlayers($filenameWithoutExtension,$podcast_filetype,$url,$upload_dir,$recent_count) {
	
	$playersOutput = "";
	
	$browserAudioVideoSupport = detectModernBrowser();
	
	//// AUDIO PLAYER (MP3)
		if ($browserAudioVideoSupport[0] == TRUE AND $podcast_filetype=="mp3") { //if browser supports HTML5
		$showplayercode =	'<audio style="width:80%;" controls preload="none">
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

		$playersOutput .= '<video width="85%" id="videoPlayer'.$recent_count.'" style="display:none;" controls preload="none">
		  <source src="'.$url.$upload_dir.$filenameWithoutExtension.'.'.$podcast_filetype.'" type="video/mp4">
		'._("Your browser does not support the video player").'
		</video>';

		$playersOutput .= '<br />';
		}
		
		return $playersOutput;
		
}



function retrieveMediaFileDetails ($MediaFile,$absoluteURL,$filenameWithoutExtension,$imgDir) {
	
	if ($filenameWithoutExtension != NULL AND $imgDir != NULL) {
		$extraDataSent = TRUE;
	//	echo 'File name no ext: '.$filenameWithoutExtension.'<br>image dir: '.$imgDir.'<br>';
	} else {
		$extraDataSent = FALSE;
	}
	

	
	require_once($absoluteURL."components/getid3/getid3.php"); //Lib to read ID3 tags in media files
	$getID3 = new getID3; //initialize getID3 engine
	
	$ThisFileSizeInMB = round(filesize($MediaFile)/1048576,2);
	$ThisFileInfo = $getID3->analyze($MediaFile); //read file tags
	$file_duration = @$ThisFileInfo['playtime_string'];
	//$file_type = @$ThisFileInfo['fileformat'];
	$file_bitrate =  @$ThisFileInfo['bitrate']/1000;
	$file_freq = @$ThisFileInfo['audio']['sample_rate'];
	$file_embedded_image = NULL;
	
	
	//// Title from ID3 tags
	$thisFileTitleID3 = NULL;
	if (isset($ThisFileInfo['tags']['id3v2']['title'][0]) AND $ThisFileInfo['tags']['id3v2']['title'][0] != NULL) { //ID3 v2
		$thisFileTitleID3 = @$ThisFileInfo['tags']['id3v2']['title'][0];
	} elseif (isset($ThisFileInfo['tags']['id3v1']['title'][0]) AND $ThisFileInfo['tags']['id3v1']['title'][0] != NULL) { //ID3 v1
		$thisFileTitleID3 = @$ThisFileInfo['tags']['id3v1']['title'][0];
	}
	
	//// Artist from ID3 tags
	$thisFileArtistID3 = NULL;
	if (isset($ThisFileInfo['tags']['id3v2']['artist'][0]) AND $ThisFileInfo['tags']['id3v2']['artist'][0] != NULL) { //ID3 v2
		$thisFileArtistID3 = @$ThisFileInfo['tags']['id3v2']['artist'][0];
	} elseif (isset($ThisFileInfo['tags']['id3v1']['artist'][0]) AND $ThisFileInfo['tags']['id3v1']['artist'][0] != NULL) { //ID3 v1
		$thisFileArtistID3 = @$ThisFileInfo['tags']['id3v1']['artist'][0];
	}
	
	
	//Image embedded in the MP3 - Extract and Save
	if($extraDataSent AND isset($ThisFileInfo['id3v2']['APIC'][0]) AND !file_exists($absoluteURL.$imgDir.$filenameWithoutExtension.'.png') AND !file_exists($absoluteURL.$imgDir.$filenameWithoutExtension.'.jpg')) {
	
	$imageMimeType = $ThisFileInfo['id3v2']['APIC'][0]['image_mime'];
	$imageData = $ThisFileInfo['id3v2']['APIC'][0]['data'];
	
	//$file_embedded_image = 'data:'.$ThisFileInfo['id3v2']['APIC'][0]['image_mime'].';charset=utf-8;base64,'.base64_encode(	$ThisFileInfo['id3v2']['APIC'][0]['data']);
	//	echo 'Image Embedded:<br><img id="FileImage" width="150" src="'.$file_embedded_image.'" />'; 
	
	//Save image file extracted from ID3 v2 APIC (attached picture) tag
	$data = 'data:'.$imageMimeType.';base64,'.base64_encode($imageData);
	list($type, $data) = explode(';', $data);
	list(, $data)      = explode(',', $data);
	$data = base64_decode($data);

		//choose extension between png and jpg (jpg by default)
		if ($imageMimeType == "image/png") $thisImageExtension = "png";
		else $thisImageExtension = "jpg";
	
	//write file
	file_put_contents($absoluteURL.$imgDir.$filenameWithoutExtension.'.'.$thisImageExtension.'', $data);
	}
	
	return array($ThisFileSizeInMB,$file_duration,$file_bitrate,$file_freq,$thisFileTitleID3,$thisFileArtistID3);

}



function readMediaDir ($absoluteurl,$upload_dir) {
	
	//List of directories or files to exclude
	$toExclude = array("..",".","index.htm","_vti_cnf",".DS_Store",".svn",".xml");
	
	$handle = opendir ($absoluteurl.$upload_dir);
	$files_array = array(); //null array
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



##################################################################################
##################################################################################
## GENERATE PODCAST RSS FEED

//GeneratePodcastFeed $outputInFile TRUE writes to a file (feed.xml), FALSE prints on screen, $manualRegeneration means that the function is called explicitly by the user (and writeEpisodeXMLDB is called)
function generatePodcastFeed ($outputInFile,$category,$manualRegeneration) {
	
	//include functions and variables in config.php
	include("core/includes.php"); 
	

	//// Set custom web url (shown in iTunes Store), if specified in config.php
	if (isset($feed_iTunes_LINKS_Website) AND $feed_iTunes_LINKS_Website != NULL) {
	$podcastWebHomePage = $feed_iTunes_LINKS_Website; } 
	else { $podcastWebHomePage = $url; }
	

	//// Define feed filename
	$feedfilename = $absoluteurl.$feed_dir."feed.xml";

	//// Rewrite the language var to adhere to ISO639
	$feed_language = languageISO639($feed_language);
	

	##### Clean categories strings
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
		<link>'.$podcastWebHomePage.'</link>
		</image>
		<itunes:summary>'.$podcast_description.'</itunes:summary>
		<itunes:subtitle>'.$podcast_subtitle.'</itunes:subtitle>
		<itunes:author>'.$author_name.'</itunes:author>
		<itunes:owner>
		<itunes:name>'.$author_name.'</itunes:name>
		<itunes:email>'.$author_email.'</itunes:email>
		</itunes:owner>
		<itunes:explicit>'.$explicit_podcast.'</itunes:explicit>
		';

		
	//// iTunes categories (and subcategories, which are separated by :)
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


	//// List all the items (i.e. podcast episodes)
	// Open podcast directory
	$fileNamesList = readMediaDir ($absoluteurl,$upload_dir);

	$episodes_feed = NULL; //define variable
	if (!empty($fileNamesList)) { // If media directory contains files

	$episodesCounter = 0; //set counter to zero

	// Loop through each file in the media directory
		foreach ($fileNamesList as $singleFileName) {

			//Limit episodes in the feed (from config.php)
			if ($episodesCounter < $recent_episode_in_feed OR $recent_episode_in_feed == "All") { 	

			////Validate the current episode
			//NB. validateSingleEpisode returns [0] episode is supported (bool), [1] Episode Absolute path, [2] Episode XML DB absolute path,[3] File Extension (Type), [4] File MimeType, [5] File name without extension, [6] episode file supported but to XML present
			$thisPodcastEpisode = validateSingleEpisode($singleFileName);


				////If episode is supported and has a related xml db, and if it's not set to a future date OR if it's set for a future date but you are logged in as admin
				if (($thisPodcastEpisode[0]==TRUE AND !publishInFuture($thisPodcastEpisode[1]))) { 

				
				////Parse XML data related to the episode 
				// NB. Function parseXMLepisodeData returns: [0] episode title, [1] short description, [2] long description, [3] image associated, [4] iTunes keywords, [5] Explicit language,[6] Author's name,[7] Author's email,[8] PG category 1, [9] PG category 2, [10] PG category 3, [11] file_info_size, [12] file_info_duration, [13] file_info_bitrate, [14] file_info_frequency
				$thisPodcastEpisodeData = parseXMLepisodeData($thisPodcastEpisode[2]);
				
				
				//// If feed manually regenerated, recreate XML DB for each file when XML does not contain file data such as size, duration etc... (i.e. <fileInfoPG> tag)
				// NB. The following function is transitional, to enable new XML tags in the file XML data introduced with PG 2.3: it can be removed in future versions.
				// We check for data about episode size ($thisPodcastEpisodeData[11]) cause all the episodes should have it, if not, the XML was generated with a version of PG < 2.3
				// We also check whether $thisPodcastEpisodeData[3] (image) is null. From PG 2.4 the image field can be a) a file name (for retro compatibility with older versions), b) 1 (mp3 parsed for embedded image. If image exists the file is extracted automatically in the images/ folder by the function retrieveMediaFileDetails)
				if ($manualRegeneration AND $thisPodcastEpisodeData[11] == NULL OR $manualRegeneration AND $thisPodcastEpisodeData[3] == "") { //NB $thisPodcastEpisodeData[3] = to "" empty and not NULL (it exists but does not contain any value).
					//// Remapping data from parseXMLepisodeData to be sent as a parameter to writeEpisodeXMLDB
					$thisEpisodeDataToWriteInXML[0] = $thisPodcastEpisodeData[0]; // Title
					$thisEpisodeDataToWriteInXML[1] = $thisPodcastEpisodeData[1]; // Short Desc
					$thisEpisodeDataToWriteInXML[2] = $thisPodcastEpisodeData[2]; // Long Desc
				
					//Image embedded or specified in the XML file is empty (no values)
					if ($thisPodcastEpisodeData[3] == "") $thisEpisodeDataToWriteInXML[3] = 1; // assign value of 1 (so in future it won't be processed)
					else $thisEpisodeDataToWriteInXML[3] = $thisPodcastEpisodeData[3]; // Image

					$thisEpisodeDataToWriteInXML[4] = array($thisPodcastEpisodeData[8],$thisPodcastEpisodeData[9],$thisPodcastEpisodeData[10]); // Categories
					$thisEpisodeDataToWriteInXML[5] = $thisPodcastEpisodeData[4]; // Keywords
					$thisEpisodeDataToWriteInXML[6] = $thisPodcastEpisodeData[5]; // Explicit
					$thisEpisodeDataToWriteInXML[7] = $thisPodcastEpisodeData[6]; // Auth name
					$thisEpisodeDataToWriteInXML[8] = $thisPodcastEpisodeData[7]; // Auth email
					
//Episode size and data from GETID3 from retrieveMediaFileDetails function
//NB retrieveMediaFileDetails returns: [0] $ThisFileSizeInMB, [1] $file_duration, [2] $file_bitrate, [3] $file_freq, [4] $thisFileTitleID3, [5] $thisFileArtistID3
					$episodeID3 = retrieveMediaFileDetails ($thisPodcastEpisode[1],$absoluteurl,$thisPodcastEpisode[5],$img_dir);

	//Rewrite the XML data file of this episode (including the fileInfoPG tag)
	writeEpisodeXMLDB($thisEpisodeDataToWriteInXML,$absoluteurl,$thisPodcastEpisode[1],$thisPodcastEpisode[2],$thisPodcastEpisode[5],TRUE);

				} //end if $manualRegeneration

					
				//// If category is specified, show just episodes belonging to it (if the current is not, skip this loop)
				if (isset($category) AND $category != NULL AND $category != $thisPodcastEpisodeData[8] AND $category != $thisPodcastEpisodeData[9] AND $category != $thisPodcastEpisodeData[10]) { 
					continue;
				}
				

				//// Content Depuration (to avoid validation errors in the RSS feed)
				$text_title = depurateContent($thisPodcastEpisodeData[0]); //title
				$text_shortdesc = depurateContent($thisPodcastEpisodeData[1]); //short desc
				$text_longdesc = stripslashes($thisPodcastEpisodeData[2]);
				$text_longdesc = strip_tags($text_longdesc);
				$text_longdesc = depurateCDATAfield($text_longdesc);
				$text_keywordspg = depurateContent($thisPodcastEpisodeData[4]); //Keywords
				$text_keywordspg = htmlspecialchars($thisPodcastEpisodeData[4]); //convert special characters e.g. r&b -> r&amp;b
				$text_authornamepg = depurateContent($thisPodcastEpisodeData[6]); //author's name
				$text_authoremailpg = depurateContent($thisPodcastEpisodeData[7]);

				// Other Data from the file
				$text_explicit = $thisPodcastEpisodeData[5];
				$file_size = filesize($thisPodcastEpisode[1]);
				$filetime = filemtime($thisPodcastEpisode[1]);
				$filepubdate = date ('r', $filetime);
				$filemimetype = $thisPodcastEpisode[4];
				$fileDuration = $thisPodcastEpisodeData[12];

				$episodes_feed.= '
				<item>
				<title>'.$text_title.'</title>
				<itunes:subtitle>'.$text_shortdesc.'</itunes:subtitle>
				<itunes:summary><![CDATA[ '.$text_longdesc.' ]]></itunes:summary>
				<description>'.$text_shortdesc.'</description>
				<link>'.$link.$singleFileName.'</link>
				<enclosure url="'.$url.$upload_dir.$singleFileName.'" length="'.$file_size.'" type="'.$filemimetype.'"/>
				<guid>'.$link.$singleFileName.'</guid>
				';

				//// Duration
				if($fileDuration != NULL) { 
				$episodes_feed.= '<itunes:duration>'.$fileDuration.'</itunes:duration>
				';
				} 
				

				
				//Image associated to single episode
				if (file_exists($absoluteurl.$img_dir.$thisPodcastEpisode[5].'.jpg')) {
					$episodes_feed.= '<itunes:image href="'.$url.$img_dir.$thisPodcastEpisode[5].'.jpg" />
				';
				} 
				else if (file_exists($absoluteurl.$img_dir.$thisPodcastEpisode[5].'.png')) {
					$episodes_feed.= '<itunes:image href="'.$url.$img_dir.$thisPodcastEpisode[5].'.png" />
				';
				}
				


				//// Author
				// If no author specified, use default author from config.php
				if ($text_authornamepg == NULL OR $text_authornamepg == ",") { 
				$episodes_feed.= '<author>'.$author_email.' ('.$author_name.')</author>
				<itunes:author>'.$author_name.'</itunes:author>
				';
				} else {
				$episodes_feed.= '<author>'.$text_authoremailpg.' ('.$text_authornamepg.')</author>
				<itunes:author>'.$text_authornamepg.'</itunes:author>
				';
				}


				//// Keywords
				if ($text_keywordspg!=NULL) { //if keywords are present
				$episodes_feed.= '<itunes:keywords>'.$text_keywordspg.'</itunes:keywords>
				';
				} 


				//// Explicit
				if ($text_explicit!=NULL) {
				$episodes_feed.= '<itunes:explicit>'.$text_explicit.'</itunes:explicit>
				';
				}

				//// File Date
				$episodes_feed.= '<pubDate>'.$filepubdate.'</pubDate>
				</item>
				';

				$episodesCounter++; // increment recent counter

				} // END - If episode is supported
			} // END - Limit episodes in the feed
		} // END - Loop through each file in the media directory
	} // END - If media directory contains files



	//// RSS Feed Tail
	$tail_feed = '
	</channel>
	</rss>';
	
	//// Construct Output
	$finalRSSfeed = $head_feed.$episodes_feed.$tail_feed;
	
	// Output in a file
	if ($outputInFile == TRUE) {
	$fp1 = fopen($feedfilename, "w+"); //Open for reading and empty
	fclose($fp1);

	$fp = fopen($feedfilename, "a+"); //testa xml
	fwrite($fp, $finalRSSfeed); 
	fclose($fp);
	} 
	// Output on screen
	else {
	echo $finalRSSfeed;
	}
	
	if (!isset($episodesCounter)) $episodesCounter = 0;
	return $episodesCounter;
	
}
## END - GENERATE PODCAST RSS FEED
##################################################################################



function attachToEpisode ($episodeFileNameWithoutExtension,$episodeFileExtension,$episodeTitle) {
//include functions and variables in config.php
	include("core/includes.php");

	//NB $url comes from config.php
	$fullURL = $url."?name=".$episodeFileNameWithoutExtension.'.'.$episodeFileExtension; //full URL of the episode
	
	$outputToReturn = NULL;
	
	// CUSTOMIZED CODE TO EMBED along with each episode
	// IF a file called embed-code.txt is manually created in the root of Podcast Generator. The content of that file will be displayed along with each episode
		if(file_exists($absoluteurl."embed-code.txt")){
			$embeddedcodetoshow = file_get_contents($absoluteurl."embed-code.txt");
			$outputToReturn .= $embeddedcodetoshow; } 
		
	//SOCIAL NETWORKS INTEGRATION
	//if the parameter "noextras" (e.g. ?p=archive&cat=all&noextras) is passed in the GET, then no social network integration is displayed
	if (!isset($_GET['noextras'])) {
		if (in_array(TRUE,$enablesocialnetworks)) { //IF at least one value of config.php is true
		$outputToReturn .= displaySocialNetworkButtons($fullURL,$episodeTitle,$enablesocialnetworks[0],$enablesocialnetworks[1],$enablesocialnetworks[2]); //0 is FB, 1 twitter, 2 G+
		//Blank space
		$outputToReturn .= '<br />';
		}
	}
	
	return $outputToReturn;
}

	
function validateSingleEpisode ($episodeFile) {
//include functions and variables in config.php

	include("core/includes.php");

	$episodeFile_parts = divideFilenameFromExtension($episodeFile); // PHP >= 5.2.0 needed
	$episodeFilenameWithoutExtension = $episodeFile_parts[0];
	$EpisodeFileExtension = strtolower($episodeFile_parts[1]); //lowercase extension
	$checkEpisodeFileFormat = checkFileType($EpisodeFileExtension,$absoluteurl);
	$episodeFileType = $checkEpisodeFileFormat[0];
	$episodeFileMimeType = $checkEpisodeFileFormat[1];
	$episodeFileFullPath = $absoluteurl.$upload_dir.$episodeFile;
	$episodeFileXMLDB = $absoluteurl.$upload_dir.$episodeFilenameWithoutExtension.'.xml'; //database file
	
		//If media file is ok and XML file is associated to it
		if (isset($episodeFileType) AND $EpisodeFileExtension==$episodeFileType AND file_exists($episodeFileXMLDB)) { 
		//NB. $GoForIt = TRUE means that the episode file format is supported, it has a corresponding XML data file
		$GoForIt = TRUE;
		$OkButNoXMLDBpresent = FALSE;
		}
		//If media file is ok but no XML file is associated (i.e. auto index / FTP feature)
		else if (isset($episodeFileType) AND $EpisodeFileExtension==$episodeFileType AND !file_exists($episodeFileXMLDB)) { 
		$GoForIt = FALSE;
		$OkButNoXMLDBpresent = TRUE;
		}
		else {
		$GoForIt = FALSE;
		$OkButNoXMLDBpresent = FALSE;
		}
	
	return array($GoForIt,$episodeFileFullPath,$episodeFileXMLDB,$episodeFileType,$episodeFileMimeType,$episodeFilenameWithoutExtension,$OkButNoXMLDBpresent);

}



//DETECT WHETHER USER IS LOGGED-IN OR NOT
function isUserLogged () {
	//// Security code (Register Globals ON)
	if (isset($_REQUEST['GLOBALS'])) { exit; } 
	
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


function parseXMLepisodeData ($episodeFileXMLDB) {

	$parser = simplexml_load_file($episodeFileXMLDB,'SimpleXMLElement',LIBXML_NOCDATA);
	//var_dump($parser); //Debug
	
	//NB. to handle CDATA values see: http://blog.evandavey.com/2008/04/how-to-fix-simplexml-cdata-problem-in-php.html

	//Parse the episode in the xml file - just one episode (array [0]) is stored in each XML file
	$episode_title = $parser->episode[0]->titlePG[0]; //episode title
	$episode_shortdesc = $parser->episode[0]->shortdescPG[0]; //short description
	$episode_longdesc = $parser->episode[0]->longdescPG[0]; // long description
	$episode_imgpg = $parser->episode[0]->imgPG[0]; // image (url) associated to episode
	$episode_keywordspg = $parser->episode[0]->keywordsPG[0]; //iTunes keywords
	$episode_explicitpg = $parser->episode[0]->explicitPG[0]; //explicit podcast (yes or no)
	$episode_authornamepg = $parser->episode[0]->authorPG[0]->namePG[0]; //author's name 
	$episode_authoremailpg = $parser->episode[0]->authorPG[0]->emailPG[0]; //author's email

	//categories
	$episode_category1 = $parser->episode[0]->categoriesPG[0]->category1PG[0];
	$episode_category2 = $parser->episode[0]->categoriesPG[0]->category2PG[0];
	$episode_category3 = $parser->episode[0]->categoriesPG[0]->category3PG[0];

	//Until PG 2.2 this data was read from the media file in real time, from 2.3+ it'll be stored in the XML
	$file_info_size = NULL;
	$file_info_duration = NULL;
	$file_info_bitrate = NULL;
	$file_info_frequency = NULL;
		if (isset($parser->episode[0]->fileInfoPG[0]->size[0])) $file_info_size = $parser->episode[0]->fileInfoPG[0]->size[0];
		if (isset($parser->episode[0]->fileInfoPG[0]->duration[0])) $file_info_duration = $parser->episode[0]->fileInfoPG[0]->duration[0];
		if (isset($parser->episode[0]->fileInfoPG[0]->bitrate[0])) $file_info_bitrate = $parser->episode[0]->fileInfoPG[0]->bitrate[0];
		if (isset($parser->episode[0]->fileInfoPG[0]->frequency[0])) $file_info_frequency = $parser->episode[0]->fileInfoPG[0]->frequency[0];


	return array($episode_title,$episode_shortdesc,$episode_longdesc,$episode_imgpg,$episode_keywordspg,$episode_explicitpg,$episode_authornamepg,$episode_authoremailpg,$episode_category1,$episode_category2,$episode_category3,$file_info_size,$file_info_duration,$file_info_bitrate,$file_info_frequency);

}


// $readID3 is a bool (true on first upload and on manual RSS regeneration)
function writeEpisodeXMLDB ($thisEpisodeData,$absoluteurl,$episodeFileAbsPath,$episodeXMLDBAbsPath,$episodeFileNameWithoutExtension,$readID3) {


	include($absoluteurl."core/includes.php");
	

	
	//NB. $thisEpisodeData array contains [0] $title, [1] $description, [2] $long_description, [3] $image_new_name, [4] $category (array), [5] $keywords, [6] $explicit, [7] $auth_name, [8] $auth_email
	

	$xmlfiletocreate = '<?xml version="1.0" encoding="'.$feed_encoding.'"?>
	<PodcastGenerator>
		<episode>
		<titlePG><![CDATA['.$thisEpisodeData[0].']]></titlePG>
		<shortdescPG><![CDATA['.$thisEpisodeData[1].']]></shortdescPG>
		<longdescPG><![CDATA['.$thisEpisodeData[2].']]></longdescPG>
		<imgPG>'.$thisEpisodeData[3].'</imgPG>
		<categoriesPG>
		<category1PG>';
	if(isset($thisEpisodeData[4][0]) AND $thisEpisodeData[4][0]!= NULL){
		$xmlfiletocreate .=	$thisEpisodeData[4][0];
	}
	$xmlfiletocreate .='</category1PG>
		<category2PG>';
	if(isset($thisEpisodeData[4][1]) AND $thisEpisodeData[4][1]!= NULL){
		$xmlfiletocreate .=	$thisEpisodeData[4][1];
	}
	$xmlfiletocreate .='</category2PG>
		<category3PG>';
	if(isset($thisEpisodeData[4][2]) AND $thisEpisodeData[4][2]!= NULL){
		$xmlfiletocreate .=	$thisEpisodeData[4][2];
	}
	$xmlfiletocreate .='</category3PG>
		</categoriesPG>
		<keywordsPG>'.$thisEpisodeData[5].'</keywordsPG>
		<explicitPG>'.$thisEpisodeData[6].'</explicitPG>
		<authorPG>
		<namePG>'.$thisEpisodeData[7].'</namePG>
		<emailPG>'.$thisEpisodeData[8].'</emailPG>
		</authorPG>';
	

	$episodeID3 = array(); //Declaration
	if ($readID3 == TRUE) {
	
	//Episode size and data from GETID3 from retrieveMediaFileDetails function
	//NB retrieveMediaFileDetails returns: [0] $ThisFileSizeInMB, [1] $file_duration, [2] $file_bitrate, [3] $file_freq, [4] $thisFileTitleID3, [5] $thisFileArtistID3
	$episodeID3 = retrieveMediaFileDetails ($episodeFileAbsPath,$absoluteurl,$episodeFileNameWithoutExtension,$img_dir);

		$xmlfiletocreate .='
			<fileInfoPG>';
		if(isset($episodeID3[0]) AND $episodeID3[0]!= NULL){
			$xmlfiletocreate .=	'
			<size>'.$episodeID3[0].'</size>';
		}
		if(isset($episodeID3[1]) AND $episodeID3[1]!= NULL){
			$xmlfiletocreate .=	'
			<duration>'.$episodeID3[1].'</duration>';
		}
		//NB variable bitrate is rounded to int
		if(isset($episodeID3[2]) AND $episodeID3[2]!= NULL){
			$xmlfiletocreate .=	'
			<bitrate>'.round($episodeID3[2]).'</bitrate>';
		}
		if(isset($episodeID3[3]) AND $episodeID3[3]!= NULL){
			$xmlfiletocreate .=	'
			<frequency>'.$episodeID3[3].'</frequency>';
		}
	}
	
	$xmlfiletocreate .='
			</fileInfoPG>
		</episode>
	</PodcastGenerator>';

	
	//// Write the XMK file
	$fp = fopen($episodeXMLDBAbsPath,'w');
	fwrite($fp,$xmlfiletocreate);
	fclose($fp);

}


// NB. Former "FTP Feature"
function autoIndexingEpisodes () {

include("core/includes.php");

// Open podcast directory and read all the files contained
$fileNamesList = readMediaDir ($absoluteurl,$upload_dir);


	if (!empty($fileNamesList)) { // If media directory contains files

	$episodesCounter = 0; //set counter to zero

		// Loop through each file in the media directory
		foreach ($fileNamesList as $singleFileName) {

		////Validate the current episode
		//NB. validateSingleEpisode returns [0] episode is supported (bool), [1] Episode Absolute path, [2] Episode XML DB absolute path,[3] File Extension (Type), [4] File MimeType, [5] File name without extension, [6] episode file supported but to XML present
		$thisPodcastEpisode = validateSingleEpisode($singleFileName);
		
			////If episode is supported and does NOT have a related xml db
			if ($thisPodcastEpisode[6]==TRUE) { 
	
			
				// From config.php
				if ($strictfilenamepolicy == "yes") {
				$episodeNewFileName = date('Y-m-d')."_".renamefilestrict($thisPodcastEpisode[5]);
				}
				else {
				$episodeNewFileName = renamefile($thisPodcastEpisode[5]);
				}

			//lowercase extension
			$episodeNewFileExtension = strtolower($thisPodcastEpisode[3]);

			// New file full path
			$episodeNewNameAbsPath = $absoluteurl.$upload_dir.$episodeNewFileName.'.'.$episodeNewFileExtension;
				//if file already exists add an incremental suffix
				
				
				$filesuffix = NULL;
				while (file_exists($episodeNewNameAbsPath)) { 
				$filesuffix++;
				$episodeNewNameAbsPath = $absoluteurl.$upload_dir.$episodeNewFileName.$filesuffix.'.'.$episodeNewFileExtension;
				}

			
				if (file_exists($thisPodcastEpisode[1])) {
				//rename episode
				rename ($thisPodcastEpisode[1],$episodeNewNameAbsPath);
				// Change file date to now
				touch($episodeNewNameAbsPath,time());
				} else { exit; }


			//Episode size and data from GETID3 from retrieveMediaFileDetails function
			//NB retrieveMediaFileDetails returns: [0] $ThisFileSizeInMB, [1] $file_duration, [2] $file_bitrate, [3] $file_freq, [4] $thisFileTitleID3, [5] $thisFileArtistID3
			$episodeID3 = retrieveMediaFileDetails ($episodeNewNameAbsPath,$absoluteurl,$thisPodcastEpisode[5],$img_dir);

			//// Assign title and short description (from ID3 title and artist, respectively. Or default)
			if ($episodeID3[4]!= "") $thisEpisodeTitle = $episodeID3[4];
			else $thisEpisodeTitle = $thisPodcastEpisode[5];
			if ($episodeID3[5]!= "") $thisEpisodeShortDesc = $episodeID3[5];
			else $thisEpisodeShortDesc = _("Podcast Episode");
			
			// Use GETID3 Title and Artist to fill title and description automatically
			$thisEpisodeData = array($thisEpisodeTitle,$thisEpisodeShortDesc,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

			$episodeXMLDBAbsPath = $absoluteurl.$upload_dir.$episodeNewFileName.$filesuffix.'.xml';
			//// Creating xml file associated to episode
			writeEpisodeXMLDB($thisEpisodeData,$absoluteurl,$episodeNewNameAbsPath,$episodeXMLDBAbsPath,$episodeNewFileName.$filesuffix,TRUE);

			$episodesCounter++;
			
			} // END - If episode is supported
		
		} // END - Loop through each file

	} // END - If media directory contains files

	return $episodesCounter;

}


?>