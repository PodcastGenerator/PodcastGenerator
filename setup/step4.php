<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('checkconfigexistence.php');
?>



	<?php

$PG_mainbody = NULL; //define

$user = $_POST['username'];
$pwd = $_POST['password'];
$pwd2 = $_POST['password2'];

if (isset($user) AND $user != "") {

	if (isset($pwd) AND isset($pwd2) AND $pwd == $pwd2) { // IF ALL IS OK

################
##LOAD FUNCTIONS (needs depuratecontent and renamefilestrict)
include_once("$absoluteurl"."core/functions.php"); //LOAD ONCE
################
/*
include('firstcreatecategory.php'); //creates categories.xml file in the root dir
include('firstcreatefreeboxtext.php'); //creates freebox-content.txt file in the root dir
*/

		include('firstcreation.php'); //creates categories file, freebox and attachment files

	//	$PG_mainbody .= '<p>'._("Creation of the configuration file...").'</p>';

		include('firstcreateconfig.php'); //creates config.php file in the root dir
		
		$PG_mainbody .= '<p>'._("Installation completed successfully...").'</p>';
		$PG_mainbody .= '<p><a href="../?p=admin"><b>'._("Go right to your podcast!").'</b></a></p>';
	}
	else { //if pwds not set or don't match

	$PG_mainbody .= '<p>'._("You didn't enter a password or the two passwords do not correspond; please go back and type your password again...").'</p>
		<form method="post" action="index.php?step=4">
		<input type="button" value="'._("Back").'" onClick="history.back()">
		</form>';
}

} else { // if user is not set

	$PG_mainbody .= '<p>'._("You didn't enter the username...").'</p>
		<form method="post" action="index.php?step=4">
		<input type="button" value="'._("Back").'" onClick="history.back()">
		</form>';
}



//print output

echo $PG_mainbody;

?>
