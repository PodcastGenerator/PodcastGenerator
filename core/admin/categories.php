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

if ($categoriesenabled == "yes") { /////// if categories are enabled in config.php


	if (isset($_GET['do']) AND $_GET['do']=="categories" AND isset($_GET['action']) AND $_GET['action']=="add") { // if add a category

		include ("$absoluteurl"."core/admin/categories_add.php");
	}

	elseif (isset($_GET['do']) AND $_GET['do']=="categories" AND isset($_GET['action']) AND $_GET['action']=="del") { // if remove a category

		include ("$absoluteurl"."core/admin/categories_remove.php");
	}

	else { //001 (If no add or remove display main categories page)

		$PG_mainbody .= "<h3>_("Add")del_categories</h3>";
		$PG_mainbody .= '<span class="admin_hints">'."._("Hint: Don't you need to classify your podcast into categories? Too complicated? ").".' <a href="?p=admin&do=config#setcategoriesfeature">'."._("Simply disable them").".'</a></span>';

		include ("$absoluteurl"."components/xmlparser/loadparser.php");
		include ("$absoluteurl"."core/admin/readXMLcategories.php");

		if (file_exists("$absoluteurl"."categories.xml") AND isset($parser->document->category)) {


			######
			$PG_mainbody .= '
				<form action="?p=admin&amp;do=categories&amp;action=add" method="POST" enctype="multipart/form-data" name="categoryform" id="categoryform" onsubmit="return submitForm();">

				<br /><br />
				<label for="addcategory"><b>'._("Add")newcat.'</b></label><br />
				<input name="addcategory" id="addcategory" type="text" size="50" maxlength="255" ><br />

				<input type="submit" value="'._("Add").'" onClick="showNotify(\''._("Add")ing.'\');">
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



			$PG_mainbody .= "<br /><br /><p><b>"._("Delete Categories")."</b> ($n)</p>";
			$PG_mainbody .= "<ul>";


			natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

			foreach ($arr as $key => $val) {
				//$PG_mainbody .= "cat[" . $key . "] = " . $val . "<br>";

				$PG_mainbody .= '<li>' . $val . ' ';



				$PG_mainbody .= '<a href="javascript:Effect.toggle(\''.$arrid[$key].'\',\'appear\');">['."._("Delete").".']</a></li>';


				$PG_mainbody .= '<div id="'.$arrid[$key].'" style="display:none"><b>'."._("Do you really want to permanently delete this category?").".'</b><p>'."._("Yes").".' <input type="radio" name="'."._("Delete").".' '.$val.'" value="yes" onClick="showNotify(\''."._("Deleting...").".'\');location.href=\'?p=admin&do=categories&action=del&cat='.$arrid[$key].'\';"> &nbsp;&nbsp; '."._("No").".' <input type="radio" name="'."._("No").".'" value="no" onClick="javascript:Effect.toggle(\''.$arrid[$key].'\',\'appear\');"></p>

					</div>';







			}
			$PG_mainbody .= '</ul>';

		} //if xml categories file doesn't exist
		else
		{
			$PG_mainbody .= '<p><b>'."._("Categories file doesn't exist or empty...").".'</b></p>';

			$PG_mainbody .= '
				<form action="?p=admin&amp;do=categories&amp;action=add" method="POST" enctype="multipart/form-data" name="categoryform" id="categoryform" onsubmit="return submitForm();">

				<br /><br />
				<label for="addcategory"><b>'._("Add")newcat.'</b></label><br />
				<input name="addcategory" id="addcategory" type="text" size="50" maxlength="255" ><br />

				<input type="submit" value="'._("Add").'" onClick="showNotify(\''._("Add")ing.'\');">
				';
		}

	} //001

} /////// end if categories enabled
else {
	$PG_mainbody .= "._("Categories disabled").";
}
?>