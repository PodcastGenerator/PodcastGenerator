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
if (isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### Check if user is logged ###
	if ($amilogged != "true") { exit; }
###

// check if user is already logged in
if(isset($amilogged) AND $amilogged =="true") {

	$PG_mainbody .= '<h3>'.$L_itunescategories.'</h3>
		<span class="admin_hints">'.$L_changecat.'</span>';

	if (isset($_GET['action']) AND $_GET['action']=="change") { // if action is set


		if (isset($_POST['category1'])) { //cat1
			$itunes_category[0] = $_POST['category1'];
		}

		if (isset($_POST['category2'])) { //cat2
			$itunes_category[1] = $_POST['category2'];
		}

		if (isset($_POST['category3'])) { //cat3
			$itunes_category[2] = $_POST['category3'];
		}

		include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

		$PG_mainbody .= '<br /><br /><p>'.$L_itunescatchanged.'</p>';

		//REGENERATE FEED ...
		include ("$absoluteurl"."core/admin/feedgenerate.php");

	}
	else { // if action not set

		include ("$absoluteurl"."components/xmlparser/loadparser.php");
		include ("$absoluteurl"."core/admin/readitunescategories.php");


		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;

		foreach($parser->document->category as $singlecategory)
		{
			//echo $singlecategory->id[0]->tagData."<br>";
			//echo $singlecategory->description[0]->tagData;

			$arr[] .= $singlecategory->description[0]->tagData;
			$arrid[] .= $singlecategory->id[0]->tagData;
			$n++;
		}

		$PG_mainbody .=	'<form name="'.$L_itunescategories.'" method="POST" enctype="multipart/form-data" action="?p=admin&do=itunescat&action=change">';


		## CATEGORY 1

		$PG_mainbody .= "<br /><br /><p><b>$L_itunes_cat1</b></p>";
		$PG_mainbody .= '<select name="category1">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {

			if ( $val != "" ) { //just for 1st category - cannot be empty

				$PG_mainbody .= '
					<option value="' . $val . '"';

				if ($itunes_category[0] == $val) {
					$PG_mainbody .= ' selected';
				}

				$PG_mainbody .= '>' . $val . '</option>
					';	

			}

		}
		$PG_mainbody .= '</select>';	



		## CATEGORY 2

		$PG_mainbody .= "<br /><br /><p><b>$L_itunes_cat2</b></p>";
		$PG_mainbody .= '<select name="category2">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {

			$PG_mainbody .= '
				<option value="' . $val . '"';

			if ($itunes_category[1] == $val) {
				$PG_mainbody .= ' selected';
			}

			$PG_mainbody .= '>' . $val . '</option>
				';	



		}
		$PG_mainbody .= '</select>';


		## CATEGORY 3

		$PG_mainbody .= "<br /><br /><p><b>$L_itunes_cat3</b></p>";
		$PG_mainbody .= '<select name="category3">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {

			$PG_mainbody .= '
				<option value="' . $val . '"';

			if ($itunes_category[2] == $val) {
				$PG_mainbody .= ' selected';
			}

			$PG_mainbody .= '>' . $val . '</option>
				';	

		}
		$PG_mainbody .= '</select>';


		$PG_mainbody .= '<p>
			<input type="submit" name="'.$L_send.'" value="'.$L_send.'" onClick="showNotify(\''.$L_setting.'\');"></p>';
	}

}

?>