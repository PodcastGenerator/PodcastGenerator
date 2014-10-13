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

//// FORCE DOWNLOAD OF SUPPORTED FILES (e.g. files don't play in the browser, even when a plugin is installed)
//NB. does not work with some mobile browsers

include("config.php");
include($absoluteurl."core/functions.php");


$filename = $_GET['filename'];

//Clean variable, avoid downloading of file outside podcast generator root directory.
$filename = str_replace("/", "", $filename); // Replace / in the filename 
$filename = str_replace("\\", "", $filename); // Replace \ in the filename

$filename_path = $absoluteurl.$upload_dir.$filename; // absolute path of the filename to download

if (file_exists($filename_path) ) {
	
	$file_media = divideFilenameFromExtension($filename);
	
	$fileData = checkFileType($file_media[1],$absoluteurl);

	$podcast_filetype=$fileData[0];
	$filemimetype=$fileData[1];
	$isFileSupported = $fileData[2];

	// SECURITY OPTION: if extension is supported (file to download must have a known episode extension)
	if ($isFileSupported == TRUE AND $file_media[1]==$podcast_filetype AND !publishInFuture($filename_path)) {

	//// Headers
		### required by internet explorer
		if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');
		###

		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers 
		header("Content-Type: $filemimetype");
		header("Content-Disposition: attachment; filename=".basename($filename_path).";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename_path));
		readfile("$filename_path");
		exit();	
	}
}

////else do nothing - no feedback
//else { }
?>