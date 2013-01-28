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



######################## FREEBOX FILE CREATION

if (file_exists("../freebox-content.txt")) { //if freebox text is already present

	echo "<font color=\"red\">"._("Freebox text already exists...")."</font><br />";


} else { // else create "freebox-content.txt" file in the root dir

// take the localized _("Uncategorized") variable in setup_LANGUAGE, depurate it and generate a unique id to use in the categories.xml file generated

$texttowrite = stripslashes(_("FREEBOX: use this box as you wish. For instance you can add links and text or embed HTML widgets through a visual editor from the admin section! You can also disable this feature if you don't need it."));
$texttowrite = htmlspecialchars($texttowrite);
$texttowrite = depurateContent($texttowrite);


$createtxtbox = fopen("$absoluteurl"."freebox-content.txt",'w'); //create categories file
fwrite($createtxtbox,$texttowrite); //write content into the file
fclose($createtxtbox);

}

######################## END - FREEBOX FILE CREATION



######################## CATEGORY FILE CREATION


if (file_exists("../categories.xml")) { //if categories already exist stop the script

	echo "<font color=\"red\">"._("Categories file already exists...")."</font><br />";


} else { // else create "categories.xml" file in the root dir

// take the localized _("Uncategorized") variable in setup_LANGUAGE, depurate it and generate a unique id to use in the categories.xml file generated

$idcat = stripslashes(_("Uncategorized")); 
$idcat = htmlspecialchars($idcat);
$idcat = depurateContent($idcat); // category name (external)
$id = renamefilestrict ($idcat); // category id generated (internal)


$categoriesfiletocreate = '<?xml version="1.0" encoding="utf-8"?>
<PodcastGenerator>
	<category>
	<id>'.$id.'</id>
	<description>'.$idcat.'</description>
	</category>
	</PodcastGenerator>';

$createcatf = fopen("$absoluteurl"."categories.xml",'w'); //create categories file
fwrite($createcatf,$categoriesfiletocreate); //write content into the file
fclose($createcatf);

}

######################## END - CATEGORY FILE CREATION



?>