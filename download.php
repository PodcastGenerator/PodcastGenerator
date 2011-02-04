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

# FORCE DOWNLOAD OF SUPPORTED FILES (doesn't play in the browser, but forces download)

include("config.php"); 

include("$absoluteurl"."core/functions.php");

include("$absoluteurl"."core/supported_media.php");

//include("$absoluteurl"."core/language.php"); //We don't need language here

$filename = $_GET['filename'];

$filename = str_replace("/", "", $filename); // Replace / in the filename.. avoid downloading of file outside podcastgenerator root directory

$filename_path = "$absoluteurl"."$upload_dir$filename"; // absolute path of the filename to download



if (file_exists("$filename_path") ) { // check real existence of the file. Avoid possible cross-site scripting attacks


	$file_media = explode(".",$filename); //divide filename from extension

	$fileData = checkFileType($file_media[1],$podcast_filetypes,$filemimetypes);

	if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
		$podcast_filetype=$fileData[0]; $filemimetype=$fileData[1];


		if ($file_media[1]=="$podcast_filetype" AND $file_media[1]!=NULL) {// SECURITY OPTION: if extension is supported (file to download must have a known episode extension)


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
}
else {
	echo "<p>File doesn't exist or Variable not correct. Cannot Download.</p><p>No cross-site scripting allowed with Podcast Generator :-P</p>";	
}

?>