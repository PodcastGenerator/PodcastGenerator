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
if (isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

include ("$absoluteurl"."components/loading_indicator/loading.js"); //include top right loading indicator

// define login form
$loginform ='
	<br /><br />
	<form id="login" action="?p=admin" method="post">
	<label for="user">'.$L_user.'</label><br />
	<input type="text" id="user" name="user" size="20" maxlength="255"><br /><br />
	<label for="password">'.$L_password.'</label><br />
	<input type="password" id="password" name="password" size="20" maxlength="255"><br /><br />
	<input type="submit" value="'.$L_login.'" onClick="showNotify(\''.$L_logginin.'\');">';


// logout section
if(isset($_GET['action']) AND $_GET['action'] == "logout" ){

	$action = $_GET['action'];

	//session_start();

	session_unset();

	session_destroy();

}
// end logout section 


// check if user is already logged in (Thanks to Pavel Urusov for the MD5 password encoding suggestion)

if(isset($_SESSION["user_session"]) AND $_SESSION["user_session"]==$username AND md5($_SESSION["password_session"])==$userpassword){ //if so, keep displaying the page

	$PG_mainbody .= '<div class="episode">
		'.$L_welcome.' <i>'.$username.'</i> ';

	if (isset($_GET['do']) AND $_GET['do'] != NULL) {

		$PG_mainbody .= '(<a href="?p=admin">'.$L_menu_backadmin.'</a> - <a href="?p=admin&action=logout">'.$L_logout.'</a>)';
	}
	else {

		$PG_mainbody .= '(<a href="?p=admin&action=logout">'.$L_logout.'</a>)';

	}

	$PG_mainbody .= '<br /><br />
		</div>';

}else{

	if(isset($_POST["user"]) AND $_POST["user"]==$username AND isset($_POST["password"]) AND md5($_POST["password"])==$userpassword){ //if user and pwd are valid

		$PG_mainbody .= '<div class="episode">
			'.$L_welcome.' <i>'.$username.'</i> (<a href="?p=admin&action=logout">'.$L_logout.'</a>)
			<br /><br />
			</div>';

		$_SESSION["user_session"] = $_POST["user"];
		$_SESSION["password_session"] = $_POST["password"];

	}else{

		if(isset($_POST["user"]) AND isset($_POST["password"])){ //if user and pwd are not correct

			//display AGAIN login form if usr/pwd not correct

			$PG_mainbody .= '
				<div class="topseparator">
				<b>'.$L_notvalid.'</b>
				'.$loginform.'
				</div>
				</form>';


		}else{ 


			//display login form

			$PG_mainbody .= '
				<div class="topseparator">
				<b>'.$L_login.'</b>
				'.$loginform.'
				</div>

				</form>';

		}

	}		
}	
?>