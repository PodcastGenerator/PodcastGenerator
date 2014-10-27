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

// check if user is already logged in
if(isUserLogged()) {


	$PG_mainbody .= '<h3>'._("Theme Selection").'</h3>';


	if (isset($_POST['themedir']) AND $_POST['themedir'] != NULL) { 


		$new_theme_path = 'themes/'.$_POST['themedir'].'/'; // new theme

		if ($new_theme_path != $theme_path) { // theme is different

			$theme_path = $new_theme_path;

			include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

			$PG_mainbody .= '<p>'._("Theme changed!").'</p>
				<p><a href="?p=admin&do=theme">'._("Try another theme...").'</a></p>';
		}
		else { // if theme is already in use

			$PG_mainbody .= '<p>'._("You are already using this theme").'</p>
				<p><a href="?p=admin&do=theme">'._("Try another theme...").'</a></p>';

		}

	} 
	else {

		$PG_mainbody .= '

			<p>'._("Change Podcast Generator theme and aspect:").'</p>
			<form name="'._("Theme Selection").'" method="POST" enctype="multipart/form-data" action="?p=admin&do=theme">

			<select name="themedir">';

		$dir = "$absoluteurl"."themes/";

		$dirHandle = opendir($dir);
		// $count = -1;
		$returnstr = "";
		while ($themedir = readdir($dirHandle)) {


			if(!is_dir($themedir) AND $themedir != '..' AND $themedir != '.' AND $themedir != 'index.htm' AND $themedir != 'common.css' AND $themedir != '_vti_cnf' AND $themedir != '.DS_Store') {

				//      $count++; //if u want a theme counter
				//      $returnstr .= '&f'.$count.'='.$file;

				$new_theme_path = 'themes/'.$themedir.'/'; //check theme and eventually select it in the form if it is in use

				if ($new_theme_path == $theme_path) { // select current theme in the form
					$PG_mainbody .= "<option value='$themedir' selected>$themedir</option>";
				}
				else {
					$PG_mainbody .= "<option value='$themedir'>$themedir</option>";
				}
			}
		} 

		$PG_mainbody .= '</select><br /><br />

			<input type="submit" name="'._("Change").'" value="'._("Change").'" onClick="showNotify(\''._("Setting...").'\');">

			';

		closedir($dirHandle);

	} 

	$PG_mainbody .= '<br /><br /><div class="topseparator">
		<span class="alert">'._("Hint: How to create your own theme?").' <a href="http://podcastgen.sourceforge.net/documentation/FAQ-themes" target="_blank">'._("See documentation").'</a></span>
	</div>
		';

}

?>