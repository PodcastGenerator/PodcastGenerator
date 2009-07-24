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

	include("$absoluteurl"."core/admin/checklogged.php");


	// check if user is already logged in
	if(isset($amilogged) AND $amilogged =="true") {


		if (isset($_GET['do']) AND $_GET['do']=="ftpfeature") {

			include("$absoluteurl"."core/admin/ftpfeature.php");
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="generate") {

			include("$absoluteurl"."core/admin/feedgenerate.php");
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="upload") {

			include("$absoluteurl"."core/admin/upload.php");
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="editdel") {

			include("$absoluteurl"."core/admin/editdel.php");
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="edit") {

			include("$absoluteurl"."core/admin/edit.php");
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="delete") {

			include("$absoluteurl"."core/admin/delete.php");
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="categories") {

			include("$absoluteurl"."core/admin/categories.php");
		} 

		elseif (isset($_GET['do']) AND $_GET['do']=="freebox") {

			include("$absoluteurl"."core/admin/freebox.php");
		}

		elseif (isset($_GET['do']) AND $_GET['do']=="theme") {

			include("$absoluteurl"."core/admin/selecttheme.php");
		}

		elseif (isset($_GET['do']) AND $_GET['do']=="itunesimg") {

			include("$absoluteurl"."core/admin/itunesimg.php");
		}

		elseif (isset($_GET['do']) AND $_GET['do']=="itunescat") {

			include("$absoluteurl"."core/admin/itunescategories.php");
		}


		elseif (isset($_GET['do']) AND $_GET['do']=="changedetails") {

			include("$absoluteurl"."core/admin/podcastdetails.php");
		}
		elseif (isset($_GET['do']) AND $_GET['do']=="config") {

			include("$absoluteurl"."core/admin/scriptconfig.php");
		}

		elseif (isset($_GET['do']) AND $_GET['do']=="serverinfo") {

			include("$absoluteurl"."core/admin/server_info.php");
		} 

		else {

			if (isset($firsttimehere) AND $firsttimehere == "yes") { // if it's the first time (parameter specified in config.php)

			$PG_mainbody .= '
				<div class="topseparator"> 
				<h3>'.$L_welcome.'</h3>
				<p><i>'.$L_firstadminmsg.'</i> <a href="?p=admin&do=changedetails"><b>'.$L_startnow.'</b></a></p>

				</div>';	
		}


		$PG_mainbody .= '
			<div class="topseparator"> 
			<h3>'.$L_admin_episodes.'</h3>
			<ul> 
			<li><a href="?p=admin&do=upload">'.$L_admin_upload.'</a></li>
			<li><a href="?p=admin&do=editdel">'.$L_admin_editdel.'</a></li>
			<li><a href="?p=admin&do=ftpfeature">'.$L_ftpfeature.'</a></li>
			<li><a href="?p=admin&do=generate">'.$L_admin_genfeed.'</a></li>
			</ul>
			</div>';

		if ($categoriesenabled == "yes") { //if categories are enabled in config.php

			$PG_mainbody .= '<div class="topseparator"> 
				<h3>'.$L_admin_categories.'</h3>
				<ul> 
				<li><a href="?p=admin&do=categories">'.$L_adddel_categories.'</a></li>
				</ul>
				</div>';
		} // end if categories enabled

		if ($freebox == "yes") {

			$PG_mainbody .= '<div class="topseparator"> 
				<h3>'.$L_admin_freebox.'</h3>
				<ul> 
				<li><a href="?p=admin&do=freebox">'.$L_customize_freebox.'</a></li>
				</ul>
				</div>';
		}

		$PG_mainbody .= '<div class="topseparator"> 
			<h3>'.$L_admin_themes.'</h3>
			<ul> 
			<li><a href="?p=admin&do=theme">'.$L_admin_selecttheme.'</a></li>
			</ul>
			</div>


			<div class="topseparator"> 
			<h3>'.$L_admin_itunessettings.'</h3>
			<ul> 
			<li><a href="?p=admin&do=itunesimg">'.$L_change_itunesimage.'</a></li>
			<li><a href="?p=admin&do=itunescat">'.$L_changecat.'</a></li>
			<li><a href="https://phobos.apple.com/WebObjects/MZFinance.woa/wa/publishPodcast?feedURL='.$url.$feed_dir.'feed.xml" target="_blank">'.$L_submit_itunes_store.'</a></li>
		<li><a href="https://phobos.apple.com/WebObjects/MZFinance.woa/wa/pingPodcast?feedURL='.$url.$feed_dir.'feed.xml" target="_blank">'.$L_ping_itunes_store.'</a></li>
		</ul>
			</div>

			<div class="topseparator"> 
			<h3>'.$L_admin_podcastdetails.'</h3>
			<ul> 
			<li><a href="?p=admin&do=changedetails">'.$L_changepodcastdetails.'</a></li>
			<li><a href="http://validator.w3.org/feed/check.cgi?url='.$url.'feed.xml" target="_blank">'.$L_admin_feed_validate.'</a></li>
		</ul>
			</div>

			<div class="topseparator"> 
			<h3>'.$L_pgconfig.'</h3>
			<ul> 
			<li><a href="?p=admin&do=config">'.$L_admin_changeconf.'</a></li>
			</ul>
			</div>


			';


		##### Display PodcastGen news

		if ($enablepgnewsinadmin == "yes") { //if display news is enabled in config.php

			$PG_mainbody .= '<div class="topseparator">
				<h3>'.$L_pgnews.'</h3>';

			include("$absoluteurl"."core/admin/pgRSSnews.php"); // display the latest RSS news of podcastgen

			$PG_mainbody .= '</div>';
		} // end if rss news enabled
		####


	}
}
}
?>