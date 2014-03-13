<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ("checkconfigexistence.php");

include ('set_path.php'); //define URL and absolute path on the server
include ('../core/admin/VERSION.php'); //define Podcast Generator Version

include($absoluteurl."core/language.php");

include($absoluteurl."core/functions.php");

################ 

$setuptext = _("Podcast Generator")." ".$podcastgen_version." "._("Setup");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $setuptext." "._("Setup"); ?></title>
	<META NAME="ROBOTS" CONTENT="NOINDEX,FOLLOW" />
	<link rel="stylesheet" href="style/style.css" type="text/css" />
	</head>

	<body>


<div class="container"> <!-- OPEN CONTAINER -->


<h1><?php echo $setuptext; ?></h1>


<?php

if (!isset($_GET['step'])){

	echo "<span class=\"nav\">"._("Step")." 1/3</span>";
echo "<p>"._("Howdy! You are just 3 steps away to setup your podcast...")."</p>"; 

}
elseif (isset($_GET['step']) AND $_GET['step'] == 2) { echo "<span class=\"nav\">"._("Step")." 2/3</span>";}

elseif (isset($_GET['step']) AND $_GET['step'] == 3) { echo "<span class=\"nav\">"._("Step")." 3/3</span>";}

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


?>

</div> <!-- CLOSE CONTAINER -->
</body>
</html>