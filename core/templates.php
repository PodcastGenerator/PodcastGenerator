<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//THIS FILE IS SIMILAR to themes.php and is used instead of it when we
//have a new theme for version 2.0+
//The old themes.php is kept for retro-compatibility with old themes
//The choice between themes.php and templates.php is made in index.php
//and depends on the theme.xml file (a file that must be included
//in the main folder of each new theme for PG 2.0+


/*Common CSS classes to add to a PG theme:

active (menu active)
nav-header (titles in sidebar)

navbar-link (links in the navbar (e.g. log out) 

*/

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End


if (isUserLogged()) {
$loginmenu = _("Hello").' '.$username.' (<a href="?p=admin" class="navbar-link">'._("Admin").'</a> - <a href="?p=admin&amp;action=logout" class="navbar-link">'._("Log out").'</a>)';
} else {
	//standard login menu item (replaced with the authenticated one if logged in
	$loginmenu = '<a href="?p=admin" class="navbar-link">'._("Admin").'</a>';
}

if(($theme_file_contents = file_get_contents($theme_path."index.htm")) === FALSE) {
	echo "<p class=\"error\">"._("Failed to open theme file")."</p>";
	exit;
}

#Replace URLs
$theme_file_contents = str_replace("href=\"style/", "href=\"".$theme_path."style/", $theme_file_contents); // Replace CSS location

$theme_file_contents = str_replace("src=\"img/", "src=\"".$theme_path."img/", $theme_file_contents); // Replace image location

$theme_file_contents = str_replace("src=\"js/", "src=\"".$theme_path."js/", $theme_file_contents); // Replace js location

$theme_file_contents = str_replace("<param name=movie value=\"", "<param name=movie value=\"".$theme_path, $theme_file_contents); // Replace flash objects IE

$theme_file_contents = str_replace("<embed src=\"", "<embed src=\"".$theme_path, $theme_file_contents); // Replace flash objects embed



####### INCLUDE PHP FUNCTIONS SPECIFIED IN THE THEME (functions.php)
if (file_exists($theme_path."functions.php")) {
	include ($theme_path."functions.php");
}	
####### END INCLUDE PHP FUNCTIONS


#########################
# SET PAGE TITLE

$page_title_prefix = NULL;

//Show category name
if (isset($_GET['cat']) AND $_GET['cat'] != "all" AND !isset($_GET['action'])) {
	$existingCategories = readPodcastCategories ($absoluteurl);
	if (isset($existingCategories[avoidXSS($_GET['cat'])])) {
		//URL depuration (avoidXSS)
		$page_title_prefix .= $existingCategories[avoidXSS($_GET['cat'])]." - ";
		}	
	}
	//Show a generic "All episodes"
	elseif (isset($_GET['p']) AND $_GET['p']=="archive") {
		$page_title_prefix .= _("All Episodes")." - ";
	}
	
	
	//if is single episode, add title of episode to title of page
	elseif (isset($_GET['name'])) {
	
		$titleOfEpisode = showSingleEpisode(avoidXSS($_GET['name']),1); //the last parameter (1) requires just the title to that function
		
		if ($titleOfEpisode != NULL) $page_title_prefix .= "$titleOfEpisode - ";
	
	}
	

$page_title = $page_title_prefix.$podcast_title;
	
$theme_file_contents = str_replace("-----PG_PAGETITLE-----", $page_title, $theme_file_contents);  


include($absoluteurl."core/admin/loadjavascripts.php");

$theme_file_contents = str_replace("-----PG_JSLOAD-----", $loadjavascripts, $theme_file_contents); 

###############################
###############################



//LOAD A CSS WITH CLASSES COMMON TO ALL THE THEMES
$commonCSSurl = '<link href="themes/common.css" rel="stylesheet">';
//ADDING FONT AWESOME, FOR AWESOME ICONS
$commonCSSurl .= '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">';
$theme_file_contents = str_replace("-----PG_COMMONCSSLOAD-----", $commonCSSurl, $theme_file_contents); 





# SET PODCAST FEED URL

if (isset($feed_URL_replace) AND $feed_URL_replace != "") {
$podcastFeedURL = $feed_URL_replace;
$podcastFeedURLiTunes = str_replace("http://", "itpc://", $podcastFeedURL); 
} else {
$podcastFeedURL = $url.$feed_dir.'feed.xml';
$podcastFeedURLiTunes = str_replace("http://", "itpc://", $podcastFeedURL);
}

