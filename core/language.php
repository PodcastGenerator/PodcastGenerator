<?php 
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

/*
 NOTE: podcastgen uses gettext to handle locale and translations
 English is the main language and will work always. However, when using translations:
 if the gettext extension is not installed in the server then a php lib is used.
 if installed, then podcastgen will use native gettext. 
 Tested under various linux distro and mac os (mamp)
 It WON'T probably work flawlessly with a windows server.
 This is due to different way windows has to handle locales (not easy to solve, not reliable): http://php.net/manual/en/function.setlocale.php
 Well, I hope there are not so many people out there using windows as a web server!!!
 If you are just using WAMP to test podcastgenerator, and you need localization in a language
 different than english, then you can go to mamp menu -> PHP -> PHP extensions 
 and disable php_gettext. It will work.
*/

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['scriptlang'])) { exit; } 
########### End


//FIRST CHECK IF GETTEXT IS INSTALLED AS A PHP EXTENSION IN THE SERVER (Otherwise php-gettext lib will be installed)
//the var $gettextInstalled is used to show the server info (for future debugs)
if (function_exists("gettext")) $gettextInstalled = 1; //1 = extension installed
else $gettextInstalled = 0;




if (!defined('LOCALE_DIR')) define('LOCALE_DIR', $absoluteurl .'/components/locale'); //dir containing locales - define just if not already defined



if (isset($scriptlang)) {
//define('DEFAULT_LOCALE', $scriptlang); 
$locale = $scriptlang;
}
elseif (!isset($scriptlang) AND isset($_POST['setuplanguage'])) { //if setup
$locale = $_POST['setuplanguage'];
}
else {
//define('DEFAULT_LOCALE', 'en_EN');
$locale = "en_EN";
}


//Encoding! UT8 recommended!
$encoding = 'UTF-8';

// Set the text domain as 'messages'
$domain = 'messages';


if ($gettextInstalled == 0) {
//WE USE PHPGETTEXT LIB - https://launchpad.net/php-gettext/
require_once($absoluteurl.'components/php-gettext/gettext.inc');

//$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup

// TO DEBUG  T_setlocale in /core/language.php doesn't work on IIS8, PHP5.5alpha4 when php_gettext.dll is enabled in php.ini -> switching to setlocale doesn't generate errors but doesn't change language either

T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);  //encoding
T_textdomain($domain);
}

else { //IF GETTEXT EXTENSION INSTALLED

if (!ini_get('safe_mode')) putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain($domain, LOCALE_DIR);
bind_textdomain_codeset($domain, $encoding); //encoding
textdomain($domain);
	
}


?>