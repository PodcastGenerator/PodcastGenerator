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

// check if user is logged in
if (!isUserLogged()) { exit; }

// var_dump($_POST['categories']);
$categories = array();
foreach ($_POST['categories'] as $key => $category) {
	
	$id = stripslashes($category["id"]);
	$id = htmlspecialchars($id);
	$id = depurateContent($id);

	$title = stripslashes($category["title"]);
	$title = htmlspecialchars($title);
	$title = depurateContent($title);

	$description = stripslashes($category["description"]);
	$description = htmlspecialchars($description);
	$description = depurateContent($description);

	$categories[$id] = array("id" => $id,"title" => $title, "description" => $description );
}

var_dump($categories);
// exit;

if (true) { /// 000


	$xmlfiletocreate = '<?xml version="1.0" encoding="'.$feed_encoding.'"?>
	<PodcastGenerator>';

	foreach ($categories as $key => $category) {


		$xmlfiletocreate .= '
			<category>
			<id>'.$category["id"].'</id>
			<title>'.$category["title"].'</title>
			<description>'.$category["description"].'</description>
			</category>';
	}


	$xmlfiletocreate .= '
		</PodcastGenerator>';

	/////////////////////
	// WRITE THE XML FILE
	$fp = fopen("categories.xml",'w+'); //open desc file or create it

	fwrite($fp,$xmlfiletocreate);

	fclose($fp);

	// $PG_mainbody .= '<p>'._("New category:").' <i>'.$val.'</i></p>';

	$PG_mainbody .= '<p><b>'._("Categories updated!").'</b></p><p><a href="?p=admin&do=categories">'._("Back to category management").'</a>';

} // 001 end 



?>