<?php

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

//XML parser library (I do not use SimpleXML or DOM for PHP5 in order to keep supporting PHP4)

if (version_compare(phpversion(), "5.0.0", ">=")) {
  // if server is using PHP version 5.0.0 or later
  include_once("$absoluteurl"."components/xmlparser/parser_php5.php"); //include XML parser for PHP 5

//echo "PHP5";
 
} else {
  include_once("$absoluteurl"."components/xmlparser/parser_php4.php"); //include XML parser for PHP 4
//echo "PHP4"; 
 
 } 


?>