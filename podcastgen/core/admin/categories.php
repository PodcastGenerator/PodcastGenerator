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

if ($categoriesenabled == "yes") { /////// if categories are enabled in config.php


	if (isset($_GET['do']) AND $_GET['do']=="categories" AND isset($_GET['action']) AND $_GET['action']=="add") { // if add a category

		include ("$absoluteurl"."core/admin/categories_add.php");
	}

	elseif (isset($_GET['do']) AND $_GET['do']=="categories" AND isset($_GET['action']) AND $_GET['action']=="del") { // if remove a category

		include ("$absoluteurl"."core/admin/categories_remove.php");
	}

	else { //001 (If no add or remove display main categories page)

		$PG_mainbody .= "<h3>$L_adddel_categories</h3>";
		$PG_mainbody .= '<span class="admin_hints">'.$L_cathintdisable.' <a href="?p=admin&do=config#setcategoriesfeature">'.$L_cathintsimplydisable.'</a></span>';

		include ("$absoluteurl"."components/xmlparser/loadparser.php");
		include ("$absoluteurl"."core/admin/readXMLcategories.php");

		if (file_exists("$absoluteurl"."categories.xml") AND isset($parser->document->category)) {


			######
			$PG_mainbody .= '
				<form action="?p=admin&amp;do=categories&amp;action=add" method="POST" enctype="multipart/form-data" name="categoryform" id="categoryform" onsubmit="return submitForm();">

				<br /><br />
				<label for="addcategory"><b>'.$L_addnewcat.'</b></label><br />
				<input name="addcategory" id="addcategory" type="text" size="50" maxlength="255" ><br />

				<input type="submit" value="'.$L_add.'" onClick="showNotify(\''.$L_adding.'\');">
				';
			#####


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



			$PG_mainbody .= "<br /><br /><p><b>$L_del_categories</b> ($n)</p>";
			$PG_mainbody .= "<ul>";


			natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

			foreach ($arr as $key => $val) {
				//$PG_mainbody .= "cat[" . $key . "] = " . $val . "<br>";

				$PG_mainbody .= '<li>' . $val . ' ';



				$PG_mainbody .= '<a href="javascript:Effect.toggle(\''.$arrid[$key].'\',\'appear\');">['.$L_delete.']</a></li>';


				$PG_mainbody .= '<div id="'.$arrid[$key].'" style="display:none"><b>'.$L_catdeleteconfirmation.'</b><p>'.$L_yes.' <input type="radio" name="'.$L_delete.' '.$val.'" value="yes" onClick="showNotify(\''.$L_deleting.'\');location.href=\'?p=admin&do=categories&action=del&cat='.$arrid[$key].'\';"> &nbsp;&nbsp; '.$L_no.' <input type="radio" name="'.$L_no.'" value="no" onClick="javascript:Effect.toggle(\''.$arrid[$key].'\',\'appear\');"></p>

					</div>';







			}
			$PG_mainbody .= '</ul>';

		} //if xml categories file doesn't exist
		else
		{
			$PG_mainbody .= '<p><b>'.$L_catfileerror.'</b></p>';

			$PG_mainbody .= '
				<form action="?p=admin&amp;do=categories&amp;action=add" method="POST" enctype="multipart/form-data" name="categoryform" id="categoryform" onsubmit="return submitForm();">

				<br /><br />
				<label for="addcategory"><b>'.$L_addnewcat.'</b></label><br />
				<input name="addcategory" id="addcategory" type="text" size="50" maxlength="255" ><br />

				<input type="submit" value="'.$L_add.'" onClick="showNotify(\''.$L_adding.'\');">
				';
		}

	} //001

} /////// end if categories enabled
else {
	$PG_mainbody .= $L_categoriesdisabled;
}
?>