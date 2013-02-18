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
//Get the XML document loaded into a variable (The xml parser must be previously included)
$xml = file_get_contents("$filedescr");
//Set up the parser object
$parser = new XMLParser($xml);
*/

//Parse the XML file with podcast data...
//$parser->Parse();

//to handle CDATA values see here: http://blog.evandavey.com/2008/04/how-to-fix-simplexml-cdata-problem-in-php.html
$parser = simplexml_load_file("$filedescr",'SimpleXMLElement',LIBXML_NOCDATA);

//var_dump($parser); //Debug

//Parse the episode in the xml file - just one episode (array [0]) in the file
$text_title = $parser->episode[0]->titlePG[0];
$text_shortdesc = $parser->episode[0]->shortdescPG[0];
$text_longdesc = $parser->episode[0]->longdescPG[0];
$text_imgpg = $parser->episode[0]->imgPG[0];
$text_keywordspg = $parser->episode[0]->keywordsPG[0];
$text_explicitpg = $parser->episode[0]->explicitPG[0];
$text_authornamepg = $parser->episode[0]->authorPG[0]->namePG[0];
$text_authoremailpg = $parser->episode[0]->authorPG[0]->emailPG[0];

//categories
$text_category1 = $parser->episode[0]->categoriesPG[0]->category1PG[0];
$text_category2 = $parser->episode[0]->categoriesPG[0]->category2PG[0];
$text_category3 = $parser->episode[0]->categoriesPG[0]->category3PG[0];



?>