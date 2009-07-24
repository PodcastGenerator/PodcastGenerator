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

### Check if user is logged ###
	if ($amilogged != "true") { exit; }
###

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="freebox" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$freeboxcontent = $_POST['long_description'];
	$freeboxcontent = stripslashes($freeboxcontent); //depurate

	$PG_mainbody .= '<h3>'.$L_admin_freebox.'</h3>';

	$fp1 = fopen("$absoluteurl"."freebox-content.txt", "w+"); //Apri il file in lettura e svuotalo (w+)
	fclose($fp1);

	$fp = fopen("$absoluteurl"."freebox-content.txt", "a+"); //testa xml
	fwrite($fp, "$freeboxcontent"); 
	fclose($fp);

	$PG_mainbody .= "$L_freeboxupdated";

}

else {


	$PG_mainbody .= '<h3>'.$L_admin_freebox.'</h3>';

	if(file_exists("$absoluteurl"."freebox-content.txt")){

		$freeboxcontenttodisplay = file_get_contents("$absoluteurl"."freebox-content.txt");
		} else { $freeboxcontenttodisplay = NULL; }

		$PG_mainbody .= '
			<form action="?p=admin&amp;do=freebox&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform" onsubmit="return submitForm();">

			<span class ="admin_hints">'.$L_htmlaccepted.'</span><br /><br />

			<textarea id="long_description" name="long_description" cols="50" rows="3">'.$freeboxcontenttodisplay.'</textarea>

			<br /></div>

			<input type="submit" value="'.$L_send.'" onClick="showNotify(\''.$L_setting.'\');">
			<br /><br /><br /><br />

			</form>

			';


	} // end else . if GET variable "c" is not = "ok"

	?>