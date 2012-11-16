<?php 
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

## Below I include English translation by default (as I write new versions of Podcast Generator in English, I suppose this language and the associated variables will be always up to date)

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['scriptlang'])) { exit; } 
########### End
// I depurate scriptlang above

/*
if (file_exists("language/en.php") AND $scriptlang != "en") {
	
	
	include ("language/en.php");

}	


##Now, I include the language file specified in $scriptlang var in config.php.

if (!file_exists("language/$scriptlang.php")) { //if language file doesn't exist

echo "<p class=\"error\">The language you specified in the config file is not available (default = en)</p><br />";
echo "Please correct <i>scriptlang</i> var in config.php";
exit;

}

else { // if language file exist

	include ("language/$scriptlang.php"); //include it and use the variables in the whole script

}



*/

//INITIALIZE PHPGETTEXT LIB - https://launchpad.net/php-gettext/
// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/components/locale'); //dir containing locales
define('DEFAULT_LOCALE', 'en_EN');

require_once('components/php-gettext/gettext.inc');

$supported_locales = array('en_US', 'sr_CS', 'de_CH');
$encoding = 'UTF-8';

$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'messages';
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);




/*
################################
#### GESTIONE LINGUE CON GETTEXT
$locale = "en_EN";
if (isset($scriptlang) AND $scriptlang == "en") $locale = "en_EN";
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./components/locale");
textdomain("messages");

#### FINE - GESTIONE LINGUE CON GETTEXT
*/
?>