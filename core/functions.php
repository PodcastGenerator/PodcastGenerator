<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

$defined=true;

## Support for multiple file types (e.g. mp3, ogg, mpg, avi) at the same time.

function checkFileType ($filetype,$podcast_filetypes,$filemimetypes) {
	$i=0;
	$bool=false;
	$fileData = array();

	while (($i < sizeof($podcast_filetypes)) && $bool==false) {
		if ($filetype==$podcast_filetypes[$i]) {
			$fileData[0]=$podcast_filetypes[$i];
			$fileData[1]=$filemimetypes[$i];
			$bool=true;
		}
		$i+=1;
	}
	return $fileData;
}
// Thanks to Francesco D'Offizi (francesco.doffizi@email.it) for implementing the checkFileType function.


########

## Rename uploaded file - STRICT

function renamefilestrict ($filetorename) { // strict rename policy (just characters from a to z and numbers... no accents and other characters). This kind of renaming can have problems with some languages (e.g. oriental)

	$filetorename = preg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($filetorename))));

	return $filetorename;

}


########

## Rename uploaded file - LESS STRICT

function renamefile ($filetorename) { // normal file rename policy

	$filetorename = strtolower($filetorename); // lower-case.
	$filetorename = strip_tags($filetorename); // remove HTML tags.
	$filetorename = preg_replace('!\s+!','_',$filetorename); // change space chars to underscores.
	$filetorename = stripslashes($filetorename); //remove slashes in the file name
	$filetorename = str_replace("'", "", $filetorename);
	$filetorename = str_replace("&", "_and_", $filetorename);

	return $filetorename;

}

########

## Validate e-mail address

function validate_email ($address) { //validate email address
	return (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $address));
}

########

## Depurate feed Content from non accepted characters (according also to iTunes specifications)

function depurateContent($content) {
	$content = stripslashes($content);				
	$content = str_replace('<', '&lt;', $content);
	$content = str_replace('>', '&gt;', $content);
	$content = str_replace('& ', '&amp; ', $content);
	$content = str_replace('’', '&apos;', $content);
	$content = str_replace('"', '&quot;', $content);
	$content = str_replace('©', '&#xA9;', $content);
	$content = str_replace('&copy;', '&#xA9;', $content);
	$content = str_replace('℗', '&#x2117;', $content);
	$content = str_replace('™', '&#x2122;', $content);
	return $content;
}




############ Create form date and time
	function CreaFormData($inName, $useDate=0, $dateformat) //inName is the form name, it can be null
	{ 
	// array with months
	$monthName = array(1=> _("January"), _("February"), _("March"),
	_("April"), _("May"), _("June"), _("July"), _("August"),
	_("September"), _("October"), _("November"), _("December"));

	// se data non specificata, o invalida, usa timestamp corrente
	if($useDate == NULL)
	{
	$useDate = Time();
	}
	
	$outputform =  _("Date:")." "; //title
	
	//day
	$outputformDAY =  "<select name=\"" . $inName . "Day\">\n";
	for($currentDay=1; $currentDay <= 31; $currentDay++)
	{
	$outputformDAY .=  "<option value=\"$currentDay\"";
	if(intval(date( "d", $useDate))==$currentDay)
	{
	$outputformDAY .=  " selected";
	}
	$outputformDAY .=  ">$currentDay\n";
	}
	$outputformDAY .=  "</select>";

	//mese
	$outputformMONTH =  "<select name=\"" . $inName . "Month\">\n";
	for($currentMonth = 1; $currentMonth <= 12; $currentMonth++)
	{
	$outputformMONTH .=  "<option value=\"";
	$outputformMONTH .=  intval($currentMonth);
	$outputformMONTH .=  "\"";
	if(intval(date( "m", $useDate))==$currentMonth)
	{
	$outputformMONTH .=  " selected";
	}
	$outputformMONTH .=  ">" . $monthName[$currentMonth] . "\n";
	}
	$outputformMONTH .=  "</select>";

	//anno
	$outputformYEAR =  "<select name=\"" . $inName . "Year\">\n";
	$startYear = date( "Y", $useDate);
	for($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++)
	{
	$outputformYEAR .=  "<option value=\"$currentYear\"";
	if(date( "Y", $useDate)==$currentYear)
	{
	$outputformYEAR .=  " selected";
	}
	$outputformYEAR .=  ">$currentYear\n";
	}
	$outputformYEAR .=  "</select>";
	
	
	if ($dateformat == "m-d-Y") {
	$outputform.= $outputformMONTH.$outputformDAY.$outputformYEAR;}
	elseif ($dateformat == "Y-m-d") {
	$outputform.= $outputformYEAR.$outputformMONTH.$outputformDAY;}
	else { $outputform.= $outputformDAY.$outputformMONTH.$outputformYEAR; }
	
	
	$outputform .=  "&nbsp;&nbsp;"; //two blank spaces
	$outputform .=  _("Time:")." "; //titoletto


    //ore
	$outputform .=  "<select name=\"" . $inName . "Hour\">\n";
	for($currentHour = 0; $currentHour <= 23; $currentHour++)
	{
	$outputform .=  "<option value=\"";
	$outputform .=  intval($currentHour);
	$outputform .=  "\"";
	if(intval(date( "G", $useDate))==$currentHour)
	{
	$outputform .=  " selected";
	}
	$outputform .=  ">" . $currentHour. "\n";
	}
	$outputform .=  "</select>";
	
	//minuti
	$outputform .=  "<select name=\"" . $inName . "Minute\">\n";
	for($currentMinute = 0; $currentMinute <= 59; $currentMinute++)
	{
	$outputform .=  "<option value=\"";
	
	if ($currentMinute <= 9) {
	$outputform .=  "0".intval($currentMinute); } //add 0 before number from 1 to 9
	else { $outputform .=  intval($currentMinute); }
	
	$outputform .=  "\"";
	if(intval(date( "i", $useDate))==$currentMinute)
	{
	$outputform .=  " selected";
	}
	
	if ($currentMinute <= 9) {
	$outputform .=  ">0".intval($currentMinute). "\n"; } //aggiungi zero ai minuti da 1 a 9
	else { $outputform .=  ">".intval($currentMinute). "\n"; }
	
	}
	$outputform .=  "</select>";

	
	return $outputform;

} // End - form date and time




#################### SOCIAL NETWORK INTEGRATION

//$fullURL,$text_title are episode data. the rest: value 1 (/ TRUE) enable a certain social network, value 0 disables
function displaySocialNetworkButtons($fullURL,$text_title,$fb,$tw,$gp) { 
	
$construct_output = '<br />'; //space above

//FB Like Button
if ($fb == TRUE) {
$construct_output .= '
<iframe src="//www.facebook.com/plugins/like.php?href='.$fullURL.'&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=361488987252256" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px;" allowTransparency="true"></iframe>
';
}

//TWITTER Button
if ($tw == TRUE) {
$construct_output.= '
<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$fullURL.'" data-text="'.$text_title.'" data-hashtags="podcastgen">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
';
}

//G+ Button
if ($gp == TRUE) {
$construct_output .= '
<div class="g-plusone" data-size="medium" data-href="'.$fullURL.'"></div>

<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
';
}
	
	return $construct_output;
}




?>