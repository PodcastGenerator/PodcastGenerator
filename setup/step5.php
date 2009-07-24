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


	<ul class="episode_imgdesc">
	<li>

	<?php

$PG_mainbody = NULL; //define

$user = $_POST['username'];
$pwd = $_POST['password'];
$pwd2 = $_POST['password2'];

if (isset($user) AND $user != "") {

	if (isset($pwd) AND isset($pwd2) AND $pwd == $pwd2) { // IF ALL IS OK

################
##LOAD FUNCTIONS (needs depuratecontent and renamefilestrict)
if (!isset($defined)) include("$absoluteurl"."core/functions.php"); //LOAD ONCE
################

include('firstcreatecategory.php'); //creates categories.xml file in the root dir

include('firstcreatefreeboxtext.php'); //creates freebox-content.txt file in the root dir

		$PG_mainbody .= '<p>'.$SL_configcreation.'</p>';

		include('firstcreateconfig.php'); //creates config.php file in the root dir
		$PG_mainbody .= '<p>'.$SL_complete.'</p>';
		$PG_mainbody .= '<p><a href="../?p=admin"><b>'.$SL_start.'</b></a></p>';
	}
	else { //if pwds not set or don't match

	$PG_mainbody .= '<p>'.$SL_pwdwrong.'</p>
		<form method="post" action="index.php?step=4">
		<input type="button" value="'.$SL_back.'" onClick="history.back()">
		</form>';
}

} else { // if user is not set

	$PG_mainbody .= '<p>'.$SL_nouser.'</p>
		<form method="post" action="index.php?step=4">
		<input type="button" value="'.$SL_back.'" onClick="history.back()">
		</form>';
}



//print output

echo $PG_mainbody;

?>


	</li>
	</ul>