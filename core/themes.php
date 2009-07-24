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

if(($theme_file_contents = file_get_contents($theme_path."index.htm")) === FALSE) {
	echo "<p class=\"error\">".$L_failedopentheme."</p>";
	exit;
}

#Replace URLs
$theme_file_contents = eregi_replace("href=\"style/", "href=\"".$theme_path."style/", $theme_file_contents); // Replace CSS location

$theme_file_contents = eregi_replace("src=\"img/", "src=\"".$theme_path."img/", $theme_file_contents); // Replace image location

$theme_file_contents = eregi_replace("<param name=movie value=\"", "<param name=movie value=\"".$theme_path, $theme_file_contents); // Replace flash objects IE

$theme_file_contents = eregi_replace("<embed src=\"", "<embed src=\"".$theme_path, $theme_file_contents); // Replace flash objects embed



####### INCLUDE PHP FUNCTIONS SPECIFIED IN THE THEME (funcions.php)
if (file_exists($theme_path."functions.php")) {
	include ($theme_path."functions.php");
}	
####### END INCLUDE PHP FUNCTIONS


#########################
# SET PAGE TITLE
$page_title = $podcast_title; 

if (isset($_GET['p'])) {

	if ($_GET['p']=="archive") {
		$page_title .= " - $L_menu_allpodcasts";

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


		}	
		#########		
	}
	elseif ($_GET['p']=="episode" AND isset($episode_present) AND $episode_present == "yes") {

		$page_title .= " - $text_title";
	}
}

$theme_file_contents = eregi_replace("-----PG_PAGETITLE-----", $page_title, $theme_file_contents);  

###############################
# LOAD JAVASCRIPTS IN THE HEADER IF PAGE REQUIRES - REPLACES "-----PG_JSLOAD-----" IN THE HEADER OF THE THEME PAGE

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

	$loadjavascripts = ""; //null

}

$theme_file_contents = eregi_replace("-----PG_JSLOAD-----", $loadjavascripts, $theme_file_contents); 

###############################
###############################



# SET RIGHT BOX

$urlforitunes = eregi_replace("http://", "itpc://", $url); 

$rightboxcontent = '<div class="rightbox">

	<b>'.$podcast_title.' '.$L_boxdx_feed.'</b>
	<p>'.$L_boxdx_copylink.'<br /><br />
	<a href="'.$url.$feed_dir.'feed.xml"><img src="rss-podcast.gif" alt="'.$L_boxdx_copylink.'" title="'.$L_boxdx_copylink.'" border="0" /></a>
	</p>
	<p>'.$L_boxdx_subitunes.'<br /><br /><a href="'.$urlforitunes.$feed_dir.'feed.xml"><img src="podcast_itunes.jpg" alt="'.$L_boxdx_subitunes.'" title="'.$L_boxdx_subitunes.'" border="0" /></a></p>


	</div>';

# If you are logged show right boxes

if(isset($amilogged) AND $amilogged =="true") { //if logged

	//show donation box
	$rightboxcontent .= '<div class="rightbox">
		<b>'.$L_donatebox.'</b><p>'.$L_admin_donation.'<br /><br />
		<a href="http://www.podcastgenerator.net/donation.php"><img src="project-support.jpg" title="'.$L_admin_donation1.'" alt="'.$L_admin_donation1.'" width="88" height="32" border="0" /></a></p>
	</div>';

	//show PG box
	$rightboxcontent .= '<div class="rightbox">
		<b>'.$L_podcast_generator.'</b><br /><p>- <a href="?p=admin&do=serverinfo">'.$L_serverconf.'</a><br />- <a href="http://podcastgen.sourceforge.net/checkforupdates.php?v='.$podcastgen_version.'" target="_blank">'.$L_checkforupdates.'</a><br />- <a href="http://feeds.podcastgenerator.net/podcastgenerator" target="_blank">'.$L_subscribenewsfeed.'</a><br />- <a href="http://podcastgen.sourceforge.net/documentation.php?ref=local-admin" target="_blank">'.$L_readdocgetsupport.'</a><br />- <a href="http://podcastgen.sourceforge.net/credits.php?ref=local-admin" target="_blank">'.$L_credits.'</a></p>
	</div>';

}

$theme_file_contents = eregi_replace("-----PG_RIGHTBOX-----", $rightboxcontent, $theme_file_contents); 


# SET RIGHT OPTIONAL BOX ("freebox")

if (isset($amilogged) AND $amilogged =="true") { //if you are logged do not display freebox

	$freeboxcontent = NULL;

	$theme_file_contents = eregi_replace("-----PG_FREEBOX-----", $freeboxcontent, $theme_file_contents);

	} elseif($freebox == "yes") {

		if(file_exists("$absoluteurl"."freebox-content.txt")){

			$freeboxcontenttodisplay = file_get_contents("$absoluteurl"."freebox-content.txt");	

			$freeboxcontent = "<div class=\"rightbox\">
				$freeboxcontenttodisplay
				</div>";
		} else {
			$freeboxcontent = NULL;
		}

		$theme_file_contents = eregi_replace("-----PG_FREEBOX-----", $freeboxcontent, $theme_file_contents); 

	} else {

		$freeboxcontent = NULL;

		$theme_file_contents = eregi_replace("-----PG_FREEBOX-----", $freeboxcontent, $theme_file_contents); 		

	}


	# Othere Theme elements replacing
	$theme_file_contents = eregi_replace("-----PG_MAINBODY-----", $PG_mainbody, $theme_file_contents);

	$theme_file_contents = eregi_replace("-----PG_PAGECHARSET-----", $feed_encoding, $theme_file_contents); 

	$theme_file_contents = eregi_replace("-----PG_PODCASTTITLE-----", $podcast_title, $theme_file_contents);

	$theme_file_contents = eregi_replace("-----PG_PODCASTSUBTITLE-----", $podcast_subtitle, $theme_file_contents);

	$theme_file_contents = eregi_replace("-----PG_PODCASTDESC-----", $podcast_description, $theme_file_contents); 

	$theme_file_contents = eregi_replace("-----PG_MENUHOME-----", $L_menu_home, $theme_file_contents); 

	$theme_file_contents = eregi_replace("-----PG_MENUARCHIVE-----", $L_menu_allpodcasts, $theme_file_contents); 

	$theme_file_contents = eregi_replace("-----PG_MENUADMIN-----", $L_menu_admin, $theme_file_contents); 

	#FOOTER

	$definefooter = $L_footer_poweredby.' <a href="http://podcastgen.sourceforge.net" title="'.$L_podcast_generator.$L_footer_pgdesc.'">'.$L_podcast_generator.'</a>'.$L_footer_pgdesc;

	$theme_file_contents = eregi_replace("-----PG_FOOTER-----", $definefooter, $theme_file_contents);


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

	$theme_file_contents = eregi_replace("-----PG_METATAGS-----", $metatagstoreplace, $theme_file_contents);

	# END META TAGS DEFINITION
	#########################


	?>