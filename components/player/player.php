<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

//HTML 5 to add (tested with Chrome and Safari)


if (detectModernBrowser() == TRUE) { 
$showplayercode =	'<audio controls>
	  <source src="'.$url.$upload_dir.$filenameWithouExtension.'.mp3" type="audio/mpeg">
	'._("Your browser does not support the audio player").'
	</audio>';
}
// if browser does not (or it is not know to) support HTML5 video and audio, then show mp3 flash
else { //if no HTML5 support

$showplayercode = "<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
	codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" WIDTH=\"290\" HEIGHT=\"24\" id=\"player";

$showplayercode .= "\" ALIGN=\"\">

	<PARAM NAME=movie VALUE=\"components/player/player.swf?FlashVars=&amp;soundFile=$url$upload_dir$filenameWithouExtension.mp3&amp;bg=0xf8f8f8&amp;leftbg=0xeeeeee&amp;lefticon=0x666666&amp;rightbg=0xcccccc&amp;rightbghover=0x999999&amp;righticon=0x666666&amp;righticonhover=0xffffff&amp;text=0x666666&amp;slider=0x666666&amp;track=16777215&amp;border=0x666666&amp;loader=0xffffcc&amp;\">

	<PARAM NAME=quality VALUE=high>

	<PARAM NAME=bgcolor VALUE=#FFFFFF>

<param name=\"wmode\" value=\"transparent\">

	<EMBED src=\"components/player/player.swf?FlashVars=&amp;soundFile=$url$upload_dir$filenameWithouExtension.mp3&amp;bg=0xf8f8f8&amp;leftbg=0xeeeeee&amp;lefticon=0x666666&amp;rightbg=0xcccccc&amp;rightbghover=0x999999&amp;righticon=0x666666&amp;righticonhover=0xffffff&amp;text=0x666666&amp;slider=0x666666&amp;track=16777215&amp;border=0x666666&amp;loader=0xffffcc&amp;\" quality=high bgcolor=#FFFFFF wmode=\"transparent\" WIDTH=\"290\" HEIGHT=\"24\" NAME=\"Streaming\" ALIGN=\"\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED>

</OBJECT>


";


} //END if no HTML5 support

?> 