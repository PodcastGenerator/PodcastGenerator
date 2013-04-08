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

//include ("$absoluteurl"."components/loading_indicator/loading.js"); //include top right loading indicator





// define login form
$loginform ='
	<br /><br />
	<form id="login" action="?p=admin" method="post">
	<label for="user">'._("User").'</label><br />
	<input type="text" id="user" name="user" size="20" maxlength="255"><br /><br />
	<label for="password">'._("Password").'</label><br />
	<input type="password" id="password" name="password" size="20" maxlength="255"><br /><br />
	<input type="submit" value="'._("Log in").'" onClick="showNotify(\''._("Logging in...").'\');">';


// logout section
if(isset($_GET['action']) AND $_GET['action'] == "logout" ){

	$action = $_GET['action'];

	//session_start();

	session_unset();

	session_destroy();

}
// end logout section 


// check if user is already logged in 

if(isset($_SESSION["user_session"]) AND $_SESSION["user_session"]==$username AND md5($_SESSION["password_session"])==$userpassword){ //if so, keep displaying the page


if (!useNewThemeEngine($theme_path)) { //if is not new theme engine

//write in the body the login / logout pointers
	$PG_mainbody .= '<div class="episode">'._("Hello").' '.$username.' ';
	if (isset($_GET['do']) AND $_GET['do'] != NULL) { //if we are in admin area and an action is performed
		$PG_mainbody .= '(<a href="?p=admin">'._("Back to Admin").'</a> - <a href="?p=admin&amp;action=logout">'._("Log out").'</a>)';
	} else {$PG_mainbody .= '(<a href="?p=admin&amp;action=logout">'._("Log out").'</a>)';}
	$PG_mainbody .= '<br /><br /></div>';
}
	
	
}else{

	if(isset($_POST["user"]) AND $_POST["user"]==$username AND isset($_POST["password"]) AND md5($_POST["password"])==$userpassword){ //if user and pwd are valid

	if (!useNewThemeEngine($theme_path)) { //if is not new theme engine
		$PG_mainbody .= '<div class="episode">
			'._("Hello").' '.$username.' (<a href="?p=admin&amp;action=logout">'._("Log out").'</a>)
			<br /><br />
			</div>';
	}

		$_SESSION["user_session"] = $_POST["user"];
		$_SESSION["password_session"] = $_POST["password"];

	}else{

		if(isset($_POST["user"]) AND isset($_POST["password"])) { //if user and pwd are not correct

			//display AGAIN login form if usr/pwd not correct


			$PG_mainbody .= '
				<div class="topseparator">
				<b>'._("Username or password not valid. Please try again...").'</b>
				'.$loginform.'
				</div>
				</form>';


		}else {


			//display login form

			$PG_mainbody .= '
				<div class="topseparator">
				<b>'._("Log in").'</b>
				'.$loginform.'
				</div>

				</form>
				';

		}

	}		
}	

?>