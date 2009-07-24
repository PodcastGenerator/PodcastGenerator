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

include ('checkconfigexistence.php');
?>




	<ul class="episode_imgdesc">
	<li>

	<?php

$PG_mainbody = NULL; //define

include ("$absoluteurl"."components/xmlparser/loadparser.php");
include ("$absoluteurl"."setup/readsetuplanguages.php");


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


## SCRIPT LANGUAGES LIST

$PG_mainbody .= '<br /><br />

	<form method="post" action="index.php?step=2">

	<p><label for="setuplanguage"><b>Select Language</b></label></p>
	';
$PG_mainbody .= '<select name="setuplanguage">';


natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive


$browserlanguage = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2); // ASSIGN BROWSER LANGUAGE into a VARIABLE

foreach ($arr as $key => $val) {



	$PG_mainbody .= '
		<option value="' . $arrid[$key] . '"';

	// PRE select the language in the form checking the browser language

	if (isset($browserlanguage) AND  $browserlanguage == $arrid[$key]) {
		$PG_mainbody .= ' selected';
	}

	$PG_mainbody .= '>' . $val . '</option>
		';	

}
$PG_mainbody .= '</select>
	<br /><br />
	<input type="submit" value="'.$SL_next.'">
	</form>
	<br /><br />
	';


//print output

echo $PG_mainbody;

?>


	</li>
	</ul>