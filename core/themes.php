<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//THIS FILE HANDLES THE OLD THEMES (i.e. PG v. 1.x). We keep it for retro compatibility with customized themes the users might have designed

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

if(($theme_file_contents = file_get_contents($theme_path."index.htm")) === FALSE) {
	echo "<p class=\"error\">"._("Failed to open theme file")."</p>";
	exit;
}

#Replace URLs
$theme_file_contents = str_replace("href=\"style/", "href=\"".$theme_path."style/", $theme_file_contents); // Replace CSS location

$theme_file_contents = str_replace("src=\"img/", "src=\"".$theme_path."img/", $theme_file_contents); // Replace image location

$theme_file_contents = str_replace("<param name=movie value=\"", "<param name=movie value=\"".$theme_path, $theme_file_contents); // Replace flash objects IE

$theme_file_contents = str_replace("<embed src=\"", "<embed src=\"".$theme_path, $theme_file_contents); // Replace flash objects embed



####### INCLUDE PHP FUNCTIONS SPECIFIED IN THE THEME (functions.php)
if (file_exists($theme_path."functions.php")) {
	include ($theme_path."functions.php");
}	
####### END INCLUDE PHP FUNCTIONS


#########################
# SET PAGE TITLE
$page_title = $podcast_title; 

if (isset($_GET['p'])) {

	if ($_GET['p']=="archive") {
		$page_title .= " - "._("Podcast Archive")."";

		/*
		#########
		// display category name in the title	
		if (isset($_GET['cat']) and $_GET['cat'] != NULL) {	
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

				foreach ($arr as $key => $val) {
					//$PG_mainbody .= "cat[" . $key . "] = " . $val . "<br>";

					if ($_GET['cat'] == $arrid[$key])
						$page_title .= ' - ' . $val . ''; //display cat name in the title

				}

			}


		}	*/
		#########		
	}
	elseif ($_GET['p']=="episode" AND isset($episode_present) AND $episode_present == "yes") {

		$page_title .= " - $text_title";
	}
}

$theme_file_contents = str_replace("-----PG_PAGETITLE-----", $page_title, $theme_file_contents);  

###############################
# LOAD JAVASCRIPTS IN THE HEADER IF PAGE REQUIRES - REPLACES "-----PG_JSLOAD-----" IN THE HEADER OF THE THEME PAGE

/*
if (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "upload") {

	include("$absoluteurl"."core/admin/loadjavascripts.php");
}

elseif (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "editdel") {

	include("$absoluteurl"."core/admin/loadjavascripts.php");
}

elseif (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "edit") {

	include("$absoluteurl"."core/admin/loadjavascripts.php");
}

elseif (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "categories") {

	include("$absoluteurl"."core/admin/loadjavascripts.php");

} 

elseif (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "freebox") {

	include("$absoluteurl"."core/admin/loadjavascripts.php");

}

else {
*/

include($absoluteurl."core/admin/loadjavascripts.php");
	
/*
}
*/


$theme_file_contents = str_replace("-----PG_JSLOAD-----", $loadjavascripts, $theme_file_contents); 

###############################
###############################



//LOAD A CSS WITH CLASSES COMMON TO ALL THE THEMES
$commonCSSurl = '<link href="themes/common.css" rel="stylesheet">';
$theme_file_contents = str_replace("-----PG_COMMONCSSLOAD-----", $commonCSSurl, $theme_file_contents); 






# SET RIGHT BOX

$urlforitunes = str_replace("http://", "itpc://", $url); 

$rightboxcontent = '<div class="rightbox">

	<b>'.$podcast_title.' '._("feed:").'</b>
	<p>'._("Copy the feed link and paste it into your aggregator").'<br /><br />
	<a href="'.$url.$feed_dir.'feed.xml"><img src="rss-podcast.gif" alt="'._("Copy the feed link and paste it into your aggregator").'" title="'._("Copy the feed link and paste it into your aggregator").'" border="0" /></a>
	</p>
	<p>'._("Subscribe to this podcast with iTunes").'<br /><br /><a href="'.$urlforitunes.$feed_dir.'feed.xml"><img src="podcast_itunes.jpg" alt="'._("Subscribe to this podcast with iTunes").'" title="'._("Subscribe to this podcast with iTunes").'" border="0" /></a></p>


	</div>';

# If you are logged show right boxes

if(isset($amilogged) AND $amilogged =="true") { //if logged

	//show donation box
	$rightboxcontent .= '<div class="rightbox">
		<b>'._("Make a donation:").'</b>
		<p>'._("If you like Podcast Generator please consider making a donation:").'<br /><br />
				<a href="https://www.paypal.com/cgi-bin/webscr?item_name=Donation+to+Podcast+Generator&cmd=_donations&business=beta%40yellowjug.com" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG_global.gif" border="0" alt="Donate through PayPal"></a>
				<br /><br />
			</p>
	</div>';

	//show PG box
	$rightboxcontent .= '<div class="rightbox">
		<b>'._("Podcast Generator").'</b><br /><p>- <a href="?p=admin&amp;do=serverinfo">'._("Your server configuration").'</a><br />- <a href="http://podcastgen.sourceforge.net/checkforupdates.php?v='.$podcastgen_version.'" target="_blank">'._("Check for updates").'</a><br />- <a href="http://feeds.podcastgenerator.net/podcastgenerator" target="_blank">'._("Subscribe to the news feed").'</a><br />- <a href="http://podcastgen.sourceforge.net/documentation.php?ref=local-admin" target="_blank">'._("Read documentation and get support").'</a><br />- <a href="http://podcastgen.sourceforge.net/credits.php?ref=local-admin" target="_blank">'._("Credits").'</a></p>
	</div>';

}

