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



################ LAGUAGES: 1/2
//assigned below in english before language choice, when language has been chosen they will be read in the language files and the below variables "overwritten" (see 2/2)
$SL_pg = "Podcast Generator";
$SL_pgsetup = "- Setup"; 
$SL_welcome = "Welcome!";
$SL_next = "Next";
################ 

################ LAGUAGES: 2/2
if (isset($_POST['setuplanguage'])) {

	$setuplang = $_POST['setuplanguage'];	
	//	echo "lang/setup_".$setuplang;

	if (file_exists("lang/setup_".$setuplang.".php")) {
		include ("lang/setup_".$setuplang.".php");
	}
	

}
################ 

$SL_pgsetuptext = $SL_pg." ".$podcastgen_version." ".$SL_pgsetup;

?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $SL_pgsetuptext;?></title>
<meta name="Description" content="<?php echo $SL_pgsetuptext; ?>" />

<META NAME="ROBOTS" CONTENT="NOINDEX,FOLLOW" />

	<link rel="stylesheet" href="style/style.css" type="text/css" />

	</head>

	<body>

	<div class="container">

	<div class="header">
	<h1 class="headertitle"><?php echo $SL_pgsetuptext; ?></h1>
</div>
	<div class="headermenu">
	<div class="headermenutext">

	<?php

if (!isset($_GET['step'])){

	echo "Step 1/5";


}
elseif (isset($_GET['step']) AND $_GET['step'] == 2) {

	echo "Step 2/5";

}

elseif (isset($_GET['step']) AND $_GET['step'] == 3) {

	echo "Step 3/5";

}

elseif (isset($_GET['step']) AND $_GET['step'] == 4) {

	echo "Step 4/5";

}

elseif (isset($_GET['step']) AND $_GET['step'] == 5) {

	echo "Step 5/5";

}
?>

	</div>

	</div>

	<div class="main">

	<!--
	<div class="rightcolumn">

	<div>

	</div>

	</div>
	-->

	<div class="leftcolumn">

	<div>
	<h2 class="site_welcome"><?php echo $SL_welcome; ?></h2>
<p class="site_desc">
	<?php
if (isset($SL_welcometext) AND $SL_welcometext != NULL) {
	echo $SL_welcometext;
} 

?>	
	</p>
	</div>


	<div class="episode">


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



				</div>


				</div>


				</div>

				<div class="footer">
				<a href="http://podcastgen.sourceforge.net" title="Podcast Generator: open source podcast publishing solution"><img src="img/podcastgen.gif" alt="Podcast Generator: open source podcast publishing solution" class="footerdx" /></a>
			Powered by <a href="http://podcastgen.sourceforge.net" title="Podcast Generator: open source podcast publishing solution">Podcast Generator</a>, an open source podcast publishing solution.

			</div>

				</div>

				</body>

				</html>