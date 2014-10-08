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
	if (!isUserLogged()) { exit; }
###

if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="freebox" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	$freeboxcontent = $_POST['long_description'];
	$freeboxcontent = stripslashes($freeboxcontent); //depurate

	$PG_mainbody .= '<h3>'._("FreeBox").'</h3>';

	$fp1 = fopen("$absoluteurl"."freebox-content.txt", "w+"); //Apri il file in lettura e svuotalo (w+)
	fclose($fp1);

	$fp = fopen("$absoluteurl"."freebox-content.txt", "a+"); //testa xml
	fwrite($fp, "$freeboxcontent"); 
	fclose($fp);

	$PG_mainbody .= ""._("Your freebox has been updated!")."";

}

else {


	$PG_mainbody .= '<h3>'._("FreeBox").'</h3>';

	if(file_exists("$absoluteurl"."freebox-content.txt")){

		$freeboxcontenttodisplay = file_get_contents("$absoluteurl"."freebox-content.txt");
		} else { $freeboxcontenttodisplay = NULL; }

		
		
		$PG_mainbody .= '
		
		<span class ="alert">'._("(HTML tags accepted)").'</span><br /><br />
		
			<form action="?p=admin&amp;do=freebox&amp;c=ok" method="POST" enctype="application/x-www-form-urlencoded" name="freeboxform" id="freeboxform">

			<textarea id="long_description" name="long_description" cols="50" rows="10">'.$freeboxcontenttodisplay.'</textarea>

		
			
<br /><br />

			
			
			<input type="submit" value="'._("Send").'" onClick="showNotify(\''._("Setting...").'\');">
		

			</form>
				

			';


	} // end else . if GET variable "c" is not = "ok"

	?>