$theme_file_contents = str_replace("-----PG_RIGHTBOX-----", $rightboxcontent, $theme_file_contents); 


# SET RIGHT OPTIONAL BOX ("freebox")

if (isset($amilogged) AND $amilogged =="true") { //if you are logged do not display freebox

	$freeboxcontent = NULL;

	$theme_file_contents = str_replace("-----PG_FREEBOX-----", $freeboxcontent, $theme_file_contents);

	} elseif($freebox == "yes") {

		if(file_exists("$absoluteurl"."freebox-content.txt")){

			$freeboxcontenttodisplay = file_get_contents("$absoluteurl"."freebox-content.txt");	

			$freeboxcontent = "<div class=\"rightbox\">
				$freeboxcontenttodisplay
				</div>";
		} else {
			$freeboxcontent = NULL;
		}

		$theme_file_contents = str_replace("-----PG_FREEBOX-----", $freeboxcontent, $theme_file_contents); 

	} else {

		$freeboxcontent = NULL;

		$theme_file_contents = str_replace("-----PG_FREEBOX-----", $freeboxcontent, $theme_file_contents); 		

	}


	# Othere Theme elements replacing
	$theme_file_contents = str_replace("-----PG_MAINBODY-----", $PG_mainbody, $theme_file_contents);

	$theme_file_contents = str_replace("-----PG_PAGECHARSET-----", $feed_encoding, $theme_file_contents); 

	$theme_file_contents = str_replace("-----PG_PODCASTTITLE-----", $podcast_title, $theme_file_contents);

	$theme_file_contents = str_replace("-----PG_PODCASTSUBTITLE-----", $podcast_subtitle, $theme_file_contents);

	$theme_file_contents = str_replace("-----PG_PODCASTDESC-----", $podcast_description, $theme_file_contents); 

	$theme_file_contents = str_replace("-----PG_MENUHOME-----", _("Home"), $theme_file_contents); 

	$theme_file_contents = str_replace("-----PG_MENUARCHIVE-----", _("Podcast Archive"), $theme_file_contents); 

	$theme_file_contents = str_replace("-----PG_MENUADMIN-----", _("Admin"), $theme_file_contents); 

	#FOOTER

	$definefooter = _("Powered by").' <a href="http://podcastgen.sourceforge.net" title="'._("Podcast Generator")._(", an open source podcast publishing solution").'">'._("Podcast Generator").'</a>'._(", an open source podcast publishing solution");

	$theme_file_contents = str_replace("-----PG_FOOTER-----", $definefooter, $theme_file_contents);


	#########################
	# META TAGS AND FEED LINK

	//meta tags
	$metatagstoreplace = '
		<meta http-equiv="content-language" content="'.$scriptlang.'" />
		<meta name="Generator" content="Podcast Generator '.$podcastgen_version.'" />
		<meta name="Author" content="'.depuratecontent($author_name).'" />
		<meta name="Copyright" content="'.depuratecontent($copyright).'" />
		';
		

	if (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "itunesimg") { // no cache in itunes image admin page

		$metatagstoreplace .= '<meta http-equiv="expires" content="0" />
			';
	}


	# define META KEYWORDS

	// on single episode page (permalink), use itunes keywords and episode description as meta tags...
	if (isset($_GET['p']) AND $_GET['p']=="episode" AND isset($episode_present) AND $episode_present == "yes") { 
		if ($text_keywordspg != NULL) { // ...if keywords exist
			$metatagstoreplace .= '<meta name="Keywords" content="'.depuratecontent($text_keywordspg).'" />
				';
		}
		$metatagstoreplace .= '<meta name="Description" content="'.depuratecontent($text_shortdesc).'" />
			'; // use episode short description
	} 
	else { // if not permalink page, use podcast general description as meta tag
		$metatagstoreplace .= '<meta name="Description" content="'.depuratecontent($podcast_description).'" />
			';

	}

	// on the home page (recent_list.php) use keywords of the most recent episode
	if (isset($assignmetakeywords) AND $assignmetakeywords != NULL) { // the variable $assignmetakeywords is assigned in recent_list.php
		$metatagstoreplace .= '<meta name="Keywords" content="'.depuratecontent($assignmetakeywords).'" />
			';	
	}


	// general XML feed of the podcast
	$metatagstoreplace .= '
		<link href="'.$url.$feed_dir.'feed.xml" rel="alternate" type="application/rss+xml" title="'.$podcast_title.' RSS" />'; 

	$theme_file_contents = str_replace("-----PG_METATAGS-----", $metatagstoreplace, $theme_file_contents);

	# END META TAGS DEFINITION
	#########################

	
	
//INCLUDE LOADING INDICATOR IN ALL THE PAGES 
//The indicator is included in all the pages just before closing the tag </html> so we are sure it is outside others relative containers (e.g. bootstrap theme). Otherwise it would be displayed relative to the main container, not to the body

if (isset($_GET['p']) AND $_GET['p']=="admin") { //all admin pages included login
		
//NB the closing body tag has been added below!
$loading_indicator_code = '
<div id="status_notification"></div>
</body>
';
			
	$theme_file_contents = str_replace("</body>", $loading_indicator_code, $theme_file_contents);
	
}
	
//END - INCLUDE LOADING INDICATOR IN ALL THE PAGES 
	

	?>