$rightboxcontent = '<div class="rightbox">

	<span class="nav-header">'.$podcast_title.' '._("feed").'</span>
	<p>'._("Copy the feed link and paste it into your aggregator").'<br /><br />
	<a href="'.$podcastFeedURL.'"><img src="rss-podcast.gif" alt="'._("Copy the feed link and paste it into your aggregator").'" title="'._("Copy the feed link and paste it into your aggregator").'" border="0" /></a>
	</p>
	<p>'._("Subscribe to this podcast with iTunes").'<br /><br /><a href="'.$podcastFeedURLiTunes.'"><img src="podcast_itunes.jpg" alt="'._("Subscribe to this podcast with iTunes").'" title="'._("Subscribe to this podcast with iTunes").'" border="0" /></a></p>


	</div>';

# If you are logged show right boxes
$adminrightboxcontent = NULL;
if(isUserLogged()) { //if admin page

	//show donation box after 3 days from installation
	if (isset($first_installation) and time()-$first_installation>259200) {//259200 seconds = 3 days
	
		if (isset($author_name) and $author_name != NULL) $nameToAddressUser = $author_name.", ";
		else $nameToAddressUser = NULL;
		
		$adminrightboxcontent .= '
			<div class="rightbox">
			<span class="nav-header">'._("Support Podcast Generator").'</span>
			<p>'.$nameToAddressUser._("if you like Podcast Generator please consider").' <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=K6KLDE3KPP6VN" target="_blank"><strong>'._(" making a donation").'</strong></a>.
			'._("No matter the amount you donate, your contribution will support future development and bug fixes. Thank you!").'</p>
			<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=K6KLDE3KPP6VN" target="_blank">
			<i class="fa fa-cc-paypal fa-2x"></i> <i class="fa fa-cc-visa fa-2x"></i> <i class="fa fa-cc-mastercard fa-2x"></i> <i class="fa fa-cc-amex fa-2x"></i>
			</a>
			</div>
		';
	}
	
	
	//show PG box
	$adminrightboxcontent .= '
		<div class="rightbox">
		<span class="nav-header">'._("Help").'</span>
		<ul>
		<li><a href="?p=admin&amp;do=serverinfo">'._("Your server configuration").'</a></li>
		<li><a href="http://podcastgen.sourceforge.net/checkforupdates.php?v='.$podcastgen_version.'" target="_blank">'._("Check for updates").'</a></li>
		<li><a href="http://podcastgen.sourceforge.net/documentation/#faq?ref=local-admin" target="_blank">'._("Read Documentation").'</li>
		<li><a href="http://podcastgen.sourceforge.net/support/?ref=local-admin" target="_blank">'._("Get Support").'</a></li>
		</ul>
		</div>
	';
	
}

$theme_file_contents = str_replace("-----PG_RIGHTBOX-----", $rightboxcontent, $theme_file_contents);

$theme_file_contents = str_replace("-----PG2_ADMINRIGHTBOX-----", $adminrightboxcontent, $theme_file_contents); 


# SET RIGHT OPTIONAL BOX ("freebox")

