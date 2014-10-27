<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//Checks for previously uploaded episodes with a future date, and regenerate automatically RSS feed if needed
//NB. THIS SCRIPT CAN BE CALLED THROUGH A CRON OR ACT AS PSEUDO CRON CALLED BY INDEX.PHP

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

ob_start(); 

$startTime = time();

//// Called directly throught the URL (i.e. if absoluteurl is not known)
if (!isset($absoluteurl)) { 
	//The unique key should be sent via GET variable and be correspond to $installationKey
	include("core/includes.php"); 
	if (!isset($_GET["key"]) OR isset($_GET["key"]) AND $_GET["key"] != $installationKey) {
	exit; //Key doesn not correspond with the one in config.php
	} 
$includedInIndexPHP = FALSE; //called through the URL (direct call or cron)
} 
//// Called via inclusion in index.php (pseudo cron)
else {
$includedInIndexPHP = TRUE; 
}


//// Auto Index new Episodes uploaded to the media folder (this works just went called via cron and not index.php)
if (isset($cronAutoIndex) AND $cronAutoIndex == TRUE AND $includedInIndexPHP == FALSE) {
	
	$episodesCounter = autoIndexingEpisodes();
	
	if ($episodesCounter > 0 AND $includedInIndexPHP == FALSE) {
	generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
	echo ' -- '.$episodesCounter.' '._("new episodes added"); //Output on screen
	}
	
} // END Auto Index new Episodes uploaded


//// Regenerate automatically RSS feed (so that episodes set to future are indexed in the main RSS feed)
if (isset($cronAutoRegenerateRSS) AND $cronAutoRegenerateRSS == TRUE) {
	
	// If called via index.php inclusion use the cache system (cache time in seconds defined in $cronAutoRegenerateRSScacheTime)
	if ($includedInIndexPHP == TRUE) {
	
		$RSSFeedURL = $absoluteurl.$feed_dir."feed.xml";
		$lastRSSFeedBuild = NULL;
		if (file_exists($RSSFeedURL)) {
		//Timestamp last time main RSS feed was generated
		$lastRSSFeedBuild = filemtime($RSSFeedURL);
		}

		if (isset($cronAutoRegenerateRSScacheTime) AND time() - $lastRSSFeedBuild > $cronAutoRegenerateRSScacheTime)  {
			$episodesinFeedCounter = generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
		}
	}
	//Called via CRON, regenerate feed now (no cache)
	else {
	$episodesinFeedCounter = generatePodcastFeed(TRUE,NULL,FALSE); //Output in file
		if ($episodesinFeedCounter > 0 AND $includedInIndexPHP == FALSE) {
		echo ' -- '._("RSS feed regenerated:").' '.$episodesinFeedCounter.' '._("episodes"); //Output on screen
		}
	}
	

} // END Regenerate automaticcaly RSS feed

	
	if ($includedInIndexPHP == FALSE) {
		$tempusFugit=time()-$startTime;
		echo ' -- '._("Execution time (s):").' '.$tempusFugit; //Output on screen
	}


ob_end_flush();
	
?>