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


// DEFAULT freebox content showing the potential
$texttowrite = '<p><strong>'._('This is FREEBOX').'</strong></p><p><img src=images/smiley_default_freebox.png alt="Smiley face" style="float:left;margin-right:5px;" />'._('From the admin area you can customize this box. For instance you can add:').'</p><ul><li><a href="http://podcastgen.sourceforge.net" target="_blank">'._('Hyperlinks').'</a></li><li><span style="font-size:0.8em;color:#666;letter-spacing:5px;">Formatted Text</span></li><li>Widgets/Buttons<br/>
<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FPodcast-Generator%2F399742720074553&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe>
</li></ul><p>'._('and so on...').'<br />'._('You can optionally disable the freebox if you don\'t need it.</p>');
//$texttowrite = htmlspecialchars($texttowrite);
//$texttowrite = depurateContent($texttowrite);


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