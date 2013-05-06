<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue

	##############
	#show server information
	# convert max upload size set in config.php in megabytes
	$max_upload_form_size_MB = $max_upload_form_size/1048576;
	$max_upload_form_size_MB = round($max_upload_form_size_MB, 2);


	$PG_mainbody .= '
		<div>
		<h3>'._("Your server configuration").'</h3>';


	if (php_uname('s')!= NULL) { $PG_mainbody .= '<p>'._("Operating System:").' '.php_uname('s'); }

	$PG_mainbody .= '
		<p>'._("PHP Version:").' '.phpversion().'
		<br />
		<br />display_errors = ' . ini_get('display_errors').'';


	//IF GETTEXT EXTENSION INSTALLED IN THE SERVER OR NOT
	//note that $gettextInstalled is created in language.php
	if ($gettextInstalled == 0) $PG_mainbody .= '<br />'._("GETTEXT extension: not installed (php-gettext will be used)");
	else $PG_mainbody .= '<br />'._("GETTEXT extension: installed");


	if (ini_get('register_globals')!= NULL) { //if value not null
		$PG_mainbody .= '<br />register_globals = ' . ini_get('register_globals').'';
	} else {
		$PG_mainbody .= '<br />register_globals = OFF '._("(this is good)");
	}

	$PG_mainbody .= '<br />
		<br />upload_max_filesize (php.ini) = ' . ini_get('upload_max_filesize') . '
		<br />post_max_size (php.ini) = ' . ini_get('post_max_size') . '<br />';

	if (ini_get('memory_limit')!= NULL) { //if value not null
		$PG_mainbody .= 'memory_limit (php.ini) = ' . ini_get('memory_limit') . '<br />';
	}

	$PG_mainbody .= '
		<br />'._("Max upload size set in the script (config.php):").' '.$max_upload_form_size_MB.'M</p>';

	########### Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...

		$showmin = min($max_upload_form_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
		// Note: if I add +0 it eliminates the "M" (e.g. 8M,9M) and this solves some issues with the "min" function
		#############################

		if ($showmin!=NULL and $showmin!="0") { 
			$PG_mainbody .= '<p><b>'._("Your server configuration allows you to upload files up to").' '.$showmin._("MB").'</b></p>';
		}


		$PG_mainbody .= '</div>';


	}
	?>