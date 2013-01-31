<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

$amilogged = NULL; //reset variable for security reasons

// check if user is already logged in
if(isset($_SESSION["user_session"]) AND $_SESSION["user_session"]==$username AND md5($_SESSION["password_session"])==$userpassword) {

	$amilogged = "true";
	$loginmenu = _("Hello").' '.$username.' (<a href="?p=admin" class="navbar-link">'._("Admin").'</a> - <a href="?p=admin&action=logout" class="navbar-link">'._("Log out").'</a>)';
} else {

	$amilogged = "false";
	//standard login menu item (replaced with the authenticated one if logged in
	$loginmenu = '<a href="?p=admin" class="navbar-link">'._("Admin").'</a>';
}



?>