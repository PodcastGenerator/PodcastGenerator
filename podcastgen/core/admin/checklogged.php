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

} else {

	$amilogged = "false";

}

?>