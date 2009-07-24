<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//Get the XML document loaded into a variable (The xml parser must be previously included)
$xml = file_get_contents("$filedescr");
//Set up the parser object
$parser = new XMLParser($xml);

//Parse the XML file with podcast data...
$parser->Parse();

//Parse the episode in the xml file - just one episode (array [0]) in the file
$text_title = $parser->document->episode[0]->titlepg[0]->tagData;
$text_shortdesc = $parser->document->episode[0]->shortdescpg[0]->tagData;
$text_longdesc = $parser->document->episode[0]->longdescpg[0]->tagData;
$text_imgpg = $parser->document->episode[0]->imgpg[0]->tagData;
$text_keywordspg = $parser->document->episode[0]->keywordspg[0]->tagData;
$text_explicitpg = $parser->document->episode[0]->explicitpg[0]->tagData;
$text_authornamepg = $parser->document->episode[0]->authorpg[0]->namepg[0]->tagData;
$text_authoremailpg = $parser->document->episode[0]->authorpg[0]->emailpg[0]->tagData;

//categories
$text_category1 = $parser->document->episode[0]->categoriespg[0]->category1pg[0]->tagData;
$text_category2 = $parser->document->episode[0]->categoriespg[0]->category2pg[0]->tagData;
$text_category3 = $parser->document->episode[0]->categoriespg[0]->category3pg[0]->tagData;


?>