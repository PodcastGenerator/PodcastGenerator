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
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['scriptlang'])) { exit; } 
########### End


//INITIALIZE PHPGETTEXT LIB - https://launchpad.net/php-gettext/
// define constants

//define('PROJECT_DIR', realpath('./'));
//define('LOCALE_DIR', PROJECT_DIR .'/components/locale'); //dir containing locales


define('LOCALE_DIR', $absoluteurl .'/components/locale'); //dir containing locales


if (isset($scriptlang)) {
//define('DEFAULT_LOCALE', $scriptlang); 
$locale = $scriptlang;
}
else {
//define('DEFAULT_LOCALE', 'en_EN');
$locale = "en_EN";
}


require_once($absoluteurl.'components/php-gettext/gettext.inc');

//$supported_locales = array('en_EN', 'it_IT', 'de_CH');
$encoding = 'UTF-8';

//$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup

// TO DEBUG  T_setlocale in /core/language.php doesn't work on IIS8, PHP5.5alpha4 when php_gettext.dll is enabled in php.ini -> switching to setlocale doesn't generate errors but doesn't change language either
T_setlocale(LC_MESSAGES, $locale);


// Set the text domain as 'messages'
$domain = 'messages';
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);


?>