$freeboxcontent = NULL;
	if ($freebox == "yes") { //if admin is logged do not display freebox - and freebox is enabled

		if(file_exists($absoluteurl."freebox-content.txt")){

			$freeboxcontenttodisplay = file_get_contents($absoluteurl."freebox-content.txt");
						
			$freeboxcontent = '<div class="rightbox">
				'.$freeboxcontenttodisplay.'
				</div>';
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
	
	
	$theme_file_contents = str_replace("-----PG2_URLRSSFEED-----", $podcastFeedURL, $theme_file_contents); 
	
	$theme_file_contents = str_replace("-----PG2_URLFORITUNES-----", $podcastFeedURLiTunes, $theme_file_contents); 

	
	
#### MENU TOP
// Replace menu top (class active assigned to the active menu)

//home button
$contentmenuhome = '<li';
if (isset($_GET['p']) and $_GET['p'] == "home") $contentmenuhome .= ' class="active"';
$contentmenuhome .= '><a href="?p=home">'._("Home").'</a></li>';

$theme_file_contents = str_replace("-----PG_MENUHOME-----", $contentmenuhome, $theme_file_contents);

// end home button




//archive button
$contentmenuarchive = NULL; //DEFINE VARIABLE


if ($categoriesenabled == "yes") { //if categories are enabled

	$contentmenuarchive .= '
		<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">'._("Archive").' <b class="caret"></b></a>
					<ul class="dropdown-menu">';
			

			// READ THE CATEGORIES
	$existingCategories = readPodcastCategories ($absoluteurl);
	//var_dump($existingCategories); //Debug
			
		ksort($existingCategories);	//sort array by key alphabetically
		
		for ($i = 0; $i <  count($existingCategories); $i++) {
		$key=key($existingCategories);
		$val=$existingCategories[$key];
			if ($val<> ' ') {
			   $contentmenuarchive .= '<li><a href="?p=archive&amp;cat='.$key.'">'.$val.'</a></li>';
			}
		 next($existingCategories);
		}
		// END - READ THE CATEGORIES
			
	 $contentmenuarchive .= '
	 <li class="divider"></li>
	 <li><a href="?p=archive&amp;cat=all">'._("All Episodes").'</a></li>
	 </ul></li>';


} else {
$contentmenuarchive = '<li';
if (isset($_GET['p']) and $_GET['p'] == "archive") $contentmenuarchive .= ' class="active"';
$contentmenuarchive .= '><a href="?p=archive">'._("All Episodes").'</a></li>';
}
 
 

$theme_file_contents = str_replace("-----PG_MENUARCHIVE-----", $contentmenuarchive, $theme_file_contents);

// end home button



//	$theme_file_contents = str_replace("-----PG_MENUARCHIVE-----", _("Podcast Archive"), $theme_file_contents); 
	
	

	$theme_file_contents = str_replace("-----PG_MENUADMIN-----", $loginmenu, $theme_file_contents); 

	#FOOTER

	$definefooter = _("Powered by").' <a href="http://podcastgen.sourceforge.net" title="'._("Podcast Generator")._(", an open source podcast publishing solution").'">'._("Podcast Generator").'</a>'._(", an open source podcast publishing solution");

	$theme_file_contents = str_replace("-----PG_FOOTER-----", $definefooter, $theme_file_contents);


	#########################
	# META TAGS AND FEED LINK

	//meta tags
	//new meta tags HTML5 - deleted the obsolete
	$metatagstoreplace = '
		<meta name="Generator" content="Podcast Generator '.$podcastgen_version.'" />
		<meta name="Author" content="'.depuratecontent($author_name).'" />
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
		<link href="'.$podcastFeedURL.'" rel="alternate" type="application/rss+xml" title="'.$podcast_title.' RSS" />';
		
		
		
	//CUSTOMIZE THE PAGES DEDICATED TO SINGLE EPISODES (with dedicated meta tags to increase SEO)
	
	//reconstruct the full URL of the episode
			
			if (isset($_GET['name'])) {
			
			$episodeURLreconstructed = $url.'?name='.avoidXSS($_GET['name']);
			
			// then ADD SOME OPEN GRAPH META TAGS
			$metatagstoreplace .= '
			<meta property="og:title" content="'.$titleOfEpisode.' &laquo; '.$podcast_title.'"/>
			<meta property="og:url" content="'.$episodeURLreconstructed.'"/>
			';
	
			// and the canonical link
			$metatagstoreplace .= '
			<link rel="canonical" href="'.$episodeURLreconstructed.'" />
			';
			} 
			
			else { //IF IS HOME PAGE
			
			$metatagstoreplace .= '
			<meta property="og:title" content="'.$podcast_title.'"/>
			<meta property="og:url" content="'.$url.'"/>
			<meta property="og:image" content="'.$url.$img_dir.'itunes_image.jpg"/>
			';
	
			// and the canonical link
			$metatagstoreplace .= '
			<link rel="canonical" href="'.$url.'" />
			';
			} 
			

	$theme_file_contents = str_replace("-----PG_METATAGS-----", $metatagstoreplace, $theme_file_contents);

	# END META TAGS DEFINITION
	#########################

	
	
	
	
//INCLUDE LOADING INDICATOR IN ALL THE PAGES 
//The indicator is included in all the pages just before closing the tag </html> so we are sure it is outside others relative containers (e.g. bootstrap theme). Otherwise it would be displayed relative to the main container, not to the body

if (isset($_GET['p']) AND $_GET['p']=="admin") { //all admin pages included login
		
//NB the closing body tag has been added below!
$loading_indicator_code = '
<div id="status_notification">Uploading...</div>
</body>
';
			
	$theme_file_contents = str_replace("</body>", $loading_indicator_code, $theme_file_contents);
	
}
	
//END - INCLUDE LOADING INDICATOR IN ALL THE PAGES 

	?>