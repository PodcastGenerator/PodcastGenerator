<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('checkconfigexistence.php');
?>


	<ul class="episode_imgdesc">
	<li>

	<?php

$PG_mainbody = NULL; //define
$PG_mainbody = '<form method="post" action="index.php?step=5">	
	';

$PG_mainbody .= '<p><b>'.$SL_enteruserandpwd.'</b></p>
	<label for="username">'.$SL_username.'</label><br />

	<input name="username" id="username" type="text" size="20" maxlength="20" value=""><br /><br /><br />

	<label for="password">'.$SL_pwd.'</label><br />

	<input type="password" id="password" name="password" size="20" maxlength="20"><br />

	<label for="password_confirm">'.$SL_pwdconfirm.'</label><br />
	<input type="password" id="password_confirm" name="password_confirm" size="20" maxlength="20"><br /><br />

	';


$PG_mainbody .= '
	<input type="hidden" name="setuplanguage" value="'.$_POST['setuplanguage'].'">
	<input type="submit" value="'.$SL_next.'">
	</form>';

//print output

echo $PG_mainbody;

?>


	</li>
	</ul>