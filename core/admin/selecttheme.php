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
if(isset($amilogged) AND $amilogged =="true") {


	$PG_mainbody .= '<h3>'.$L_selecttheme.'</h3>';


	if (isset($_POST['themedir']) AND $_POST['themedir'] != NULL) { 


		$new_theme_path = 'themes/'.$_POST['themedir'].'/'; // new theme

		if ($new_theme_path != $theme_path) { // theme is different

			$theme_path = $new_theme_path;

			include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

			$PG_mainbody .= '<p>'.$L_themechanged.'</p>
				<p><a href="?p=admin&do=theme">'.$L_tryanothertheme.'</a></p>';
		}
		else { // if theme is already in use

			$PG_mainbody .= '<p>'.$L_themeinuse.'</p>
				<p><a href="?p=admin&do=theme">'.$L_tryanothertheme.'</a></p>';

		}

	} 
	else {

		$PG_mainbody .= '

			<p>'.$L_selectpgtheme.'</p>
			<form name="'.$L_selecttheme.'" method="POST" enctype="multipart/form-data" action="?p=admin&do=theme">

			<select name="themedir">';

		$dir = "$absoluteurl"."themes/";

		$dirHandle = opendir($dir);
		// $count = -1;
		$returnstr = "";
		while ($themedir = readdir($dirHandle)) {


			if(!is_dir($themedir) AND $themedir != '..' AND $themedir != '.' AND $themedir != 'index.htm' AND $themedir != '_vti_cnf' AND $themedir != '.DS_Store') {

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

			<input type="submit" name="'.$L_change.'" value="'.$L_change.'" onClick="showNotify(\''.$L_setting.'\');">

			';

		closedir($dirHandle);

	} 

	$PG_mainbody .= '<br /><br /><div class="topseparator">
		<span class="admin_hints">'.$L_howtocreatetheme.' <a href="http://podcastgen.sourceforge.net/documentation.php#createtheme" target="_blank">'.$L_seedocumentation.'</a></span>
	</div>
		';

}

?>