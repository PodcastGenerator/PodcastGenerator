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

// check if user is already logged in
if(isset($amilogged) AND $amilogged =="true") {

	$PG_mainbody .= '<h3>'.$L_changepodcastdetails.'</h3>
		<p><span class="admin_hints">'.$L_podcastdetailshints.'</span></p>';

	if (isset($_GET['action']) AND $_GET['action']=="change") { // if action is set

		//title
		$title = $_POST['title'];
		if ($title != "") {
			$title = stripslashes($title);
			$title = strip_tags($title);
			$title = htmlspecialchars($title);
			$title = depurateContent($title);
			$podcast_title = $title;
		}else{
			$PG_mainbody .= '<p>'.$L_podcastitle.' '.$L_empty.' '.$L_ignored.'</p>';	
		}

		// subtitle
		$subtitle = $_POST['subtitle'];
		if ($subtitle != "") {
			$subtitle = stripslashes($subtitle);
			$subtitle = strip_tags($subtitle);
			$subtitle = htmlspecialchars($subtitle);
			$subtitle = depurateContent($subtitle);
			$podcast_subtitle = $subtitle;
		}else{
			$PG_mainbody .= '<p>'.$L_podcastsubtitle.' '.$L_empty.' '.$L_ignored.'</p>';	
		}

		// description
		$description = $_POST['description'];
		if ($description != "") {
			$descmax =4000; #set max characters variable. iTunes specifications by Apple say "max 4000 characters" for itunes:summary tag

			if (strlen($description)<$descmax) { // (if long description IS NOT too long

				$description = stripslashes($description);
				$description = strip_tags($description);
				$description = htmlspecialchars($description);
				$description = depurateContent($description);
				$podcast_description = $description;

			}else { //if description is more than max characters allowed

				$PG_mainbody .= "<p>$L_podcastdesctoolong<br />$L_max $descmax $L_characters - $L_actualenght ".strlen($description)." $L_characters.</p>";

			} // end of description lenght checking
		}else{
			$PG_mainbody .= '<p>'.$L_podcastdesc.' '.$L_empty.' '.$L_ignored.'</p>';	
		}



		// copyright
		$copyright_notice = $_POST['copyright_notice'];
		if ($copyright_notice != "") {
			$copyright_notice = stripslashes($copyright_notice);
			$copyright_notice = strip_tags($copyright_notice);
			$copyright_notice = htmlspecialchars($copyright_notice);
			$copyright_notice = depurateContent($copyright_notice);
			$copyright = $copyright_notice;
		}else{
			$PG_mainbody .= '<p>'.$L_copyrightnotice.' '.$L_empty.' '.$L_ignored.'</p>';	
		}

		// author's name
		$authorname = $_POST['authorname'];

		if ($authorname != "") {
			$authorname = stripslashes($authorname);
			$authorname = strip_tags($authorname);
			$authorname = htmlspecialchars($authorname);
			$authorname = depurateContent($authorname);
			$author_name = $authorname;
		}else{
			$PG_mainbody .= '<p>'.$L_authorname.' '.$L_empty.' '.$L_ignored.'</p>';	
		}

		// author's email
		$authoremail = $_POST['authoremail'];

		$authoremail = stripslashes($authoremail);
		$authoremail = strip_tags($authoremail);
		$authoremail = htmlspecialchars($authoremail);
		$authoremail = depurateContent($authoremail);

		if (validate_email($authoremail)) { //if email is valid

			$author_email = $authoremail;
		}
		else{ // if email not valid
			$PG_mainbody .= '<p>'.$L_noauthemail.' '.$L_ignored.'</p>';	
		}


		//feed language
		$feedlanguage = $_POST['feedlanguage'];
		$feed_language = $feedlanguage;

		//explicit
		$explicit = $_POST['explicit'];
		$explicit_podcast = $explicit;


		include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

		$PG_mainbody .= '<p><b>'.$L_informationsent.'</b></p>';

		//REGENERATE FEED ...
		include ("$absoluteurl"."core/admin/feedgenerate.php");
		$PG_mainbody .= '<br /><br />';
	}
	else { // if action not set


		$PG_mainbody .=	'<form name="podcastdetails" method="POST" enctype="multipart/form-data" action="?p=admin&do=changedetails&action=change">';

		$PG_mainbody .=	'<br /><br />
			<p><label for="title"><b>'.$L_podcastitle.'</b></label></p>
			<input name="title" type="text" id="title" size="50" maxlength="255" value="'.$podcast_title.'">
			<br /><br />
			<p><label for="subtitle"><b>'.$L_podcastsubtitle.'</b></label></p>
			<input name="subtitle" type="text" id="title" size="50" maxlength="255" value="'.$podcast_subtitle.'">
			<br /><br />
			<p><label for="description"><b>'.$L_podcastdesc.'</b></label></p>
			<textarea name="description" cols="50" rows="3">'.$podcast_description.'</textarea>	
			<br /><br />
			<p><label for="copyright_notice"><b>'.$L_copyrightnotice.'</b></label></p>
			<input name="copyright_notice" type="text" id="title" size="50" maxlength="255" value="'.$copyright.'">	
			<br /><br />
			<p><label for="authorname"><b>'.$L_authorname.'</b></label></p>
			<input name="authorname" type="text" id="title" size="50" maxlength="255" value="'.$author_name.'">	
			<br /><br />
			<p><label for="authoremail"><b>'.$L_authoremail.'</b></label></p>
			<input name="authoremail" type="text" id="title" size="50" maxlength="255" value="'.$author_email.'">';




		include ("$absoluteurl"."components/xmlparser/loadparser.php");
		include ("$absoluteurl"."core/admin/readfeedlanguages.php");


		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;

		foreach($parser->document->language as $singlelanguage)
		{
			//echo $singlelanguage->id[0]->tagData."<br>";
			//echo $singlelanguage->description[0]->tagData;

			$arr[] .= $singlelanguage->description[0]->tagData;
			$arrid[] .= $singlelanguage->id[0]->tagData;
			$n++;
		}


		## FEED LANGUAGES LIST

		$PG_mainbody .= '<br /><br /><p><label for="feedlanguage"><b>'.$L_feedlang.'</b></label></p>
			<p><span class="admin_hints">'.$L_feedlanguagehint.'</span></p>
			';
		$PG_mainbody .= '<select name="feedlanguage">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {



			$PG_mainbody .= '
				<option value="' . $arrid[$key] . '"';

			if ($feed_language == $arrid[$key]) {
				$PG_mainbody .= ' selected';
			}

			$PG_mainbody .= '>' . $val . '</option>
				';	



		}
		$PG_mainbody .= '</select>';	


		$PG_mainbody .= '<br /><br /><p><label for="explicit"><b>'.$L_explicitpodcast.'</b></label></p>
			<span class="admin_hints">'.$L_yourpodcastexplicit.'</span>
			<p>'.$L_yes.' <input type="radio" name="explicit" value="yes" ';

		if ($explicit_podcast == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '.$L_no.' <input type="radio" name="explicit" value="no" ';

		if ($explicit_podcast == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '>&nbsp;&nbsp; '.$L_clean.'<input type="radio" name="explicit" value="clean" ';

		if ($explicit_podcast == "clean") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></p>';

		$PG_mainbody .= '<br /><p>
			<input type="submit" name="'.$L_send.'" value="'.$L_send.'" onClick="showNotify(\''.$L_setting.'\');"></p>';
	}

}

?>