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

$texttowrite = stripslashes(_("FREEBOX: in this box you can write freely what you wish: add links, text, HTML code through a visual editor from the admin section! You can optionally disable this feature if you don't need it..."));
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





######################## EPISODE ATTACHMENT FILE CREATION

//Comment box attachment (just an example)
$first_attachment = '<!-- begin htmlcommentbox.com -->
 <div id="HCB_comment_box"><a href="http://www.htmlcommentbox.com">HTML Comment Box</a> is loading comments...</div>
 <script type="text/javascript" language="javascript" id="hcb"> /*<!--*/ if(!window.hcb_user){hcb_user={};} (function(){s=document.createElement("script");s.setAttribute("type","text/javascript");s.setAttribute("src", "http://www.htmlcommentbox.com/jread?page="+escape((window.hcb_user && hcb_user.PAGE)||(""+window.location)).replace("+","%2B")+"&opts=343&num=10");if (typeof s!="undefined") document.getElementsByTagName("head")[0].appendChild(s);})(); /*-->*/ </script>
<!-- end htmlcommentbox.com -->
';

if (file_exists("../embedded-code.txt")) { //if freebox text is already present

	echo "<font color=\"red\">"._("The embedded code file already exists...")."</font><br />";


} else { // else create "episode-attachment.txt" file in the root dir

// take the localized _("Uncategorized") variable in setup_LANGUAGE, depurate it and generate a unique id to use in the categories.xml file generated

//$first_attachment = htmlspecialchars($first_attachment);
//$first_attachment = depurateContent($first_attachment);


$createtxtbox = fopen("$absoluteurl"."embedded-code.txt",'w'); //create categories file
fwrite($createtxtbox,$first_attachment); //write content into the file
fclose($createtxtbox);

}


######################## END - EPISODE ATTACHMENT FILE CREATION
?>