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

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="embed-code" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$embeddedcontent = $_POST['embedded-code'];
	$embeddedcontent = stripslashes($embeddedcontent); //depurate

	$PG_mainbody .= '<h3>'._("Embed code with episodes").'</h3>';

	$fp1 = fopen("$absoluteurl"."embedded-code.txt", "w+"); //Apri il file in lettura e svuotalo (w+)
	fclose($fp1);

	$fp = fopen("$absoluteurl"."embedded-code.txt", "a+"); //testa xml
	fwrite($fp, "$embeddedcontent"); 
	fclose($fp);

	$PG_mainbody .= _("The code has been embedded.");

}

else {


	$PG_mainbody .= '<h3>'._("Embed code with episodes").'</h3>';

	if(file_exists("$absoluteurl"."embedded-code.txt")){

		$embedcodetodisplay = file_get_contents("$absoluteurl"."embedded-code.txt");
		} else { $embedcodetodisplay = NULL; }

		$PG_mainbody .= '
			<form action="?p=admin&amp;do=embed-code&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform" onsubmit="return submitForm();">

			<span class ="admin_hints">'._("You can insert below a code with want to embed with each episode. Optionally use the special string").' __THISEPISODEURL__ '._("to obtain the URL of the single episode.").'</span><br /><br />

			<textarea id="embedded-code" name="embedded-code" cols="50" rows="10">'.$embedcodetodisplay.'</textarea>

			<br />
	
			
			</div>

			<input type="submit" value="'._("Send").'" onClick="showNotify(\''._("Setting...").'\');">
			<br /><br /><br /><br />

			</form>

			';


	} // end else . if GET variable "c" is not = "ok"

	?>