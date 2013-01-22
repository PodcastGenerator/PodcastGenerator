<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('set_path.php'); //define URL and absolute path on the server
include ('../core/admin/VERSION.php'); //define Podcast Generator Version


if (isset($_POST['setuplanguage'])) { //lang is posted in step1
	$scriptlang = $_POST['setuplanguage'];
}
include($absoluteurl."core/language.php");

include ("checkconfigexistence.php");



/*
################ LAGUAGES
if (isset($_POST['setuplanguage'])) {

	$setuplang = $_POST['setuplanguage'];	
	//	echo "lang/setup_".$setuplang;

	if (file_exists("lang/setup_".$setuplang.".php")) {
		include ("lang/setup_".$setuplang.".php");
	}
	

}

*/



################ 

$setuptext = _("Podcast Generator")." ".$podcastgen_version;

?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $setuptext." "._("Setup"); ?></title>
<meta name="Description" content="<?php echo $setuptext." "._("Setup") ?>" />

<META NAME="ROBOTS" CONTENT="NOINDEX,FOLLOW" />

	<link rel="stylesheet" href="style/style.css" type="text/css" />

	</head>

	<body>



	<?php

if (!isset($_GET['step'])){

	echo "Step 1/3";
echo "<p>"._("Welcome to")." ".$setuptext."</p>";
echo "<p>"._("You are just 3 steps away to setup your podcast!")."</p>"; 

}
elseif (isset($_GET['step']) AND $_GET['step'] == 2) {

	echo "Step 2/3";

}

elseif (isset($_GET['step']) AND $_GET['step'] == 3) {

	echo "Step 3/3";

}

elseif (isset($_GET['step']) AND $_GET['step'] == 4) {

//	echo "Step 4/5";

}


?>






	


	<?php
########## INCLUDE INSTALLATION STEPS

if (!isset($_GET['step'])) {
	include ('step1.php');
	} elseif (isset($_GET['step']) AND $_GET['step'] == 2) {

		include ('step2.php');

		}	elseif (isset($_GET['step']) AND $_GET['step'] == 3) {

			include ('step3.php');

			} 	elseif (isset($_GET['step']) AND $_GET['step'] == 4) {

				include ('step4.php');

			}
			elseif (isset($_GET['step']) AND $_GET['step'] == 5) {

				include ('step5.php');

			}

			?>



				</body>

				</html>