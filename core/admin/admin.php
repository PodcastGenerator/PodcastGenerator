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

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue


	include("$absoluteurl"."core/admin/login.php");

	
	# SET PODCAST FEED URL
	if (isset($feed_URL_replace) AND $feed_URL_replace != "") {
	$podcastFeedURL = $feed_URL_replace;
	} else {
	$podcastFeedURL = $url.$feed_dir.'feed.xml';
	}

	// check if user is already logged in
	if(isUserLogged()) {

		
		if (isset($_GET['do']) AND $_GET['do']=="ftpfeature") {
		
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/ftpfeature.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="generate") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/feedgenerate.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="upload") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/upload.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 

		/*
		elseif (isset($_GET['do']) AND $_GET['do']=="editdel") {

			include("$absoluteurl"."core/admin/editdel.php");
		} 
		*/

		elseif (isset($_GET['do']) AND $_GET['do']=="edit") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/edit.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="delete") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/delete.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="categories") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/categories.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="freebox") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/freebox.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		}
		

		elseif (isset($_GET['do']) AND $_GET['do']=="theme") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/selecttheme.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		}

		elseif (isset($_GET['do']) AND $_GET['do']=="itunesimg") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/itunesimg.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		}

		elseif (isset($_GET['do']) AND $_GET['do']=="itunescat") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/itunescategories.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		}


		elseif (isset($_GET['do']) AND $_GET['do']=="changedetails") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/podcastdetails.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		}
		elseif (isset($_GET['do']) AND $_GET['do']=="config") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/scriptconfig.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		}
		elseif (isset($_GET['do']) AND $_GET['do']=="serverinfo") {
		$PG_mainbody .= '<div class=" episodebox">';
			include("$absoluteurl"."core/admin/server_info.php");
		$PG_mainbody .= '</div>'; //close episodebox 
		} 

	
	
		else {

			if (isset($firsttimehere) AND $firsttimehere == "yes") { // if it's the first time (parameter specified in config.php)

			$PG_mainbody .= "
				<div class=\"topseparator episodebox\"> 
				<h3>"._("Welcome")."</h3>
				<p><i>"._("This is possibly the first time you have entered this page: you haven't changed your podcast details yet. You are reccommended to provide a podcast title, description, etc... Try a different theme!")."</i> <a href=\"?p=admin&amp;do=changedetails\"><b>"._("Start now...")."</b></a></p>
				</div>";	
		}


		$PG_mainbody .= '
			<div class="topseparator episodebox"> 
			<h3>'._("Episodes").'</h3>
			<ul> 
			<li><a href="?p=admin&do=upload">'._("Upload New Episode").'</a></li>
			<li><a href="?p=archive&cat=all&noextras">'._("Edit / Delete Episodes").'</a></li>';
			
			if ($categoriesenabled == "yes") { //if categories are enabled in config.php

			$PG_mainbody .= '
				<li><a href="?p=admin&do=categories">'._("Manage categories").'</a></li>';
		} // end if categories enabled
			
			$PG_mainbody .= '
			<li><a href="?p=admin&do=ftpfeature">'._("FTP Feature").' '._("(Auto Indexing)").'</a></li>
			<li><a href="?p=admin&do=generate">'._("Manually regenerate RSS feed").'</a></li>
			</ul>
			</div>';

		


		$PG_mainbody .= '<div class="topseparator episodebox"> 
			<h3>'._("Themes and aspect").'</h3>
			<ul> 
			<li><a href="?p=admin&do=theme">'._("Change Theme").'</a></li>';
	
//Frebox	
	if ($freebox == "yes") { $PG_mainbody .= '<li><a href="?p=admin&do=freebox">'._("Customize your FreeBox").'</a></li>'; }
			
			$PG_mainbody .= '</ul>
			</div>


			<div class="topseparator episodebox"> 
			<h3>'._("iTunes Store Settings").'</h3>
			<ul> 
			<li><a href="?p=admin&do=itunesimg">'._("Change iTunes Cover Art").'</a></li>
			<li><a href="?p=admin&do=itunescat">'._("Select or change iTunes Categories").'</a></li>
			<li><a href="https://buy.itunes.apple.com/WebObjects/MZFinance.woa/wa/publishPodcast?feedURL='.$podcastFeedURL.'" target="_blank">'._("Submit your podcast to the iTunes Store").'</a></li>
		</ul>
			</div>

			<div class="topseparator episodebox"> 
			<h3>'._("Your podcast details").'</h3>
			<ul> 
			<li><a href="?p=admin&do=changedetails">'._("Change your podcast details").'</a></li>
			<li><a href="http://validator.w3.org/feed/check.cgi?url='.$podcastFeedURL.'" target="_blank">'._("Validate this feed with w3c validation service").'</a></li>
		</ul>
			</div>

			<div class="topseparator episodebox"> 
			<h3>'._("Podcast Generator Configuration").'</h3>
			<ul> 
			<li><a href="?p=admin&do=config">'._("Change Podcast Generator Configuration").'</a></li>
			</ul>
			</div>

			';


		##### Display PodcastGen news

		if ($enablepgnewsinadmin == "yes") { //if display news is enabled in config.php

			$PG_mainbody .= '<div class="topseparator">
				<h3>'._("Podcast Generator News").'</h3>';

			include("$absoluteurl"."core/admin/pgRSSnews.php"); // display the latest RSS news of podcastgen

			$PG_mainbody .= '</div>';
		} // end if rss news enabled
		####


	}
	
	
	
}
}
?>