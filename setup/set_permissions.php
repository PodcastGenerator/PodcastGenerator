<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################


$testfile = "test.txt";

### define directories
$media_directory = "../media/";
$images_directory = "../images/";
$script_directory = "../";

## include language file
//if($scriptlang!=NULL) {
	//include("lang/setup_$scriptlang.php");
	//} else {
		//include("lang/setup_en.php");
		//}


		if (file_exists("../config.php")) { //if config.php already exists stop the script

			echo "<font color=\"red\">$SL_configexists</font><br />$SL_configdelete";

			exit;

		} 



		###############
		############### try to set writing permissions
		$PG_mainbody .= "<b>$SL_checkperm</b><br />";

		## checking media dir
		$fp = fopen("$media_directory$testfile",'a'); //create test file
		$content = "test";
		fwrite($fp,$content);
		fclose($fp);

		if (file_exists("$media_directory$testfile")) {

			$PG_mainbody .= "<p><font color=\"green\">$SL_mediadir $SL_iswritable</font></p>";
			unlink ("$media_directory$testfile");
			$dir1 = "ok";
		}
		else {
			$PG_mainbody .= "<p><font color=\"red\"><b>$SL_mediadir ".$media_directory." $SL_notwritable</b></font></p>";
			$dir1 = "NO";
		}


		## checking images dir
		$fp1 = fopen("$images_directory$testfile",'a'); //create test file
		$content1 = "test";
		fwrite($fp1,$content1);
		fclose($fp1);

		if (file_exists("$images_directory$testfile")) {

			$PG_mainbody .= "<p><font color=\"green\">$SL_imgdir $SL_iswritable</font></p>";
			unlink ("$images_directory$testfile");
			$dir2 = "ok";
		}
		else {
			$PG_mainbody .= "<p><font color=\"red\"><b>$SL_imgdir ".$images_directory." $SL_notwritable</b></font></p>";
			$dir2 = "NO";
		}


		## checking script root dir
		$fp2 = fopen("$script_directory$testfile",'a'); //create test file
		$content2 = "test";
		fwrite($fp2,$content2);
		fclose($fp2);

		if (file_exists("$script_directory$testfile")) {

			$PG_mainbody .=  "<p><font color=\"green\">$SL_scriptdir $SL_iswritable</font></p>";
			unlink ("$script_directory$testfile");
			$dir3 = "ok";
		}
		else {
			$PG_mainbody .=  "<p><font color=\"red\"><b>$SL_scriptdir ".$script_directory." $SL_notwritable</b></font></p>";
			$dir3 = "NO";
		}


		if (isset($dir1) AND $dir1=="ok" AND isset($dir2) AND $dir2=="ok" AND isset($dir3) AND $dir3=="ok") { // OK CAN PROCEED

			$PG_mainbody .= "<br /><p><b>$SL_permok</b></p>";
			$PG_mainbody .=  "<p>$SL_canproceed</p>";

			$PG_mainbody .= '
				<form method="post" action="index.php?step=4">
				<br />
				<input type="hidden" name="setuplanguage" value="'.$_POST['setuplanguage'].'">
				<input type="submit" value="'.$SL_next.'">
				</form>
				';

		} else {

			$PG_mainbody .=  "<br /><br /><font color=\"red\"><b>$SL_trytochmod</b></font>";

			if (isset($dir1) AND $dir1!="ok") {
				$PG_mainbody .=  "<br />$SL_settingchmod $media_directory ($SL_mediadir)";
				chmod("$media_directory", 0777);
			}
			if (isset($dir1) AND $dir2!="ok") {
				$PG_mainbody .=  "<br />$SL_settingchmod to $images_directory ($SL_imgdir)";
				chmod("$images_directory", 0777);
			}
			if (isset($dir1) AND $dir3!="ok") {
				$PG_mainbody .=  "<br />$SL_settingchmod $script_directory ($SL_scriptdir)";
				chmod("$script_directory", 0777);
			}

			$PG_mainbody .=  "<br /><p><b>$SL_permtried</b></p>";

			// reload button
			$PG_mainbody .= '
				<form method="post" action="index.php?step=3">
				<br />
				<input type="hidden" name="setuplanguage" value="'.$_POST['setuplanguage'].'">
				<input type="submit" value="'.$SL_reload1.'">
				</form>
				';

			$PG_mainbody .=  "<p>$SL_reload2</p>";
			$PG_mainbody .=  "<p>$SL_setman</p><br />";


		}



		#######
		####### end set permission
		?>